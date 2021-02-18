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

    /**
     * Validate current property values, adding valiation error messages to the errors array property
     *
     * @return boolean True if validayion successful, false otherwise
     */
    public function validate()
    {
        //Check if payment method is set
        if(isset($this->methodId)) {
            //Check if given payment method is associated with current user
            if(!in_array($this->methodId, static::getCategoriesIds())) {
                $this->errors[] = Messages::METHOD_INVALID; 
            }
        } else {
            $this->errors[] = Messages::METHOD_REQUIRED; 
        }
        
        if(empty($this->errors)) {
            return true;
        }
        else {
            return false;
        }
    }
}