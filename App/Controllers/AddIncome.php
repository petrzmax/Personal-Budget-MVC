<?php

namespace App\Controllers;

use \Core\View;
use \App\Flash;
use \App\Models\Income;
use App\Messages;

/**
 * Income controller
 *
 * PHP version 7.0
 */
class AddIncome extends Authenticated
{

    /**
     * Show the income page
     *
     * @return void
     */
    public function newAction()
    {
        View::renderTemplate('Income/new.html', [
            'categories' => Income::getCategories()
        ]);
    }

    /**
     * Save income
     *
     * @return void
     */
    public function createAction()
    {
        $income = new Income($_POST);
        if($income->save()) {
            Flash::addMessage(Messages::INCOME_ADD_SUCCESS);
            $this->redirect('/add-income');
            
        } else {
            //Show errors
            Flash::addMessage(Messages::INCOME_ADD_FAIL, Flash::WARNING);
            View::renderTemplate('Income/new.html', [
                'income' => $income
            ]);
        }
       

    }
}
