<?php

declare(strict_types=1);

namespace Model\Cashbook\Handlers;

use Codeception\Test\Unit;
use Mockery as m;
use Model\Cashbook\Cashbook;
use Model\Cashbook\Commands\Cashbook\CreateCashbook;
use Model\Cashbook\Handlers\Cashbook\CreateCashbookHandler;
use Model\Cashbook\Repositories\ICashbookRepository;

class CreateCashbookHandlerTest extends Unit
{
    public function test() : void
    {
        $type = Cashbook\CashbookType::get(Cashbook\CashbookType::CAMP);

        $repository = m::mock(ICashbookRepository::class);
        $repository->shouldReceive('save')
            ->once()
            ->withArgs(function (Cashbook $cashbook) use ($type) {
                return $cashbook->getId()->equals(Cashbook\CashbookId::fromInt(10))
                    && $cashbook->getType() === $type;
            });

        $handler = new CreateCashbookHandler($repository);

        $handler->handle(new CreateCashbook(Cashbook\CashbookId::fromInt(10), $type));

        m::close();
    }
}
