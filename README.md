# Opengrapher

Opengrapher is the simplest way to get informations about a web page using opengraph protocol. By this repository you can make your own instance.

## Usages

`GET /scan?query=http://example.com`

## Quick install

- Clone this repo
- Create a .env file in the root directory and fill it with env vars fields (you can get the list of the fields in .env.example)
- `composer install`

## Server configuration

This project use slim framework so, you can see directly all of server configuration to do [here](https://www.slimframework.com/docs/start/web-servers.html)

## The console

This template include console powered by symfony console:

The console allowed this commands:

### Local dev server

- php console serve -> for run a local dev server with php cli

## Maintenance mode

(not finish)

Maintenance mode allow a independent maintenance mode from your web application.

Maintenance mode is made for rename index.php file in web root directory (public) by _index.php and rename maintenance.php file by index.php file and vice versa.

- php console maintenance open -> for enable maintenance mode
- php console maintenance close -> for disable maintenance mode
