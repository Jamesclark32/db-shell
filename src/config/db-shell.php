<?php

return [
    /*
     *  Acceptable colors are: black, red, green, yellow, blue, magenta, cyan, white, and default
     *  Any color can be prepended with the word 'bold'. For example, boldgreen or boldyellow.
     */
    'colors' => [
        'responses' => [
            'count' => 'green',
            'error' => 'green',
            'exit' => 'green',
            'footer' => 'green',
            'reconnecting' => 'green',
            'update' => 'green',
            'use' => 'green',
        ],
        'table' => [
            'border' => 'boldgreen',
            'column_data' => 'green',
            'column_head' => 'boldyellow',
        ],
        'vertical' => [
            'column_data' => 'green',
            'column_head' => 'boldyellow',
            'delimiter_row' => 'boldgreen',
        ],
    ],

    /*
     *  Out of the box db-shell will use laravel's default database connection.
     *  Overriding these settings is possible via these connection variables.
     *  While it is certainly possible to set them directly in this config
     *  it is intended to alter any or all of them  via these env vars:
     *      DB-SHELL_HOST
     *      DB-SHELL_PORT
     *      DB-SHELL_DATABASE
     *      DB-SHELL_USERNAME
     *      DB-SHELL_PASSWORD
     *      DB-SHELL_SOCKET
     */
    'connection' => [
        'host' => env('DB-SHELL_HOST', config('database.connections.'.config('database.default').'.host')),
        'port' => env('DB-SHELL_HOST', config('database.connections.'.config('database.default').'.port')),
        'database' => env('DB-SHELL_DATABASE',
            config('database.connections.'.config('database.default').'.database')),
        'username' => env('DB-SHELL_USERNAME',
            config('database.connections.'.config('database.default').'.username')),
        'password' => env('DB-SHELL_PASSWORD',
            config('database.connections.'.config('database.default').'.password')),
        'socket' => env('DB-SHELL_SOCKET', config('database.connections.'.config('database.default').'.socket')),
    ],

    /*
     * If you would prefer to be prompted for a password when launching db-shell instead of storing
     * the password in an .env or config file, simply set this prompt_for_password option to true
     */
    'prompt_for_password' => false,

    /*
     *  Out of the box, db-shell will inherit the global locale setting from laravel's config/app.php configuration file.
     *  If you wish to override db-shell's locale, simply set this locale variable to any locale from the lang folder.
     *  Available locales are :
     *       en - American English
     *      cat - A common feline dialect, useful primarily for testing locale functionality
     */
    'locale' => 'en',

    /*
     * If you would like db-shell to verify the display of large results sets,
     * ensure this confirm_large_result_set_display variable is set to true.
     * You can also fine-tune the threshold which constitutes large here.
     */
    'confirm_large_result_set_display' => true,
    'confirm_large_result_set_limit' => 250,

    /*
     * db-shell can automatically switch between table mode and vertical mode when displaying multiple rows
     * to do this, it pre-processes all retrieved data and establishes the width of the table to be drawn
     * and compares this with the current width of the terminal in which db-shell is actively running
     * setting this automatically_switch_to_table_display to false will prevent this from happening
     * this behavior can be helpful, but does does have a performance impact on larger data sets
     * The automatic switching behavior can be overridden using a traditional /g or /G syntax
     */
    'automatically_switch_to_table_display' => true,
];
