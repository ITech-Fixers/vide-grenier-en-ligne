<?php

declare(strict_types=1);

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
    const string DB_HOST = '172.20.0.2';

    /**
     * Database name
     * @var string
     */
    const string DB_NAME = 'vgel';

    /**
     * Database user
     * @var string
     */
    const string DB_USER = 'root';

    /**
     * Database password
     * @var string
     */
    const string DB_PASSWORD = 'root';

    /**
     * Show or hide error messages on screen
     * @var boolean
     */
    const bool SHOW_ERRORS = false;

    /**
     * Base URL
     * @var string
     */
    const string BASE_URL = "http://localhost:8080/";
}
