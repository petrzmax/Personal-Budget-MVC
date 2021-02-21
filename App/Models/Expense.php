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