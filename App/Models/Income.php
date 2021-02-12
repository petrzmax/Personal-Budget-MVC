<?php

namespace App\Models;

/**
 * Incomes model
 *
 * PHP version 7.0
 */
class Income extends Finance
{
    static $financeCategoryAsignedToUserTableName = 'incomes_category_assigned_to_users';
    protected $financeTableName = 'incomes'; 
}