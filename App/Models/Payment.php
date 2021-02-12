<?php

namespace App\Models;

/**
 * Payment model
 *
 * PHP version 7.0
 */
class Payment extends Finance
{
    static $financeCategoryAsignedToUserTableName = 'payment_methods_assigned_to_users';
    protected $financeTableName = null; 
}