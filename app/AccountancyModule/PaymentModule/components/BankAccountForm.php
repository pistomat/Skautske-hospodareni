<?php

namespace App\AccountancyModule\PaymentModule\Factories;

use App\AccountancyModule\Components\BaseControl;
use App\Forms\BaseForm;
use Model\Payment\BankAccount\AccountNumber;
use Model\Payment\BankAccountService;
use Model\Payment\InvalidBankAccountNumberException;
use Nette\Utils\ArrayHash;

class BankAccountForm extends BaseControl
{

    /** @var int|NULL */
    private $id;

    /** @var BankAccountService */
    private $model;


    public function __construct(?int $id, BankAccountService $model)
    {
        parent::__construct();
        $this->id = $id;
        $this->model = $model;
    }


    protected function createComponentForm() : BaseForm
    {
        $form = new BaseForm();
        $form->addText('name', 'Název')
            ->setRequired('Musíte vyplnit název');
        $form->addText('prefix')
            ->setRequired(FALSE)
            ->addRule($form::INTEGER, 'Neplatné předčíslí')
            ->addRule($form::MAX_LENGTH, 'Maximální délka předčíslí je %d znaků', 6);
        $form->addText('number')
            ->setRequired('Musíte vyplnit číslo účtu')
            ->addRule($form::INTEGER, 'Neplatné číslo účtu')
            ->addRule($form::MAX_LENGTH, 'Maximální délka čísla účtu je %d znaků', 10);

        $form->addText('bankCode')
            ->setRequired('Musíte vyplnit kód banky')
            ->addRule($form::PATTERN, 'Kod banky musí být 4 číslice', '[0-9]{4}');

        $form->addText('token', 'Token pro párování plateb (FIO)');

        $form->addSubmit('send', 'Uložit');

        if($this->id !== NULL) {
            $account = $this->model->find($this->id);
            $form->setDefaults([
                'name' => $account->getName(),
                'prefix' => $account->getNumber()->getPrefix(),
                'number' => $account->getNumber()->getNumber(),
                'bankCode' => $account->getNumber()->getBankCode(),
                'token' => $account->getToken(),
            ]);
        }

        $form->onSuccess[] = function(BaseForm $form, ArrayHash $values) {
            $this->formSucceeded($form, $values);
        };

        return $form;
    }


    private function formSucceeded(BaseForm $form, ArrayHash $values) : void
    {
        try {
            if($this->id !== NULL) {
                $this->model->updateBankAccount(
                    $this->id,
                    $values->name,
                    new AccountNumber($values->prefix, $values->number, $values->bankCode),
                    $values->token
                );
            } else {
                $this->model->addBankAccount(
                    $this->getPresenter()->getUnitId(),
                    $values->name,
                    new AccountNumber($values->prefix, $values->number, $values->bankCode),
                    $values->token
                );

            }

            $this->presenter->flashMessage('Bankovní účet byl uložen');
            $this->presenter->redirect('BankAccounts:default');
        } catch (InvalidBankAccountNumberException $e) {
            $form->addError('Neplatné číslo účtu');
        }
    }


    public function render() : void
    {
        $this->template->setFile(__DIR__ . '/templates/BankAccountForm.latte');
        $this->template->render();
    }

}
