<?php

namespace App\Controllers;

use \Core\View;
use \App\Flash;
use \App\Models\Balance;
use \App\Messages;

/**
 * ViewBalance controller
 *
 * PHP version 7.0
 */
class ViewBalance extends Authenticated
{

    /**
     * Show the view balance page
     *
     * @return void
     */
    public function indexAction()
    {
        $balance = new Balance($_GET);

        if(!$balance->prepare()) {
            Flash::AddMessage(Messages::BALANCE_BAD_DATA, Flash::ERROR);
            $this->redirect('/view-balance');
        }

        View::renderTemplate('ViewBalance/index.html');
    }

    /**
     * AJAX
     *
     * @return void
     */
    public function getSumOfFinanceInCategoriesAction()
    {
        $balance = new Balance($_POST);

        if(!$balance->prepare()) {
            Flash::AddMessage(Messages::BALANCE_BAD_DATA, Flash::ERROR);
            $this->redirect('/view-balance');
        }

        header('Content-Type: application/json');
        if($_POST['postFinanceType'] == 'income') {
            echo json_encode($balance->getSumOfIncomeInCategories());
        } else if($_POST['postFinanceType'] == 'expense'){
            echo json_encode($balance->getSumOfExpenseInCategories());
        }
                
    }
}
