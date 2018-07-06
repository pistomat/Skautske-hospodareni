<?php

declare(strict_types=1);

namespace Model\Cashbook\Handlers\Cashbook;

use Model\Cashbook\CashbookNotFoundException;
use Model\Cashbook\CategoryNotFoundException;
use Model\Cashbook\Commands\Cashbook\AddChitToCashbook;
use Model\Cashbook\Repositories\CategoryRepository;
use Model\Cashbook\Repositories\ICashbookRepository;

final class AddChitToCashbookHandler
{
    /** @var ICashbookRepository */
    private $cashbooks;

    /** @var CategoryRepository */
    private $categories;

    public function __construct(ICashbookRepository $cashbooks, CategoryRepository $categories)
    {
        $this->cashbooks  = $cashbooks;
        $this->categories = $categories;
    }

    /**
     * @throws CashbookNotFoundException
     * @throws CategoryNotFoundException
     */
    public function handle(AddChitToCashbook $command) : void
    {
        $cashbook = $this->cashbooks->find($command->getCashbookId());
        $category = $this->categories->find($command->getCategoryId(), $cashbook->getId(), $cashbook->getType());

        $cashbook->addChit(
            $command->getNumber(),
            $command->getDate(),
            $command->getRecipient(),
            $command->getAmount(),
            $command->getPurpose(),
            $category
        );

        $this->cashbooks->save($cashbook);
    }
}
