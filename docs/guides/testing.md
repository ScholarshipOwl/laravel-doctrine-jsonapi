# Doctrine Testing Traits

This package provides several traits to make testing with Doctrine ORM in Laravel easier, more reliable, and more powerful. Below are the available traits, their purposes, and usage details.

---

## DoctrineRefreshDatabase

**Purpose:**
Refreshes the database schema before each test using Doctrine migrations. Ensures every test starts with a clean schema, similar to Laravel's `RefreshDatabase` trait but for Doctrine.

**How it works:**
- Runs all Doctrine migrations before each test.
- Begins a transaction before the test and rolls it back after, keeping the schema but discarding test data.
- Provides hooks (`beforeRefreshingDoctrineDatabase`, `afterRefreshingDoctrineDatabase`) for custom logic before/after refresh.

**Usage:**
```php
use Sowl\JsonApi\Testing\DoctrineRefreshDatabase;

class MyTestCase extends TestCase
{
    use DoctrineRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->refreshDoctrineDatabase();
    }
}
```

---

## DoctrineDatabaseTruncation

**Purpose:**
Truncates (clears) all Doctrine-managed tables after each test. Useful for cleaning up data between tests without dropping the schema, especially when you want to retain the schema but remove all data.

**How it works:**
- Iterates over all Doctrine metadata and truncates each table.
- Uses transactions for safety.

**Usage:**
```php
use Sowl\JsonApi\Testing\DoctrineDatabaseTruncation;

class MyTestCase extends TestCase
{
    use DoctrineDatabaseTruncation;

    protected function tearDown(): void
    {
        $this->truncateDoctrineDatabaseTables();
        parent::tearDown();
    }
}
```

---

## InteractWithDoctrineDatabase

**Purpose:**
Syncs Doctrine's PDO connections with Laravel's database layer. This enables you to use Laravel's `assertDatabaseHas`, `assertDatabaseMissing`, and other DB assertions with Doctrine entities.

**How it works:**
- Syncs underlying PDO connections between Doctrine and Laravel DBAL for all managers.
- Required if you want to use Laravel's database assertions in tests that use Doctrine.

**Usage:**
```php
use Sowl\JsonApi\Testing\InteractWithDoctrineDatabase;

class MyTestCase extends TestCase
{
    use InteractWithDoctrineDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->interactsWithDoctrineDatabase();
    }
}
```

---

## Recommendations
- You can combine these traits as needed in your test base class.
- See each trait's docblock and implementation for more advanced options and hooks.
- For seeding, use Laravel/Doctrine's standard seeding patterns.

---

For more details, see the trait source code in `/src/Testing`.
