## About Laravel

1. Clone project using `git clone https://github.com/ckpanchal/loan-app.git` command.
2. Run `composer install` or `composer update` to install all project dependencies.
3. Open `.env` file and setup database credentials.
4. Run `php artisan migrate` command to create all database tables.
5. Run `php artisan db:seed` command to seed all the database tables.
6. Run `php artisan jwt:secret` command to generate JWT secret key
7. Run `php artisan optimize:clear` and `php artisan optimize` command to perform cache, config, route clear operations.
8. Run `php artisan serve` to start web application server `http://127.0.0.1:8000`
9. Run `php artisan l5-swagger:generate` to generate swagger documentation and you can access it using `http://127.0.0.1:8000/api/documentation`


## Test Application

1. Run `php artisan test` to perform application test results.
