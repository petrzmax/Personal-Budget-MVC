<?php

namespace App;

/**
 * Application configuration
 *
 * PHP version 7.0
 */
class Config
{

    /**
     * Database host
     * @var string
     */
    const DB_HOST = 'localhost';

    /**
     * Database name
     * @var string
     */
    const DB_NAME = 'personal_budget';

    /**
     * Database user
     * @var string
     */
    const DB_USER = 'root';

    /**
     * Database password
     * @var string
     */
    const DB_PASSWORD = '';

    /**
     * Show or hide error messages on screen
     * @var boolean
     */
    const SHOW_ERRORS = true;

    /**
     * Secret key for hashing
     * @var boolean
     */
    const SECRET_KEY = 'your-secret-key';

    /**
     * Mail host
     *
     * @var string
     */
    const MAIL_HOST = 'your-mail-host';

    /**
     * Mail host authentication
     *
     * @var boolean
     */
    const MAIL_HOST_AUTHENTICATION = true;

    /**
     * Mail username
     *
     * @var string
     */
    const MAIL_USERNAME = 'your-mail-username';

    /**
     * Mail password
     *
     * @var string
     */
    const MAIL_PASSWORD = 'your-mail-password';

    /**
     * Mail smtp secure type
     *
     * @var string
     */
    const MAIL_SMTP_SECURE_TYPE = 'tls';

    /**
     * Mail smtp port
     *
     * @var int
     */
    const MAIL_SMTP_PORT = 'your-mail-smtp-port';

    /**
     * Mail sender name
     *
     * @var string
     */
    const MAIL_SENDER_NAME = 'your-mail-sender-name';    
}
