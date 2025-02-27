# CLAUDE.md - Laravel Doctrine JSON:API Agent Guidelines

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