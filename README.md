# Delayed Event Bundle

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
![PHPUnit][ico-phpunit]
[![Total Downloads][ico-downloads]][link-downloads]

Symfony bundle to handle post flush doctrine event in an usual event manner

## Why ?

Symfony already offers great way to dispatch events to predefined subscribers. 
This bundle extends that functionality to let listeners receive a dispatched event only after
flush has been called. There are a few reasons for that:
- application needs to make external calls only after data been written to the database;
- application needs to publish data to queue only after data been written to the database;
- applications logic requires flushing only in the upper level (e.g. controller or command) while most of the logic lies deep into the services. 
  This helps to avoid having multiple events dispatched from the controllers or commands;
- application needs to make other extra steps only after data been written to the database;

## Installation

```
composer require tjovaisas/delayed-event-bundle
```

## Usage

The only thing that's needed is to change the default tag from `kernel.event_listener` to `tjovaisas.event_listener.post_flush`:
```
<service class="Namespace\SomeListener"
         id="namespace.some_listener">
    <tag name="tjovaisas.event_listener.post_flush" event="some_event" method="onEvent" priority="1" />
</service>
```

## Caviats

There is no easy way to know if an entity already appears in the database after the changes if transaction is being used.

Due to doctrine's default behavior using transactions, first action being used is `flush` and later on `commit`. 
That means that listener may get event with data that already has database generated fields (e.g. `id`), but still may not be 100% in the database. 
Transactions are being sealed only after `commit` has been called and if this action fails, changes will not appear in the database and listener wouldn't know that.

## Semantic versioning

This bundle follows [semantic versioning](http://semver.org/spec/v2.0.0.html).

Public API of this bundle (in other words, you should only use these features if you want to easily update
to new versions):
- only services that are not marked as `public="false"`;
- only classes, interfaces and class methods that are marked with `@api`;
- console commands;
- supported DIC tags.

For example, if only class method is marked with `@api`, you should not extend that class, as constructor
could change in any release.

See [Symfony BC rules](https://symfony.com/doc/current/contributing/code/bc.html) for basic information
about what can be changed and what not in the API. Keep in mind, that in this bundle everything is
`@internal` by default.

## Running tests

```
composer update
composer test
```

## Contributing

Feel free to create issues and give pull requests.

You can check code style issues using this command:
```
composer check-style
```

[ico-version]: https://img.shields.io/packagist/v/tjovaisas/delayed-event-bundle?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-phpunit]: https://github.com/tomas7777/lib-delayed-event-bundle/actions/workflows/phpunit.yml/badge.svg
[ico-downloads]: https://img.shields.io/packagist/dt/tjovaisas/delayed-event-bundle?style=flat-square

[link-packagist]: https://packagist.org/packages/tjovaisas/delayed-event-bundle
[link-downloads]: https://packagist.org/packages/tjovaisas/delayed-event-bundle
