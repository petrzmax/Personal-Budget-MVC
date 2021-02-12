<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Income;
use \App\Models\Expense;
use \App\Models\Payment;

/**
 * Settings controller
 *
 * PHP version 7.0
 */
class Settings extends Authenticated
{

    /**
     * Before filter - called before each action method
     *
     * @return void
     */
    protected function before()
    {
        parent::before();

        $this->user = Auth::getUser();
    }

    /**
     * Show settings
     *
     * @return void
     */
    public function indexAction() {
        View::renderTemplate('Settings/index.html', [
            'incomeCategories' => Income::getCategories(),
            'expenseCategories' => Expense::getCategories(),
            'paymentCategories' => Payment::getCategories(),
            'user' => $this->user
        ]);
    }

    /**
     * AJAX - get category name
     *
     * @return void
     */
    public function getCategoryDataAction() {
        
        if(isset($_POST['postCategoryId'])) {
            $categoryId = $_POST['postCategoryId'];
        }

        if(isset($_POST['postCategoryType'])) {
            $categoryType = $_POST['postCategoryType'];
        }

        if($categoryId && $categoryType) {
            switch($categoryType) {
                case 'income':
                    $result = Income::getCategoryById($categoryId);
                    break;
    
                case 'expense':
                    $result = Expense::getCategoryById($categoryId);
                    break;
    
                case 'payment':
                    $result = Payment::getCategoryById($categoryId);
                    break;
    
                default:
                    $result = false;
                    break;
            }
    
            header('Content-Type: application/json');
            echo json_encode($result);
        }
    }

    /**
     * AJAX - Add category
     *
     * @return void
     */
    public function addCategoryAction() {
        
        if(isset($_POST['postCategoryType'])) {
            $categoryType = $_POST['postCategoryType'];
        }

        if(isset($_POST['postCategoryName'])) {
            $categoryName = $_POST['postCategoryName'];
        }

        if($categoryType && $categoryName) {
            switch($categoryType) {
                case 'income':
                    $result = Income::addCategory($categoryName);
                    break;
    
                case 'expense':
                    $result = Expense::addCategory($categoryName);
                    break;
    
                case 'payment':
                    $result = Payment::addCategory($categoryName);
                    break;
    
                default:
                    $result = false;
                    break;
            }

            header('Content-Type: application/json');
            echo $result;
            
        }
    }

    /**
     * AJAX - delete category
     *
     * @return void
     */
    public function deleteCategoryAction() {
        
        if(isset($_POST['postCategoryId'])) {
            $categoryId = $_POST['postCategoryId'];
        }

        if(isset($_POST['postCategoryType'])) {
            $categoryType = $_POST['postCategoryType'];
        }

        if($categoryId && $categoryType) {
            switch($categoryType) {
                case 'income':
                    $result = Income::deleteCategoryById($categoryId);
                    break;
    
                case 'expense':
                    $result = Expense::deleteCategoryById($categoryId);
                    break;
    
                case 'payment':
                    $result = Payment::deleteCategoryById($categoryId);
                    break;
    
                default:
                    $result = false;
                    break;
            }
    
            header('Content-Type: application/json');
            echo $result;
        }
    }
}
