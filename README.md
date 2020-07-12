## About

DbTinker adds an artisan command which emulates the mysql command line client.

This allows you to interact with the database directly via straight sql.


## Why

Refer to the [why documentation](why.md) if you are interested in the motivation for this package.

## Installation
Installing db-tinker is a straightforward matter. To add db-tinker to your project as a dev dependency, simply run:

`composer require-dev Jamesclark32/db-tinker`

If you would like db-tinker available on your production installation(s) as well: 

`composer require Jamesclark32/db-tinker`

## Publishing Config
If you want to fine-tune the configuration, or language file you will need to publish them by running:

`php artisan vendor:publish --provider="Jamesclark32\DbTinker\DbTinkerServiceProvider"`

This will copy the files to your project's `config/` and `language/vendor` folders, where you can modify them as you see fit. 

## Usage

Once installed, you can launch the db-tinker interface via:

`php artisan db-tinker`

This will start a long-running process which will allow you to directly input sql queries and see the results.

For the most part, this will behave like the mysql cli client.

You can exit the interface at any time by pressing `control + C`  

## Configuration
See the Publishing Configs section for instructions to make the config file available to modify.

This section is a bit vague. Sorry about that! Hopefully I flesh it out soon.
Currently you can configure: 
    colors
    override connection vars
    confirm display of large result sets

### Overriding connection variables
  Out of the box db-tinker will use laravel's default database connection.
   
  Overriding these settings is possible via these connection variables.
  
  While it is certainly possible to set them directly in this config, it is intended to alter any or all of them  via these env vars:
            
   
    DB-TINKER_HOST
    DB-TINKER_PORT
    DB-TINKER_DATABASE
    DB-TINKER_USERNAME
    DB-TINKER_PASSWORD
    DB-TINKER_SOCKET
 
 db-tinker will continue to use the default connection, however, during startup, the variables will be overwritten with these variables and then connection reinitialized during.
 
 
 ### Overriding locale and language
 out of the box, db-tinker will use the global local set in laravel's config/app.php file. If you want to override this specifically for db-tinker for any reason, that can be done in the db-tinker config by setting the locale variable. 
 Available locales are currently en and cat. If you are able to translate into any other language, I would love to see your contribution! 
 


## Drop me a line!

If you're using this package, I'd love to hear your thoughts about it! 
 
## TODO: 

auto width-based \g \G switching  @ src/Output/SelectStatement.php:44

add tests

exit on `exit` input
prevent `control + C` exit (and update usage doc to reflect this)

formatable and smarter user input

bold abstraction

configurable prompt

configurable table stype (via symfony console table options)

improve documentation. flesh out information and add some pictures.