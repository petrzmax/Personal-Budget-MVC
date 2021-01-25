<?php

namespace App\Controllers;

use \Core\View;
use \App\Flash;
use \App\Models\Expense;

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
            Flash::addMessage('Wydatek dodany pomyślnie');
            $this->redirect('/add-expense');
            
        } else {
            //Show errors
            View::renderTemplate('Expense/new.html', [
                'categories' => Expense::getCategories(),
                'methods' => Expense::getMethods(),
                'expense' => $expense
            ]);
        }
    }
}
