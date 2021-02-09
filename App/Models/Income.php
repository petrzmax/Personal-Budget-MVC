<?php

namespace App\Models;

use PDO;
use \App\Messages;

/**
 * Incomes model
 *
 * PHP version 7.0
 */
class Income extends \Core\Model
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
     * Get all the income categories
     *
     * @return mixed Income object if found, false otherwise
     */
    public static function getCategories()
    {
        $sql = 'SELECT id, name
                FROM incomes_category_assigned_to_users 
                WHERE user_id = :user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetchAll();
    } 

    /**
     * Get income data by id
     *
     * @return mixed Income object if found, false otherwise
     */
    public static function getCategoryById($id)
    {
        $sql = 'SELECT id, name
                FROM incomes_category_assigned_to_users 
                WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    } 

    /**
     * Get all the income categories id's
     *
     * @return mixed id array if found, false otherwise
     */
    private function getCategoriesIds()
    {
        $sql = 'SELECT id
                FROM incomes_category_assigned_to_users 
                WHERE user_id = :user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_COLUMN, 'id');
    } 
    
    /**
     * Save the income model with the current property values
     *
     * @return boolean  True if the income was saved, false otherwise
     */
    public function save()
    {
        $this->validate();

        if(empty($this->errors)) {

            $db = static::getDB();
            $sql = 'INSERT INTO incomes
                    VALUES (NULL, :user_id, :income_category_assigned_to_user_id, :incomeValue, :incomeDate, :comment)';
            $stmt = $db->prepare($sql);
            
            $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':income_category_assigned_to_user_id', $this->categoryId, PDO::PARAM_INT);
            $stmt->bindValue(':incomeValue', $this->valueInput, PDO::PARAM_STR);
            $stmt->bindValue(':incomeDate', $this->dateInput, PDO::PARAM_STR);
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
