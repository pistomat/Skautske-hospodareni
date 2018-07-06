<?php

declare(strict_types=1);

namespace Model\Unit\Services;

use Model\Payment\IUnitResolver;
use Model\Unit\Repositories\IUnitRepository;
use Model\Unit\UnitHasNoParentException;

final class UnitResolver implements IUnitResolver
{
    /** @var IUnitRepository */
    private $units;

    public function __construct(IUnitRepository $units)
    {
        $this->units = $units;
    }

    /**
     * @throws UnitHasNoParentException
     */
    public function getOfficialUnitId(int $unitId) : int
    {
        $unit = $this->units->find($unitId);

        if ($unit->isOfficial()) {
            return $unitId;
        }
        if ($unit->getParentId() === null) {
            throw new UnitHasNoParentException('Unit ' . $unit->getId() . " doesn't have set parentID");
        }

        return $this->getOfficialUnitId($unit->getParentId());
    }
}
