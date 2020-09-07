#### Overview:
* Minimal requirements to run application is [Git](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git), [PHP](http://php.net/manual/en/install.php) and [composer](https://getcomposer.org/download/) installed
* Additionally install [Symfony-cli](https://symfony.com/download)
* Set .env file based on local.env and complete it with chosen DELIVERY_MAIL
* With Git, composer, PHP and symfony-cli installed run `git clone`, `composer install`, `bin/console make:migration`, `bin/console doctrine:migrations:migrate` and finally to start server `symfony server:start`
* Built-in Symfony server will be running under: `http://127.0.0.1:8000`
* Preview of added records can is available under http://127.0.0.1:8000/enquiry

#### Todo:
* Provide authorization with oAuth
* Cover code with unit tests
* Dockerize
* Extend request validation
* Fix smtp / email connection
* Fix duplicates checks - checkDuplicates repository method should verify by date intervals not exact date
