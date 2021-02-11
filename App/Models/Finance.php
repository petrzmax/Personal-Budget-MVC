<?php

namespace App\Models;

use PDO;
use \App\Messages;

/**
 * Finance abstract model
 *
 * PHP version 7.0
 */
abstract class Finance extends \Core\Model
{
    /**
     * Class config -You need to override it in child class!
     *
     * @var string
     */
    protected $financeCategoryAsignedToUserTableName;
    protected $financeTableName;

    /**
     * Error messages
     *
     * @var array
     */
    public $errors = [];

    /**
     * Class constructor
     *
     * @param array $data Initial property values (optional)
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
     * Get all the finance categories
     *
     * @return mixed Finance object if found, false otherwise
     */
    public static function getCategories()
    {
        $sql = 'SELECT id, name
                FROM :table_name 
                WHERE user_id = :user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':table_name', $financeCategoryAsignedToUserTableName, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetchAll();
    } 

    /**
     * Get finance data by id
     *
     * @return mixed Finance object if found, false otherwise
     */
    public static function getCategoryById($id)
    {
        $sql = 'SELECT id, name
                FROM :table_name 
                WHERE id = :id AND user_id = :user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':table_name', $financeCategoryAsignedToUserTableName, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    } 

    /**
     * Add finance category
     *
     * @return mixed inserted row id if category added, false otherwise
     */
    public static function addCategory($name)
    {
        //Validate name
        if(true) {

            $sql = 'INSERT INTO :table_name (name, user_id) 
                    VALUES (:name, :user_id)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':table_name', $financeCategoryAsignedToUserTableName, PDO::PARAM_STR);
            $stmt->bindValue(':name', htmlspecialchars($name), PDO::PARAM_STR);     
            $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);

            if($stmt->execute()) {
                return $db->lastInsertId();
            }
        }
        return false;
    } 

    /**
     * Delete finance category by id
     *
     * @return boolean true if category deleted, false otherwise
     */
    public static function deleteCategoryById($id)
    {
        $sql = 'DELETE
                FROM :table_name 
                WHERE id = :id AND user_id = :user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':table_name', $financeCategoryAsignedToUserTableName, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);     
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);

        return $stmt->execute();
    } 

    /**
     * Get all the finance categories id's
     *
     * @return mixed id array if found, false otherwise
     */
    private function getCategoriesIds()
    {
        $sql = 'SELECT id
                FROM :table_name 
                WHERE user_id = :user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':table_name', $financeCategoryAsignedToUserTableName, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_COLUMN, 'id');
    } 
    
    /**
     * Save the finance model with the current property values
     *
     * @return boolean True if the finance was saved, false otherwise
     */
    public function save()
    {
        $this->validate();

        if(empty($this->errors)) {

            $db = static::getDB();
            $sql = 'INSERT INTO :table_name
                    VALUES (NULL, :user_id, :finance_category_assigned_to_user_id, :financeValue, :financeDate, :comment)';
            $stmt = $db->prepare($sql);
            
            $stmt->bindValue(':table_name', $financeTableName, PDO::PARAM_STR);
            $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':finance_category_assigned_to_user_id', $this->categoryId, PDO::PARAM_INT);
            $stmt->bindValue(':financeValue', $this->valueInput, PDO::PARAM_STR);
            $stmt->bindValue(':financeDate', $this->dateInput, PDO::PARAM_STR);
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

    /**
     * Validate provided name adding valiation error messages to the errors array property
     *
     * # PROTOTYPE -
     * 
     * @return boolean True if the name is correct, false otherwise
     */
    public function validateName($name) {
        //Check if name exist
        if($name == '') {
            //$this->errors[] = Messages::CATEGORY_NAME_REQUIRED;
            return false;
        }

        //Validate category name length
        if(strlen($name) > 50) {
            //$this->errors[] = Messages::CATEGORY_NAME_TOO_LONG;
            return false;
        }

        //Check name for special characters
        if(preg_match('/[^a-ząćęłńóśźżĄĘŁŃÓŚŹŻ\s]+/i', $name)) {
            //$this->errors[] = Messages::CATEGORY_NAME_HAS_SPECIAL_CHAR;
            return false;
        }

        //Check if category already exists in Db

        return true;

    }
}