<?php

namespace App\Controllers;

use \Core\View;
use \App\Flash;
use \App\Models\Expense;
use \App\Models\Payment;
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
            'methods' => Payment::getCategories()
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
        $payment = new Payment($_POST);

        if($payment->validate() && $expense->save($payment->getMethodId())) {
            Flash::addMessage(Messages::EXPENSE_ADD_SUCCESS);
            $this->redirect('/add-expense');
            
        } else {
            //Show errors
            Flash::addMessage(Messages::EXPENSE_ADD_FAIL, Flash::WARNING);
            View::renderTemplate('Expense/new.html', [
                'categories' => Expense::getCategories(),
                'methods' => Payment::getCategories(),
                'expense' => $expense,
                'payment' => $payment
            ]);
        }
    }

    /**
     * AJAX - get category limit data
     *
     * @return void
     */
    public function getLimitDataAction()
    {
        if(isset($_POST['postCategoryId'])) {
            $categoryId = $_POST['postCategoryId'];
            $result = Expense::getCategoryById($categoryId);
        } else {
            $result = false;
        }
        
        header('Content-Type: application/json');
        echo json_encode($result);
    }

    /**
     * AJAX - get sum of expenses in category
     *
     * @return void
     */
    public function getCategoryCurrentMonthSumByIdAction()
    {
        if(isset($_POST['postCategoryId'])) {
            $categoryId = $_POST['postCategoryId'];
            $result = Expense::getCategoryCurrentMonthSumById($categoryId);
        } else {
            $result = false;
        }
        
        header('Content-Type: application/json');
        echo $result;
    }
}
