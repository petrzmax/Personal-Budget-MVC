<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;

/**
 * Account controller
 *
 * PHP version 7.0
 */
class Account extends \Core\Controller
{

    /**
     * Validate if email is available (AJAX) for a new signup or an existing user.
     * The ID of an existing user can be passed in in the querystring to ignore when
     * checking if an email already exists or not.
     *
     * @return void
     */
    public function validateEmailAction()
    {
        $is_valid = ! User::emailExists($_GET['email'], $_GET['ignore_id'] ?? null);
        
        header('Content-Type: application/json');
        echo json_encode($is_valid);
    }

    /**
     * Show the delete account success page
     *
     * @return void
     */
    public function deleteSuccessAction()
    {
        View::renderTemplate('account/delete-success.html');
    }
}
