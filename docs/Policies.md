# Policies

Your controllers should extends the `Sowl\JsonApi\Controller` which provides a middleware to handle authorization
for JSON:API requests. Under the hood it uses Gates & Policies mechanisms from [Laravel Authorization](https://laravel.com/docs/authorization).

To create a new policy run:
```shell
php artisan make:policy UserPolicy --model=Entities\\User
```

Do not use the `Illuminate\Auth\Access\HandlesAuthorization` trait in your
policies.

## Resource access

Authenticated users must be granted the following abilities to access resources
based on the HTTP method as follow:

| HTTP Method | Route                                             | Ability               | DefaultController Method |
|-------------|---------------------------------------------------|-----------------------|--------------------------|
| GET         | /{resourceType}                                   | viewAny               | list                     |
| POST        | /{resourceType}                                   | create                | create                   |
| GET         | /{resourceType}/{id}                              | view                  | show                     |
| PATCH       | /{resourceType}/{id}                              | update                | update                   |
| DELETE      | /{resourceType}/{id}                              | delete                | remove                   |


## Relationships access

Authenticated users must be granted the following abilities to access
relationships based on the HTTP method as follow:

### To-One relationships

| HTTP Method | Route                                             | Ability               | DefaultController Method |
|-------------|---------------------------------------------------|-----------------------|--------------------------|
| GET         | /{resourceType}/{id}/{relationship}               | view{Relationship}    | showRelated              |
| GET         | /{resourceType}/{id}/relationships/{relationship} | view{Relationship}    | showRelationships        |
| PATCH       | /{resourceType}/{id}/relationships/{relationship} | update{Relationship}  | updateRelationships      |

### To-Many relationships

| HTTP Method | Route                                             | Ability               | DefaultController Method |
|-------------|---------------------------------------------------|-----------------------|--------------------------|
| GET         | /{resourceType}/{id}/{relationship}               | viewAny{Relationship} | showRelated              |
| GET         | /{resourceType}/{id}/relationships/{relationship} | viewAny{Relationship} | showRelationships        |
| PATCH       | /{resourceType}/{id}/relationships/{relationship} | update{Relationship}  | updateRelationships      |
| POST        | /{resourceType}/{id}/relationships/{relationship} | attach{Relationship}  | createRelationships      |
| DELETE      | /{resourceType}/{id}/relationships/{relationship} | detach{Relationship}  | removeRelationships      |
