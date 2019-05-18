# OCR photo detection - Symfony Web App

This is a simple example of how to use Google Cloud Vision API in PHP (https://github.com/googleapis/google-cloud-php-vision).

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes. See deployment for notes on how to deploy the project on a live system.

### Prerequisites

* [PHP 7.x](https://tecadmin.net/install-php-macos/)
* [Composer](https://getcomposer.org/)
* Google cloud keys (to be placed in ``keys/`` directory) to a project with access to Cloud Vision API

### Installing

After you clone the repository, to install all the dependencies, run:

```
composer install
```

Then, to run the local server:

```
composer php bin/console server:run
```

Enter in the address given and you can now past a url of an image.

## Deployment

TBD with [Heroku](https://www.heroku.com)

## Author

* **Luís Henriques** - [Github](https://github.com/Santos-Luis)
