<?php

declare(strict_types=1);

namespace Model\Event\Handlers\Event;

use Model\Event\Commands\Event\UpdateEvent;
use Model\Event\Repositories\IEventRepository;
use SebastianBergmann\Diff\Diff;

final class UpdateEventHandler
{

    /** @var IEventRepository */
    private $events;

    public function __construct(IEventRepository $events)
    {
        $this->events = $events;
    }

    public function handle(UpdateEvent $command): void
    {
        $event = $this->events->find($command->getEventId()->getValue());

        $event->update(
            $command->getName(),
            $command->getLocation() ?? ' ',
            $command->getStartDate(),
            $command->getEndDate(),
            $command->getScopeId(),
            $command->getTypeId()
        );

        $this->events->update($event);
    }

}