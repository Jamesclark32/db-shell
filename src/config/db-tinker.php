<?php

return [
    /*
     *  Acceptable colors are: black, red, green, yellow, blue, magenta, cyan, white, and default
     */
    'colors' => [
        'responses' => [
            'delete' => 'green',
            'error' => 'green',
            'exit' => 'green',
            'footer' => 'green',
            'insert' => 'green',
            'reconnecting' => 'green',
            'update' => 'green',
            'use' => 'green',
        ],
        'table' => [
            'border' => 'green',
            'column_data' => 'green',
            'column_head' => 'yellow',
        ],
        'vertical' => [
            'column_data' => 'green',
            'column_head' => 'yellow',
            'delimiter_row' => 'green',
        ],
    ],

    /*
     *  Out of the box db-tinker will use laravel's default database connection.
     *  Overriding these settings is possible via these connection variables.
     *  While it is certainly possible to set them directly in this config
     *  it is intended to alter any or all of them  via these env vars:
     *      DB-TINKER_HOST
     *      DB-TINKER_PORT
     *      DB-TINKER_DATABASE
     *      DB-TINKER_USERNAME
     *      DB-TINKER_PASSWORD
     *      DB-TINKER_SOCKET
     */
    'connection' => [
        'host' => env('DB-TINKER_HOST', config('database.connections.' . config('database.default') . '.host')),
        'port' => env('DB-TINKER_HOST', config('database.connections.' . config('database.default') . '.port')),
        'database' => env('DB-TINKER_DATABASE',
            config('database.connections.' . config('database.default') . '.database')),
        'username' => env('DB-TINKER_USERNAME',
            config('database.connections.' . config('database.default') . '.username')),
        'password' => env('DB-TINKER_PASSWORD',
            config('database.connections.' . config('database.default') . '.password')),
        'socket' => env('DB-TINKER_SOCKET', config('database.connections.' . config('database.default') . '.socket')),
    ],

    /*
     * If you would prefer to be prompted for a password when launching db-tinker instead of storing
     * the password in an .env or config file, simply set this prompt_for_password option to true
     */
    'prompt_for_password' => false,

    /*
     *  Out of the box, db-tinker will inherit the global locale setting from laravel's config/app.php configuration file.
     *  If you wish to override db-tinker's locale, simply set this locale variable to any locale from the lang folder.
     *  Available locales are :
     *       en - American English
     *      cat - A common feline dialect, useful primarily for testing locale functionality
     */
    'locale' => 'en',

    /*
     * If you would like db-tinker to verify the display of large results sets,
     * ensure this confirm_large_result_set_display variable is set to true.
     * You can also fine-tune the threshold which constitutes large here.
     */
    'confirm_large_result_set_display' => true,
    'confirm_large_result_set_limit' => 250,

    /*
     * db-tinker can automatically switch between table mode and vertical mode when displaying multiple rows
     * to do this, it pre-processes all retrieved data and establishes the width of the table to be drawn
     * and compares this with the current width of the terminal in which db-tinker is actively running
     * setting this automatically_switch_to_table_display to false will prevent this from happening
     * this behavior can be helpful, but does does have a performance impact on larger data sets
     * The automatic switching behavior can be overridden using a traditional /g or /G syntax
     */
    'automatically_switch_to_table_display' => true,
];
