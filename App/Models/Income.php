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
            $this->errors[] = "Kwota musi być podana"; 
            
            //Check if value is numeric
        } else if(!is_numeric($this->valueInput)) {
            $this->errors[] = "Kwota musi być wartością numeryczną"; 

            //Check if value is greater than 0
        } else  if($this->valueInput <= 0) {
            $this->errors[] = "Kwota musi być większa od 0";
        }

        //Check if category is set
        if(isset($this->categoryId)) {
            //Check if given cattegory is associated with current user
            if(!in_array($this->categoryId, static::getCategoriesIds())) {
                $this->errors[] = "Wybierz prawidłową kategorię"; 
            }
        } else {
            $this->errors[] = "Kategoria musi być wybrana"; 
        }

        //If comment exist
        if($this->comment != '') {
            //Validate comment length
            if(strlen($this->comment) > 100) {
                $this->errors[] = "Komentarz może mieć maksymalnie 100 znaków";
            }
        }

        //Validate date
        if(!$this->validateDate($this->dateInput)) {
            $this->errors[] = "Podana data jest nieprawidłowa";
        }

    }
}
