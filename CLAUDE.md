# CLAUDE.md - Laravel Doctrine JSON:API Agent Guidelines
It is important to note that this document is a guide for developers and AI agents working on the Laravel Doctrine JSON:API package.

This package allows developers to create JSON:API endpoints using the Doctrine ORM for data persistence in their Laravel projects. It provides a set of base classes and interfaces for resources, controllers, and repositories that can be used to quickly create JSON:API compliant endpoints. It also includes a set of tools for creating and handling JSON:API compliant requests and responses, such as a JSON:API request parser and a JSON:API response builder.

The package is designed to be installed in a Laravel project, but for testing purposes, a dummy Laravel installation is used under the `tests/laravel` directory.


## Build & Test Commands

### Development Environment
- Enter container: `docker compose run php sh`
- Start development: `docker compose run php`

### Testing
- Run all tests: `docker compose run php phpunit`
- Run single test: `docker compose run php phpunit --filter TestName`
- Test with coverage: `docker compose run php phpunit --coverage-html ./tests/coverage`

### Other Commands
- Generate migrations: `docker compose run php tests/artisan doctrine:migrations:diff --no-interaction`

## Code Style Guidelines

- PHP 8.1+ features (type hints, readonly, union types, attributes)
- PSR-12 compliant formatting with 4-space indentation
- PascalCase for classes, camelCase for methods/variables
- Naming: Prefix abstracts with "Abstract", suffix interfaces with "Interface", prefix traits with "With"
- Use custom exceptions extending JsonApiException for structured error responses
- Organize imports by namespace groups, alphabetically within groups
- Prefer composition over inheritance using traits
- Use factory methods (static create()) and fluent interfaces for builders
- Document classes and methods with PHPDoc format
- Dependency injection via constructors following Laravel conventions
- Follow JSON:API specification patterns for resource representation

## Project Structure Convention

```
laravel-doctrine-jsonapi/
├── src/                    # Source code
├── config/                 # Package configuration
├── docs/                   # Documentation
├── routes/                 # Package routes
├── tests/                  # Tests
│   ├── unit/               # Unit tests
│   └── laravel/            # Laravel setup for package integration tests.
├── vendor/                 # Dependencies
```
