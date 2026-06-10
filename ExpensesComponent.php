<?php

namespace Apps\Tms\Components\Tools\Expenses;

use Apps\Tms\Packages\Adminltetags\Traits\DynamicTable;
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
            $this->view->expenseTypes = $this->expensesPackage->getExpenseTypes();

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

        $replaceColumns =
            function ($dataArr) {
                if ($dataArr && is_array($dataArr) && count($dataArr) > 0) {
                    foreach ($dataArr as &$data) {
                        if ($data['type'] == '1') {
                            $data['type'] = 'Advance (' . $data['type'] . ')';
                        } else if ($data['type'] == '2') {
                            $data['type'] = 'Reimburse (' . $data['type'] . ')';
                        }
                    }
                }

                return $dataArr;
            };

        $this->generateDTContent(
            $this->expensesPackage,
            'tools/expenses/view',
            null,
            ['name', 'type'],
            true,
            ['name', 'type'],
            $controlActions,
            ['type' => 'type (id)'],
            $replaceColumns,
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

        $this->expensesPackage->addExpense($this->postData());

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

        $this->expensesPackage->updateExpense($this->postData());

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

        $this->expensesPackage->removeExpense($this->postData());

        $this->addResponse(
            $this->expensesPackage->packagesData->responseMessage,
            $this->expensesPackage->packagesData->responseCode
        );
    }
}