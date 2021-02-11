<?php

namespace App\Models;

use PDO;
use \App\Messages;

/**
 * Expense model
 *
 * PHP version 7.0
 */
class Expense extends \Core\Model
{

    /**
     * Error messages
     *
     * @var array
     */
    public $errors = [];

    /**
     * Class constructor
     *
     * @param array $data  Initial property values (optional)
     *
     * @return void
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

    /**
     * Get all the payment methods
     *
     * @return mixed Expense object if found, false otherwise
     */
    public static function getMethods()
    {
        $sql = 'SELECT id, name
                FROM payment_methods_assigned_to_users  
                WHERE user_id = :user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetchAll();
    } 

    /**
     * Get the payment method data by id
     *
     * @return mixed Expense object if found, false otherwise
     */
    public static function getMethodById($id)
    {
        $sql = 'SELECT id, name, expense_limit
                FROM payment_methods_assigned_to_users 
                WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    } 

    /**
     * Get all the expense payment method id's
     *
     * @return mixed id array if found, false otherwise
     */
    private function getMethodsIds()
    {
        $sql = 'SELECT id
                FROM payment_methods_assigned_to_users  
                WHERE user_id = :user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_COLUMN, 'id');
    } 

    /**
     * Get all the expense categories
     *
     * @return mixed Expense object if found, false otherwise
     */
    public static function getCategories()
    {
        $sql = 'SELECT id, name
                FROM expenses_category_assigned_to_users  
                WHERE user_id = :user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetchAll();
    } 

    /**
     * Get expense data by id
     *
     * @return mixed Expense object if found, false otherwise
     */
    public static function getCategoryById($id)
    {
        $sql = 'SELECT id, name, expense_limit
                FROM expenses_category_assigned_to_users 
                WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    } 
    
    /**
     * Get all the expense categories id's
     *
     * @return mixed id array if found, false otherwise
     */
    private function getCategoriesIds()
    {
        $sql = 'SELECT id
                FROM expenses_category_assigned_to_users  
                WHERE user_id = :user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_COLUMN, 'id');
    } 
    
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
     * Validate given date
     *
     * @return boolean  True if the date is correct, false otherwise
     */
    private function validateDate($date, $format = 'Y-m-d')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    /**
     * Validate current property values, adding valiation error messages to the errors array property
     *
     * @return void
     */
    public function validate()
    {

        //Check if value is set
        if($this->valueInput == '') {
            $this->errors[] = Messages::VALUE_REQUIRED; 
            
            //Check if value is numeric
        } else if(!is_numeric($this->valueInput)) {
            $this->errors[] = Messages::VALUE_IS_NOT_NUMERIC; 

            //Check if value is greater than 0
        } else  if($this->valueInput <= 0) {
            $this->errors[] = Messages::VALUE_MUST_BE_GREATER_THAN_0;
        }

        //Check if payment method is set
        if(isset($this->methodId)) {
            //Check if given payment method is associated with current user
            if(!in_array($this->methodId, static::getMethodsIds())) {
                $this->errors[] = Messages::METHOD_INVALID; 
            }
        } else {
            $this->errors[] = Messages::METHOD_REQUIRED; 
        }

        //Check if category is set
        if(isset($this->categoryId)) {
            //Check if given category is associated with current user
            if(!in_array($this->categoryId, static::getCategoriesIds())) {
                $this->errors[] = Messages::CATEGORY_INVALID; 
            }
        } else {
            $this->errors[] = Messages::CATEGORY_REQUIRED; 
        }

        //If comment exist
        if($this->comment != '') {
            //Validate comment length
            if(strlen($this->comment) > 100) {
                $this->errors[] = Messages::COMMENT_TOO_LONG;
            }
        }

        //Validate date
        if(!$this->validateDate($this->dateInput)) {
            $this->errors[] = Messages::DATE_INVALID;
        }

    }
}
