<?php

declare(strict_types=1);

namespace Model;

use Assert\Assert;
use Model\Bank\Fio\Transaction as BankTransaction;
use Model\Payment\Fio\IFioClient;
use Model\Payment\Group;
use Model\Payment\Payment;
use Model\Payment\Payment\Transaction;
use Model\Payment\Repositories\IBankAccountRepository;
use Model\Payment\Repositories\IGroupRepository;
use Model\Payment\Repositories\IPaymentRepository;
use Model\Utils\Arrays;
use function array_filter;
use function array_map;
use function count;
use function min;
use function sprintf;

class BankService
{
    /** @var IFioClient */
    private $bank;

    /** @var IGroupRepository */
    private $groups;

    /** @var IPaymentRepository */
    private $payments;

    /** @var IBankAccountRepository */
    private $bankAccounts;

    public const DAYS_BACK_DEFAULT = 60;

    public function __construct(
        IGroupRepository $groups,
        IFioClient $bank,
        IPaymentRepository $payments,
        IBankAccountRepository $bankAccounts
    ) {
        $this->groups       = $groups;
        $this->bank         = $bank;
        $this->payments     = $payments;
        $this->bankAccounts = $bankAccounts;
    }


    /**
     * Completes payments from info on bank account(s)
     *
     * @param  int[] $groupIds
     * @return int number of paired payments
     * @throws BankTimeLimit
     * @throws BankTimeout
     * @throws Payment\BankAccountNotFound
     * @throws Payment\TokenNotSet
     */
    public function pairAllGroups(array $groupIds, ?int $daysBack = null) : int
    {
        Assert::thatAll($groupIds)->integer();
        Assert::that($daysBack)->nullOr()->min(1);

        /** @var Group[][] $groupsByAccount */
        $foundGroups     = $this->groups->findByIds($groupIds);
        $groupsByAccount = Arrays::groupBy(
            $foundGroups,
            function (Group $g) {
                return $g->getBankAccountId();
            },
            true
        );

        $now         = new \DateTimeImmutable();
        $pairedCount = 0;

        foreach ($groupsByAccount as $bankAccountId => $groups) {
            $bankAccount = $this->bankAccounts->find($bankAccountId);

            if ($bankAccount->getToken() === null) {
                continue;
            }

            $payments = $this->payments->findByMultipleGroups($groupIds);
            $payments = array_filter(
                $payments,
                function (Payment $p) {
                    return $p->canBePaired();
                }
            );

            if (empty($payments)) {
                continue;
            }

            $pairSince = $daysBack === null ? $this->resolveLastPairing($groups) : new \DateTimeImmutable(sprintf('- %d days', $daysBack));

            $transactions = $this->bank->getTransactions($pairSince, $now, $bankAccount);
            $paired       = $this->markPaymentsAsComplete($transactions, $payments);

            $this->payments->saveMany($paired);
            $pairedCount += count($paired);

            if ($daysBack !== null) {
                continue;
            }

            $this->updateLastPairing($groups, $now);
        }

        return $pairedCount;
    }

    /**
     * @param Group[] $groups
     */
    private function updateLastPairing(array $groups, \DateTimeImmutable $time) : void
    {
        foreach ($groups as $group) {
            $group->updateLastPairing($time);
            $this->groups->save($group);
        }
    }

    /**
     * @param Group[] $groups
     */
    private function resolveLastPairing(array $groups) : \DateTimeImmutable
    {
        $lastPairings = array_map(
            function (Group $g) {
                return $g->getLastPairing();
            },
            $groups
        );
        $lastPairings = array_filter($lastPairings);

        if (count($lastPairings) !== 0) {
            return min($lastPairings);
        }

        return new \DateTimeImmutable('- ' . self::DAYS_BACK_DEFAULT . ' days');
    }

    /**
     * @param BankTransaction[] $transactions
     * @param Payment[]         $payments
     * @return Payment[]
     */
    private function markPaymentsAsComplete(array $transactions, array $payments) : array
    {
        $paymentsByVS = Arrays::groupBy(
            $payments,
            function (Payment $p) {
                return $p->getVariableSymbol()->toInt();
            }
        );

        $transactions = array_filter(
            $transactions,
            function (BankTransaction $t) use ($paymentsByVS) {
                return $t->getVariableSymbol() !== null && isset($paymentsByVS[$t->getVariableSymbol()]);
            }
        );

        $paired = [];
        $now    = new \DateTimeImmutable();
        foreach ($transactions as $transaction) {
            foreach ($paymentsByVS[$transaction->getVariableSymbol()] as $offset => $payment) {
                /** @var Payment $payment */
                if ($payment->getAmount() !== $transaction->getAmount()) {
                    continue;
                }

                $payment->complete($now, Transaction::fromFioTransaction($transaction));
                $paired[] = $payment;
                unset($paymentsByVS[$transaction->getVariableSymbol()][$offset]);
            }
        }

        return $paired;
    }
}
