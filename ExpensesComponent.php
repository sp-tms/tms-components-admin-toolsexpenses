<?php

namespace Apps\Tms\Components\Tools\Expenses;

use Apps\Tms\Packages\Tools\Expenses\ToolsExpenses;
use System\Base\BaseComponent;

class ExpensesComponent extends BaseComponent
{
    use DynamicTable;

    protected $expensesPackage;

    public function initialize()
    {
        $this->expensesPackage = $this->usePackage(ToolsExpenses::class);
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $expense = $this->expensesPackage->getById((int) $this->getData()['id']);

                if (!$expense) {
                    return $this->throwIdNotFound();
                }

                $this->view->expense = $expense;
            }

            $this->view->pick('expenses/view');

            return;
        }

        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'edit'      => 'tools/expenses'
                ]
            ];

        $this->generateDTContent(
            $this->expensesPackage,
            'tools/expenses/view',
            null,
            ['name'],
            true,
            ['name'],
            $controlActions,
            [],
            null,
            'name'
        );

        $this->view->pick('expenses/list');
    }

    /**
     * @acl(name=add)
     */
    public function addAction()
    {
        $this->requestIsPost();

        $this->expensesPackage->addUom($this->postData());

        $this->addResponse(
            $this->expensesPackage->packagesData->responseMessage,
            $this->expensesPackage->packagesData->responseCode
        );
    }

    /**
     * @acl(name=update)
     */
    public function updateAction()
    {
        $this->requestIsPost();

        $this->expensesPackage->updateUom($this->postData());

        $this->addResponse(
            $this->expensesPackage->packagesData->responseMessage,
            $this->expensesPackage->packagesData->responseCode
        );
    }

    /**
     * @acl(name=remove)
     */
    public function removeAction()
    {
        $this->requestIsPost();

        $this->expensesPackage->removeUom($this->postData());

        $this->addResponse(
            $this->expensesPackage->packagesData->responseMessage,
            $this->expensesPackage->packagesData->responseCode
        );
    }
}