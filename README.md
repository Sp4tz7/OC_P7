# OC_P7

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/b2705c36e91347bc97d6c44bdba27bfa)](https://www.codacy.com/gh/Sp4tz7/OC_P7/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=Sp4tz7/OC_P7&amp;utm_campaign=Badge_Grade)

This project is part of the 7th course of my OpenClassRooms course
-  Use the Symfony framework
-  Implement an API
-  Reach the 3th level of Richardson's model

## Features

-  CRUD products
-  CRUD users
-  CRUD customers
-  Documentation page
-  OAuth authentication trough github

### Requirements

In order to use this API, the following points must be respected

-  PHP version >=7.2.5

### Installation

This _API_ project requires [PHP](https://php.net/) 7.2.5+ and [Composer](https://getcomposer.org/) to run.

Install the whole project from Github and run Composer vendors dependencies.

#### File
```sh
git clone https://github.com/Sp4tz7/OC_P7.git
cd OC_P7
composer install
```

### Configuration

Before running this framework, you have to setup the database data.
1.  Copy the .env file to a .env.local file and edit the DB requested data.
2.  Point your virtual host to the **Public** directory.

#### Install database

```
php bin/console doctrine:database:create
php bin/console doctrine:schema:update --force
php bin/console doctrine:fixtures:load --append
```

[Link to the project api example](https://bilemo.siker.ch/api)

**Free Software, Hell Yeah!**
