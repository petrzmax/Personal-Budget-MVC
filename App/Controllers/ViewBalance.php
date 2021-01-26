<?php

namespace App\Controllers;

use \Core\View;
use \App\Flash;
use \App\Models\Balance;

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

        View::renderTemplate('ViewBalance/index.html', [
            'activeTimePeriod' => $balance->getActiveTimePeriod(),
            'sumOfIncomeInCategories' => $balance->getSumOfIncomeInCategories(),
            'sumOfExpenseInCategories' => $balance->getSumOfExpenseInCategories()
        ]);
        
    }
}
