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

    /**
     * Get expense data by id
     *
     * @return mixed decimal(8,2) if found, false otherwise
     */
    public static function getCategoryCurrentMonthSumById($id)
    {
        $db = static::getDB();

        $startDate = date('Y-m-01');
        $endDate = date("Y-m-t");

        $sql = 'SELECT SUM(amount) AS categorySum
            FROM expenses AS e, expenses_category_assigned_to_users AS ecu
            WHERE e.expense_category_assigned_to_user_id = ecu.id AND ecu.id = :categoryId
            AND e.user_id = :user_id AND date_of_expense BETWEEN :start_date AND :end_date';
        
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':categoryId', $id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':start_date', $startDate, PDO::PARAM_STR);
        $stmt->bindValue(':end_date', $endDate, PDO::PARAM_STR);

        $stmt->execute();
        $result = $stmt->fetchColumn();

        if(is_null($result)) {
            return 0;
        }
        
        return $result;
    } 
}