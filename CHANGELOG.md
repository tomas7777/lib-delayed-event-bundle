# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## 0.4.0 - 2022-02-05
### Added
- Configuration of listeners using attributes (`\Tjovaisas\Bundle\DelayedEventBundle\Attribute\AsDelayedEventListener`)
- Library will try to guess method's name based on the passed event when configuring listeners
### Removed
- Support for Symfony ^4.0
- Support for Symfony >=5.0 <5.3

## 0.3.0 - 2022-01-30
### Added
- Support for Symfony ^6.0
### Removed
- Support for PHP ^7.4

## 0.2.0 - 2021-08-09
### Added
- Split responsibility to handle queue to a dedicated service.
- Increased psalm's error level detection to 2.
- Increased PHP version to 7.4.
