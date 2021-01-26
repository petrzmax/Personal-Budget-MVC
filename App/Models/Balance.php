<?php

namespace App\Models;

use PDO;

/**
 * Balance model
 *
 * PHP version 7.0
 */
class Balance extends \Core\Model
{

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

        //Active period selector
        if(!isset($this->activeTimePeriod)) {
            $this->activeTimePeriod = 'currentMonth';
        }

        switch($this->activeTimePeriod) {
            default:
            case 'currentMonth':
                $this->startDate = date('Y-m-01');
                $this->endDate = date("Y-m-d");
                break;
        
            case 'lastMonth':
                $this->startDate = date('Y-m-01',strtotime('last month'));
                $this->endDate = date('Y-m-t',strtotime('last month'));
                break;
        
            case 'currentYear':
                $this->startDate = date('Y-01-01');
                $this->endDate = date("Y-m-d");
                break;
        
            case 'customPeriod':
                $this->startDate = $_GET['startDate'];
                $this->endDate =  $_GET['endDate'];
                //Validate input date
                if(!$this->validateDate($this->endDate) || !$this->validateDate($this->startDate)) {
                    header('location: balance.php');
                    exit();
                }
                break;
        }
    }

    /**
     * Get all the income categories and income sums in these categories
     *
     * @return mixed Balance object if found, false otherwise
     */
    public function getSumOfIncomeInCategories() {
        $db = static::getDB();
        $sql = 'SELECT icu.name, SUM(amount) AS categorySum
                FROM incomes AS i, incomes_category_assigned_to_users AS icu
                WHERE i.income_category_assigned_to_user_id = icu.id 
                AND i.user_id = :user_id AND date_of_income BETWEEN :start_date AND :end_date
                GROUP BY icu.name
                ORDER BY categorySum DESC';
        $stmt = $db->prepare($sql);
        
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':start_date', $this->startDate, PDO::PARAM_STR);
        $stmt->bindValue(':end_date', $this->endDate, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get all the expense categories and expense sums in these categories
     *
     * @return mixed Balance object if found, false otherwise
     */
    public function getSumOfExpenseInCategories() {
        $db = static::getDB();
        $sql = 'SELECT ecu.name, SUM(amount) AS categorySum
            FROM expenses AS e, expenses_category_assigned_to_users AS ecu
            WHERE e.expense_category_assigned_to_user_id = ecu.id 
            AND e.user_id = :user_id AND date_of_expense BETWEEN :start_date AND :end_date
            GROUP BY ecu.name
            ORDER BY categorySum DESC';
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':start_date', $this->startDate, PDO::PARAM_STR);
        $stmt->bindValue(':end_date', $this->endDate, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get activeTimePeriod
     *
     * @return string
     */
    public function getActiveTimePeriod() {
        return $this->activeTimePeriod;
    }

    public function validateDate($date, $format = 'Y-m-d'){
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    } 
}
