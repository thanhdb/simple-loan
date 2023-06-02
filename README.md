<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<h1 align="center">Simple Loan Management API</h1>
This is a demo for a simple loan management system.
Currently the Application is built over Laravel Framework version 8.

----------

# Getting started

## Installation

Clone the repository

    git clone git@github.com:thanhdb/simple-loan.git

Switch to the repo folder

    cd simple-loan

Install all the dependencies using composer

    composer install

Copy the example env file and make the required configuration changes in the .env file

    cp .env.example .env

Generate a new application key

    php artisan key:generate

Run the database migrations (**Set the database connection in .env before migrating**)

    php artisan migrate

Run the database seeders
    
    php artisan db:seed

Start the local development server

    php artisan serve

You can now access the server at http://127.0.0.1:8000

**TL;DR command list**

    git clone git@github.com:thanhdb/simple-loan.git
    cd simple-loan
    composer install
    cp .env.example .env
    php artisan key:generate 
    
**Make sure you set the correct database connection information before running the migrations**

    php artisan migrate
    php artisan db:seed
    php artisan serve
----------

## Features
-   Customer creates a loan
-   Admin approves the loan
-   Customer can only view self owned loan
-   Customer can repay the loan only once Admin approves the loan
-   Once customer pays all the scheduled payment the Loan is marked automatically marked as Paid

## List All End Points Available
Format of the all endpoints is `{{local.domain}}/api/v1/`
-   Auth Routes
    -   Register - `/api/v1/auth/register`
    -   Login - `/api/v1/auth/login`
    -   Logout - `/api/v1/auth/logout`

-   Loan Routes
    -   List loans - `/api/v1/loan/`
    -   Create loan - `/api/v1/loan/create`
    -   Admin view all loans - `/api/v1/loan/all`
    -   View loan - `/api/v1/loan/<loan_uuid>`
    -   Repayment loan - `/api/v1/loan/payment`
    -   Approve loan - `/api/v1/loan/approve`


## Testing

Run all the test cases using the following command from the project root directory

    php artisan test

## Postman Collection
You can find the postman collection in the postman document here 
https://documenter.getpostman.com/view/526934/2s93sW7a5i#a43010ca-844a-4a03-b4b8-f8f30a991997

[![Run in Postman](https://run.pstmn.io/button.svg)](https://documenter.getpostman.com/view/526934/2s93sW7a5i#a43010ca-844a-4a03-b4b8-f8f30a991997)

## Info about package and technical used

- Use Spatie Roles and Permissions package - https://spatie.be/docs/laravel-permission/v5/introduction for managing user permissions and roles
    - This package allows manage user permissions and roles in a database.
    - Clear documentation and easy to understand
    
- Apply Repository Pattern 
    - Split the business logic and database logic into separate classes
    - Easy to maintain and test
    - Follow the SOLID principles 
    - Can be replaced with other database or ORM

- Use Laravel Sanctum for API authentication
    - Easy to setup and use because it is built-in Laravel
    - Can be replaced with other authentication package

- Use Laravel Policy to manage the loan access
    - Use to check the user's permission to perform an action

- User Laravel Form Request to validate the request
    - Easy to validate the request
    - Can be replaced with other validation package
