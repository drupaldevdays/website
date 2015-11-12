# Drupal Dev Days Milan 2016 website.

## How to install in a local environment

* Ensure that you have git, composer and drush (version 8+) on your path
* git clone https://github.com/drupaldevdays/website.git ddd
* cd ddd
* composer install
* Set up a database and ensure that the db url is correct in build.loc.yml
* sudo vendor/bin/robo build:local

## How to run acceptance tests

* Ensure that you have Selenium Server running
* Setup WebDriver settings in tests/acceptance.suite.yml
* vendor/bin/robo run:tests
