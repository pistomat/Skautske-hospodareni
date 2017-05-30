<?php

namespace Model\Travel\Repositories;

use Doctrine\ORM\EntityManager;
use Model\Travel\Contract;
use Model\Travel\ContractNotFoundException;

final class ContractRepository implements IContractRepository
{

    /** @var EntityManager */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function find(int $id): Contract
    {
        $contract = $this->em->find(Contract::class, $id);

        if($contract === NULL) {
            throw new ContractNotFoundException("Contract with id #$id not found");
        }

        return $contract;
    }

}