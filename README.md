# Varyn Website

This repository holds the code and resources behind the Varyn.com website.

The site is built on:
 * HTML 5 and CSS 3
 * JavaScript ES5
 * Bootstrap 4
 * [PHP](https://php.net) 7.2
 * [node.js](https://nodejs.org) 10
 * [Enginesis](https://enginesis.com)

All public facing resources are found in the `public` folder, and this is the folder pointed to by the web server to serve those resources.

Website services are found in the `services` folder. This is only accessible via PHP since it is outside the public website.

## To do

1. update -q and liver servers with serverConfig.php
2. once logged in, add authToken to user configuration
3. sync Enginesis.php (added getAUthToken)
4. setup node build, Jest test
