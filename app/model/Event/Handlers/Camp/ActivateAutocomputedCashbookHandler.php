<?php

namespace Model\Event\Handlers\Camp;

use Model\Event\Commands\Camp\ActivateAutocomputedCashbook;
use Skautis\Skautis;

class ActivateAutocomputedCashbookHandler
{

    /** @var Skautis */
    private $skautis;

    public function __construct(Skautis $skautis)
    {
        $this->skautis = $skautis;
    }

    public function handle(ActivateAutocomputedCashbook $command): void
    {
        $this->skautis->event->eventCampUpdateRealTotalCostBeforeEnd([
                "ID" => $command->getCampId(),
                "IsRealTotalCostAutoComputed" => 1,
            ], 'eventCamp'
        );
    }

}