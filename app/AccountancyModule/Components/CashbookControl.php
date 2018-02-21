<?php

declare(strict_types=1);

namespace App\AccountancyModule\Components;

use App\AccountancyModule\Components\Cashbook\ChitListControl;
use App\AccountancyModule\Factories\Cashbook\IChitListControlFactory;
use App\AccountancyModule\Factories\IChitFormFactory;

class CashbookControl extends BaseControl
{

    /** @var int */
    private $cashbookId;

    /** @var bool */
    private $isEditable;

    /** @var IChitFormFactory */
    private $formFactory;

    /** @var IChitListControlFactory */
    private $chitListFactory;

    public function __construct(int $cashbookId, bool $isEditable, IChitFormFactory $formFactory, IChitListControlFactory $chitListFactory)
    {
        parent::__construct();
        $this->cashbookId = $cashbookId;
        $this->isEditable = $isEditable;
        $this->formFactory = $formFactory;
        $this->chitListFactory = $chitListFactory;
    }

    public function render(): void
    {
        $this->template->setParameters([
            'isEditable' => $this->isEditable,
        ]);

        $this->template->setFile(__DIR__ . '/templates/CashbookControl.latte');
        $this->template->render();
    }

    protected function createComponentChitForm(string $name): ChitForm
    {
        $control = $this->formFactory->create($this->cashbookId, $this->isEditable);

        $this->addComponent($control, $name); // necessary for JSelect

        return $control;
    }

    protected function createComponentChitList(): ChitListControl
    {
        return $this->chitListFactory->create($this->cashbookId, $this->isEditable);
    }

}
