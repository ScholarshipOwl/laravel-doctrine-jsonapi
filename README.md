# Laravel Doctrine JSON:API
Implement feature-rich [JSON:API](https://jsonapi.org/) compliant APIs
in your [Laravel](https://laravel.com/) applications using [Doctrine ORM](https://www.doctrine-project.org/).

## Overview
This package provides a comprehensive implementation of the JSON:API specification for Laravel applications using Doctrine ORM instead of Eloquent. It reduces boilerplate code by offering default controllers and actions that handle standard API operations while ensuring full compliance with the JSON:API standard.

## Features
- **Complete JSON:API Implementation**: Full compliance with the JSON:API specification
- **Built for Doctrine ORM**: Designed specifically for Laravel applications using Doctrine ORM
- **Battle-tested**: Production-ready and robust
- **Standardised, Consistent APIs**: Uniform interface for all resources
- **Resource Operations**:
  - Fetch resources with advanced querying
  - Fetch and manipulate relationships
  - Inclusion of related resources (compound documents)
  - Sparse field sets
  - Sorting
  - Pagination
  - Filtering
- **Full CRUD Support**:
  - Create resources
  - Update resources
  - Update relationships
  - Delete resources
- **Authorization**: Policy-based access control

## Quick Start

### Installation
```bash
composer require sowl/laravel-doctrine-jsonapi
```

### Configuration
```bash
php artisan vendor:publish --provider="Sowl\JsonApi\JsonApiServiceProvider"
```

### Usage
1. Implement `ResourceInterface` on your Doctrine entities
2. Create transformers extending `AbstractTransformer`
3. Define entity relationships using `RelationshipsCollection`
4. Register resources in the `config/jsonapi.php` file
5. Use the default controller or create custom ones

## Documentation
For detailed installation instructions, configuration guides and tutorials:

[Read the Documentation](./docs/README.md)

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
  - [ ] Provide QueryBuilder for the filter parsers, instead of criteria as it allows to apply any types of filters.
  - [ ] Remove the default request hydration from the package, as it may create security vulnerabilities. All the mutations should be handled by the application code, by the client.
  - [X] Integrate scribe into the package. Automatically generate Open API specs.
    - [ ] Add documentation for scribe integration
    - [X] Update metadata for each endpoint
      - [X] Endpoint description
      - [X] Endpoint tags
      - [X] Endpoint name
      - [X] Endpoint group
    - [X] Route\URL params documentation
    - [X] Query params documentation
      - [X] Add a fields lists to each query documentation. Need to extract it from the transformer.
      - [X] Filter query parameters documentation
    - [ ] Request body documentation
      - [ ] Analyze scribe default rules parser find ways to improve it.
    - [X] Response body documentation
      - [X] Create ValidationResponseAttribute that goint to read rules from validation and generate example response.
  - [X] Add documentation about the `meta` param usage. ( meta[account]=mailbox&meta[profile]=completeness )
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
