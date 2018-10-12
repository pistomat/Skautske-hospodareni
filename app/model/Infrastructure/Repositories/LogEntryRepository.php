<?php

declare(strict_types=1);

namespace Model\Infrastructure\Repositories\Logger;

use Doctrine\ORM\EntityManager;
use Model\Logger\Log\Type;
use Model\Logger\LogEntry;
use Model\Logger\Repositories\ILogEntryRepository;

class LogEntryRepository implements ILogEntryRepository
{
    /** @var EntityManager */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return LogEntry[]
     */
    public function findAllByTypeId(Type $type, int $typeId) : array
    {
        $result = $this->em->createQueryBuilder()
            ->select('l')
            ->from(LogEntry::class, 'l')
            ->where('l.typeId = :typeId')
            ->andWhere('l.type = :type')
            ->orderBy('l.date', 'DESC')
            ->setParameter('typeId', $typeId)
            ->setParameter('type', $type)
            ->getQuery()->getResult();
        return $result;
    }

    public function save(LogEntry $log) : void
    {
        $this->em->persist($log);
        $this->em->flush();
    }
}