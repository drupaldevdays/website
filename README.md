# Drupal Dev Days Milan 2016 website.

## How to install

* ensure that you have git, composer and drush (version 8+) on your path
* git clone https://github.com/drupaldevdays/website.git ddd
* cd ddd
* composer install
* Set up a database and ensure that the db url is correct in build.loc.yml
* sudo vendor/bin/robo build:local

