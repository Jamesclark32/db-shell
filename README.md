## TODO: 

add tests

write documentation 

format / smarter readline?

bold abstraction.
 
 ## Overriding connection variables
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
 
 
 ## Overriding locale and language
 out of the box, db-tinker will use the global local set in laravel's config/app.php file. If you want to override this specifically for db-tinker for any reason, that can be done in the db-tinker config by setting the locale variable. 
 Available locales are currently en and cat. If you are able to translate into any other language, I would love to see your contribution! 
 
