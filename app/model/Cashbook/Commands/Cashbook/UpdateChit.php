<?php

namespace Model\Cashbook\Commands\Cashbook;

use Cake\Chronos\Date;
use Model\Cashbook\Cashbook\Amount;
use Model\Cashbook\Cashbook\ChitNumber;
use Model\Cashbook\Cashbook\Recipient;
use Model\Cashbook\Handlers\Cashbook\UpdateChitHandler;

/**
 * @see UpdateChitHandler
 */
final class UpdateChit
{

    /** @var int */
    private $cashbookId;

    /** @var int */
    private $chitId;

    /** @var ChitNumber|NULL */
    private $number;

    /** @var Date */
    private $date;

    /** @var Recipient|NULL */
    private $recipient;

    /** @var Amount */
    private $amount;

    /** @var string */
    private $purpose;

    /** @var int */
    private $categoryId;

    public function __construct(int $cashbookId, int $chitId, $number, Date $date, $recipient, Amount $amount, string $purpose, int $categoryId)
    {
        $this->cashbookId = $cashbookId;
        $this->chitId = $chitId;
        $this->number = $number;
        $this->date = $date;
        $this->recipient = $recipient;
        $this->amount = $amount;
        $this->purpose = $purpose;
        $this->categoryId = $categoryId;
    }

    public function getCashbookId(): int
    {
        return $this->cashbookId;
    }

    public function getChitId(): int
    {
        return $this->chitId;
    }

    public function getNumber(): ?ChitNumber
    {
        return $this->number;
    }

    public function getDate(): Date
    {
        return $this->date;
    }

    public function getRecipient(): ?Recipient
    {
        return $this->recipient;
    }

    public function getAmount(): Amount
    {
        return $this->amount;
    }

    public function getPurpose(): string
    {
        return $this->purpose;
    }

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

}