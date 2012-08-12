# Korstiaan.com

[Silex](http://silex.sensiolabs.org/) based app intended for www.korstiaan.com

[![Build Status](https://secure.travis-ci.org/korstiaan/korstiaan.com.png?branch=master)](http://travis-ci.org/korstiaan/korstiaan.com)

WIP

## Installation

Just use `composer` to install the dependencies:

```bash
$ curl -s http://getcomposer.org/installer | php
$ php composer.phar install
```

Next make a copy of the config skeleton and edit it to fit your needs:

```bash
$ cp app/config.dist.php app/config.php
$ vi app/config.php
```

See the [Symfony documentation](http://symfony.com/doc/current/book/installation.html#configuration-and-setup) for setting up correct `app/cache` and `app/logs` permissions.

# License

Korstiaan.com is licensed under the MIT license.
