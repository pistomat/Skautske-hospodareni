<?php

namespace App\AccountancyModule\EventModule\Commands;

class BaseChit
{
    /** @var int */
    protected $unitId;

    /** @var int */
    protected $userId;

    /** @var string */
    protected $userName;

    /** @var int */
    protected $chitId;

    /** @var int */
    protected $localId;

    public function __construct(int $unitId, int $userId, string $userName, int $chitId, int $localId)
    {
        $this->unitId = $unitId;
        $this->userId = $userId;
        $this->userName = $userName;
        $this->chitId = $chitId;
        $this->localId = $localId;
    }

    public function getUnitId(): int
    {
        return $this->unitId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function getChitId(): array
    {
        return $this->chitId;
    }

    public function getLocalId(): int
    {
        return $this->localId;
    }
}
