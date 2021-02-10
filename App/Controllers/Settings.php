<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Income;
use \App\Models\Expense;

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
            'paymentMethods' => Expense::getMethods(),
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

        if(isset($_POST['postCategoryId'])) {
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
    
                case 'paymentMethod':
                    $result = Expense::getMethodById($categoryId);
                    break;
    
                default:
                    $result = false;
                    break;
            }
    
            header('Content-Type: application/json');
            echo json_encode($result);
        }
    }

            default:

                break;
        }

        header('Content-Type: application/json');
        echo json_encode($result);
    }

}
