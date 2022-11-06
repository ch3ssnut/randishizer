## Table of contents
* [General informations](#general-informations)
* [Technologies](#technologies)
* [Setup](#setup)

## General informations
This project is random menu and shopping list generator. It uses PHP framework - Symfony for backend and TWIG for frontend. User has to create an account, add dishes and ingredients and allocate ingredients with specific ammount and units to a specific dish. After creating dishes and ingredients user can generate random menu (number of days is based on user's input) with summed ammount of ingredients from different days (number of shopping list's days is based on user's input), menu with shopping list is generated as spreadsheet. 

## Technologies
Project is created with:
* PHP 
* Symfony 
* TWIG 
* HTML/CSS

## Setup
To run this project, install locally using composer: 

```
$ composer install
$ docker-compose up -d
$ symfony serve -d
```
Register user and login:
```
https://localhost:8000/register
https://localhost:8000/login
```

To add new dishes: 

```
https://localhost:8000/dishes
```

* edit dishes to add new ingredient with unit.
* add ingredient to specific dish that is currently edited

Generate menu and shopping list:

```
https://localhost:8000/generate
```