<?php

namespace App\Controllers;

use \Core\View;
use \App\Flash;
use \App\Models\Expense;
use \App\Messages;

/**
 * AddExpense controller
 *
 * PHP version 7.0
 */
class AddExpense extends Authenticated
{

    /**
     * Show the AddExpense page
     *
     * @return void
     */
    public function newAction()
    {
        View::renderTemplate('Expense/new.html', [
            'categories' => Expense::getCategories(),
            'methods' => Expense::getMethods()
        ]);
    }

    /**
     * Save Expense
     *
     * @return void
     */
    public function createAction()
    {
        $expense = new Expense($_POST);
        if($expense->save()) {
            Flash::addMessage(Messages::EXPENSE_ADD_SUCCESS);
            $this->redirect('/add-expense');
            
        } else {
            //Show errors
            Flash::addMessage(Messages::INCOME_ADD_FAIL, Flash::WARNING);
            View::renderTemplate('Expense/new.html', [
                'categories' => Expense::getCategories(),
                'methods' => Expense::getMethods(),
                'expense' => $expense
            ]);
        }
    }
}
