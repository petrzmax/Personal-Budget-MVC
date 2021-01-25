<?php

namespace App\Models;

use PDO;

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
     * @return mixed Incomes object if found, false otherwise
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
     * Save the income model with the current property values
     *
     * @return boolean  True if the income was saved, false otherwise
     */
    public function save()
    {
        //validate()
        $db = static::getDB();
        $sql = 'INSERT INTO incomes
                VALUES (NULL, :user_id, :income_category_assigned_to_user_id, :incomeValue, :incomeDate, :comment)';
        $stmt = $db->prepare($sql);
        
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':income_category_assigned_to_user_id', $this->categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':incomeValue', $this->valueInput, PDO::PARAM_STR);
        $stmt->bindValue(':incomeDate', $this->dateInput, PDO::PARAM_STR);
        $stmt->bindValue(':comment', $this->comment, PDO::PARAM_STR);
        $stmt->execute();

    }

    /**
     * Validate current property values, adding valiation error messages to the errors array property
     *
     * @return void
     */
    public function validate()
    {

        //Validate income value
        if(!is_numeric($this->valueInput)) {
            $this->errors[] = "Kwota musi być wartością numeryczną"; 
        }

        //Validate category
        if(!isset($this->categoryId)) {
            $this->errors[] = "Kategoria musi być wybrana"; 
        }

        //Validate comment length
        if(isset($this->comment)) {
            if(strlen($this->comment) > 100) {
                $this->errors[] = "Komentarz może mieć maksymalnie 100 znaków";
            }
        }

        //Validate date

        // Password
        if (isset($this->password)) {

            if (strlen($this->password) < 6) {
                $this->errors[] = 'Please enter at least 6 characters for the password';
            }

            if (preg_match('/.*[a-z]+.*/i', $this->password) == 0) {
                $this->errors[] = 'Password needs at least one letter';
            }

            if (preg_match('/.*\d+.*/i', $this->password) == 0) {
                $this->errors[] = 'Password needs at least one number';
            }

        }
    }
}
