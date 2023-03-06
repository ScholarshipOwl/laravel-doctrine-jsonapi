# ResourceInterface
Entity must implement [Sowl\JsonApi\ResourceInterface](../src/ResourceInterface.php) to become resource. 
Resource can be used for generation of JSON:API responses.

We must implement 4 methods to match the interface, lets review all of them.

## getResourceKey()
This static method is needed to define the `type` as part of JSON:API [Resource Object](https://jsonapi.org/format/#document-resource-objects).

```php
public static function getResourceKey(): string
{
    return 'users';
}
```

## getId()
This method must return the resource `id` as part of JSON:API [Resource Object](https://jsonapi.org/format/#document-resource-objects)

```php
public function getId(): int
{
    return $this->id;
}
```

## transformer()
Package using [Fractal](https://fractal.thephpleague.com/) for serialization of entities\resources into the JSON:API responses.
Please read [official documentation](https://fractal.thephpleague.com/transformers/) to be familiar with proper implementation of the transformer.

The `transformer` method must return new transformer object that must inherit from [Sowl\JsonApi\AbstractTransformer](../src/AbstractTransformer.php).


```php
public static function transformer(): AbstractTransformer
{
    return new UserTransformer();
}
```

Example of simple transformer implementation:
```php
class UserTransformer extends AbstractTransformer
{
    public function transform(User $user): array
    {
        return [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
        ];
    }
}
```

## relationships()
Method must return the resource relationships definitions.
We need this method so that we can automatically serve all the relationships endpoints.

Method must return [Sowl\JsonApi\Relationships\RelationshipsCollection](../src/Relationships/RelationshipsCollection.php).

Example of resource without relationships:
```php
public static function relationships(): RelationshipsCollection
{
    return new RelationshipsCollection();
}
```

There are 2 types of relationships `To-One` and `To-Many` relationships.

### To-One
To define new "To-One" relationship we must add new [Sowl\JsonApi\Relationships\ToOneRelationship](../src/Relationships/ToOneRelationship.php).

To-One relationship returns single resource in the responses.

2 required parameters must be provider and 1 optional.

#### name
First parameter must be the name of relationships that will be used for generating building endpoints.
For example `GET /user/1/{name}` or `GET /user/1/relationships/{name}`

#### class
The resource class of the relation that also must implement `ResourceInterface`.

#### property
Optional parameter that is defining the entity property name, by default the `name` param value will be used.

```php
public static function relationships(): RelationshipsCollection
{
    return new RelationshipsCollection([
        ToOneRelationship::create('user', User::class)
    ]);
}
```

### To-Many
To define new "To-Many" relationship we must add new [Sowl\JsonApi\Relationships\ToManyRelationship](../src/Relationships/ToManyRelationship.php).

To-Many relationship returns list of resources in the responses.

3 required parameters must be provider and 1 optional.

#### name
First parameter must be the name of relationships that will be used for generating building endpoints.
For example `GET /user/1/{name}` or `GET /user/1/relationships/{name}`

#### class
Resource class of the relation that also must implement `ResourceInterface`.

#### mappedBy
Property on the resource class of the relationship that represents invert relation to the parent resource.

This param can be used for generating queries for fetching list of resources.

```php
->innerJoin("user.$mappedBy", 'relation')
->where("relation.users = ${resource->getId()}")
```

#### property
Optional parameter that is defining the entity property name, by default the `name` param value will be used.

```php
public static function relationships(): RelationshipsCollection
{
    return new RelationshipsCollection([
        ToManyRelationship::create('roles', Role::class, 'users')
    ]);
}
```

### memoizeRelationships
In previous example we generate new objects on each `relationships` method call.
We can use memoize pattern so that we will build the relationships list once and save them in memory and return on request.

For that `ResourceInterface` must use [Sowl\JsonApi\Relationships\MemoizeRelationshipsTrait](../src/Relationships/MemoizeRelationshipsTrait.php).

The Trait provides static method `memoizeRelationships(callback $cb): RelationshipsCollection`.
Method must receive callback that will return relationships' collection.

```php
public static function relationships(): RelationshipsCollection
{
    return static::memoizeRelationships(fn () => [
        ToOneRelationship::create('user', User::class),
        ToManyRelationship::create('roles', Role::class, 'users'),
    ]);
}
```


