<?php

namespace Model\Payment\BankAccount;


use BankAccountValidator\Czech;
use Model\Payment\InvalidBankAccountException;
use Model\Payment\InvalidBankAccountNumberException;

class AccountNumber
{

    /** @var string|NULL */
    private $prefix;

    /** @var string */
    private $number;

    /** @var string */
    private $bankCode;

    /**
     * @throws InvalidBankAccountNumberException
     */
    public function __construct(?string $prefix, string $number, string $bankCode)
    {
        $validator = new Czech();

        if(!$validator->validate([$prefix, $number, $bankCode])) {
            throw self::invalidNumber();
        }

        $this->prefix = $prefix === '' ? NULL : $prefix;
        $this->number = $number;
        $this->bankCode = $bankCode;
    }

    /**
     * @throws InvalidBankAccountException
     */
    public static function fromString(string $number): self
    {
        $parser = new Czech();
        $number = $parser->parseNumber($number);

        if($number[1] === NULL || $number[2] === NULL) {
            throw self::invalidNumber();
        }

        return new self(...$number);
    }

    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getBankCode(): string
    {
        return $this->bankCode;
    }

    public function __toString(): string
    {
        $withoutPrefix = $this->number . "/" . $this->bankCode;

        return $this->prefix !== NULL
            ? $this->prefix . "-" . $withoutPrefix
            : $withoutPrefix;
    }

    private static function invalidNumber(): InvalidBankAccountNumberException
    {
        return new InvalidBankAccountNumberException("Invalid bank account number");
    }

}