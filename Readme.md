tic_tac_toe
A bad implementation of Tic Tac Toe writen in Symfony.

What we would like you to do? fix it. make it better. tell us what's wrong and why.

good luck Evil Bunnies

-----------------------------------------------------------------------------------

# 🐳 Docker + PHP 8.1 + MySQL + Nginx + Symfony 6.1 Boilerplate

## Description

This is a complete stack for running Symfony 6.1 into Docker containers using docker-compose tool with [docker-sync library](https://docker-sync.readthedocs.io/en/latest/).

It is composed by 4 containers:

- `nginx`, acting as the webserver.
- `php`, the PHP-FPM container with the 8.0 version of PHP.
- `db` which is the MySQL database container with a **MySQL 8.0** image.

## Installation

1. 😀 Clone this rep.

2. Install docker and Run : 

make start / (cd ./.docker && docker compose up -d && docker compose exec php composer install)

otherwise you can use xampp setup :)

3. Go to http://localhost/
