<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/993323" height="100px">
    </a>
    <h1 align="center">Yii 2 REST API Project Template</h1>
    <br>
</p>

Yii 2 REST API Project Template is a skeleton [Yii 2](http://www.yiiframework.com/) application best for
rapidly creating small rest api projects.

The template contains the basic features including user join/login api.
It includes all commonly used configurations that would allow you to focus on adding new
features to your application.

This project fork from [forecho/yii2-rest-api](https://github.com/forecho/yii2-rest-api/) with addional feature (like caching using redis) for improving performance app.

[![Testing](https://github.com/forecho/yii2-rest-api/workflows/Testing/badge.svg)](https://github.com/forecho/yii2-rest-api/actions)
[![Lint](https://github.com/forecho/yii2-rest-api/workflows/Lint/badge.svg)](https://github.com/forecho/yii2-rest-api/actions)
[![Code Coverage](https://scrutinizer-ci.com/g/forecho/yii2-rest-api/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/forecho/yii2-rest-api/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/forecho/yii2-rest-api/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/forecho/yii2-rest-api/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/forecho/yii2-rest-api/v/stable)](https://packagist.org/packages/forecho/yii2-rest-api) 
[![Total Downloads](https://poser.pugx.org/forecho/yii2-rest-api/downloads)](https://packagist.org/packages/forecho/yii2-rest-api) 
[![Latest Unstable Version](https://poser.pugx.org/forecho/yii2-rest-api/v/unstable)](https://packagist.org/packages/forecho/yii2-rest-api) 
[![License](https://poser.pugx.org/forecho/yii2-rest-api/license)](https://packagist.org/packages/forecho/yii2-rest-api)

REQUIREMENTS
------------

The minimum requirement by this project template that your Web server supports PHP 8.2.0.

INSTALLATION
------------

### Install via Composer

If you do not have [Composer](http://getcomposer.org/), you may install it by following the instructions
at [getcomposer.org](http://getcomposer.org/doc/00-intro.md#installation-nix).

You can then install this project template using the following command:

~~~
composer create-project --prefer-dist forecho/yii2-rest-api <rest-api>
cd <rest-api>
cp .env.example .env
php yii generate/key # optional 
~~~

Now you should be able to access the application through the following URL, assuming `rest-api` is the directory
directly under the Web root.

~~~
http://localhost/<rest-api>/web/
~~~

### Install from GitHub

Accessing [Use this template](https://github.com/forecho/yii2-rest-api/generate) Create a new repository from yii2-rest-api

```sh
cd <rest-api>
cp .env.example .env
php yii generate/key # optional 
```

You can then access the application through the following URL:

~~~
http://localhost/<rest-api>/web/
~~~


### Install with Docker

Update your vendor packages

```sh
docker-compose run --rm php composer update --prefer-dist
```

Run the installation triggers (creating cookie validation code)

```sh
docker-compose run --rm php composer install    
```

Start the container

```sh
docker-compose up -d
```
   
You can then access the application through the following URL:

```
http://127.0.0.1:8001
```

**NOTES:** 
- Minimum required Docker engine version `17.04` for development (see [Performance tuning for volume mounts](https://docs.docker.com/docker-for-mac/osxfs-caching/))
- The default configuration uses a host-volume in your home directory `.docker-composer` for composer caches

Check out the packages
------------

- [yiithings/yii2-dotenv](https://github.com/yiithings/yii2-dotenv)
- [sizeg/yii2-jwt](https://github.com/sizeg/yii2-jwt)
- [yiier/yii2-helpers](https://github.com/yiier/yii2-helpers)

Use
------------

Detail about apidoc can see at [Stoplight](https://flip.stoplight.io/docs/assignment/YXBpOjIxMjIxMzkx-flip-offline-assignment)