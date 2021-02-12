<?php

namespace App\Models;

use PDO;
use \App\Messages;

/**
 * Expense model
 *
 * PHP version 7.0
 */
class Expense extends Finance
{
    static $financeCategoryAsignedToUserTableName = 'expenses_category_assigned_to_users';
    protected $financeTableName = 'incomes'; 
    
    /**
     * Save the expense model with the current property values
     *
     * @return boolean  True if the expense was saved, false otherwise
     */
    public function save()
    {
        $this->validate();

        if(empty($this->errors)) {

            $db = static::getDB();
            $sql = 'INSERT INTO expenses
                    VALUES (NULL, :user_id, :expense_category_assigned_to_user_id, :payment_method_assigned_to_user_id, :expenseValue, :expenseDate, :comment)';
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':expense_category_assigned_to_user_id', $this->categoryId, PDO::PARAM_INT);
            $stmt->bindValue(':payment_method_assigned_to_user_id', $this->methodId, PDO::PARAM_INT);
            $stmt->bindValue(':expenseValue', $this->valueInput, PDO::PARAM_STR);
            $stmt->bindValue(':expenseDate', $this->dateInput, PDO::PARAM_STR);
            $stmt->bindValue(':comment', htmlspecialchars($this->comment), PDO::PARAM_STR);
            
            return $stmt->execute();
        }

        return false;
    }

    /**
     * Validate current property values, adding valiation error messages to the errors array property
     *
     * @return void
     */
    public function validate()
    {
        parent::validate();

        //Check if payment method is set
        if(isset($this->methodId)) {
            //Check if given payment method is associated with current user
            if(!in_array($this->methodId, static::getMethodsIds())) {
                $this->errors[] = Messages::METHOD_INVALID; 
            }
        } else {
            $this->errors[] = Messages::METHOD_REQUIRED; 
        }

    }
}
