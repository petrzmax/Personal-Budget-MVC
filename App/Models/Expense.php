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
    protected $financeTableName = 'expenses'; 
    
    /**
     * Get expense data by id
     *
     * @return mixed Expense object if found, false otherwise
     */
    public static function getCategoryById($id)
    {
        $sql = "SELECT id, name, expense_limit, limit_active
        FROM ".static::$financeCategoryAsignedToUserTableName.
        " WHERE id = :id AND user_id = :user_id";

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    } 

    /**
     * Add Expense category
     *
     * @return mixed inserted row id if category added, false otherwise
     */
    public static function addCategory($name, $limit = 0, $categoryLimitState = false)
    {
        //Validate name
        if(true) {

            $sql = "INSERT INTO ".static::$financeCategoryAsignedToUserTableName.
                " (name, user_id, expense_limit, limit_active) VALUES (:name, :user_id, :expense_limit, :limit_active)";

            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':name', htmlspecialchars($name), PDO::PARAM_STR);     
            $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':expense_limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':limit_active', $categoryLimitState, PDO::PARAM_BOOL);

            if($stmt->execute()) {
                return $db->lastInsertId();
            }
        }
        return false;
    } 

    /**
     * Update Expense category by id
     *
     * @return boolean true if category updated, false otherwise
     */
    public static function updateCategoryById($name, $id, $limit = 0, $categoryLimitState = false)
    {
        $sql = "UPDATE ".static::$financeCategoryAsignedToUserTableName.
               " SET name = :name, expense_limit = :expense_limit, limit_active = :limit_active
                WHERE id = :id AND user_id = :user_id";

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':name', htmlspecialchars($name), PDO::PARAM_STR); 
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);     
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':expense_limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':limit_active', $categoryLimitState, PDO::PARAM_BOOL);

        return $stmt->execute();
    } 
}