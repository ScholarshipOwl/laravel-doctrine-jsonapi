# Laravel Doctrine JSON:API
Implement feature-rich [JSON:API](https://jsonapi.org/) compliant APIs
in your [Laravel](https://laravel.com/) applications using [Doctrine ORM](https://www.doctrine-project.org/).

## Features
- Built for Doctrine ORM
- Battle-tested
- Standardised, consistent APIs
- Fetch resources
- Fetch relationships
- Inclusion of related resources (compound documents)
- Sparse field sets
- Sorting
- Pagination
- Filtering
- Create resources
- Update resources
- Update relationships
- Delete resources

## Documentation
Installation instructions, config guides and tutorials.

[Documentation](./docs/README.md)

## Development
Clone the project locally.

Install the package dependencies and run the tests:
```shell
docker compose run php
```

To enter the docker container:
```shell
docker compose run php sh
```

### Testing
[Testing Documentation](./tests)

### Roadmap
  - [ ] Make it possible to disable links generation. As it is not required by JSON:API spec and increases response size.
  - [ ] Create a validation rule "resource", "resourceExists" for validating resource identifier. Example of usage:
        ```php
        $this->validate($request, [
            'data' => 'required|resource:users',
        ]);
        ```
  - [ ] Create default global error handler or write down documentation how to create such one.
        How to handle missing route\endpoint 404 and internal 500 errors.
  - [ ] Create console command for resource policy generation: "jsonapi:make:policy".
  - [ ] Checkout option for adding include params like "include=roles:sort"  (TransformerAbstract.php:173)
