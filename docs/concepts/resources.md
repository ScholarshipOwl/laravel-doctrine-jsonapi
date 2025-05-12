# Resources: The Heartbeat of Your API

Think of **Resources** as the fundamental "nouns" of your API – the digital representations of the key entities your application deals with, like `users`, `blogPosts`, `products`, or `orders`. In the world of Laravel Doctrine JSON:API, a Resource typically mirrors a Doctrine entity, acting as the bridge between your backend data model and the structured, standardized way your API presents that data to the outside world.

Defining your resources clearly and consistently is the cornerstone of building a powerful, intuitive, and easy-to-use API. When resources are well-designed, clients can easily understand what data is available and how it interconnects.

**On this page:**

-   [Why Resources Matter](#why-resources-matter)
-   [Core Concepts: Anatomy of a Resource](#core-concepts-anatomy-of-a-resource)
-   [Implementing a Resource](#implementing-a-resource)
    -   [Basic Example: The User Resource](#basic-example-the-user-resource)
    -   [Example with Relationships: The BlogPost Resource](#example-with-relationships-the-blogpost-resource)
-   [Best Practices for Resource Design](#best-practices-for-resource-design)

---

## Why Resources Matter

Well-defined resources bring several advantages:

-   **Clarity & Predictability:** Consistent resource structures (type, id, attributes, relationships) make your API predictable and easier for developers to consume.
-   **Standardization:** Adhering to the JSON:API specification for resources means you can leverage existing tools, libraries, and conventions, speeding up development.
-   **Interconnectivity:** Resources explicitly define how data connects (via relationships), allowing clients to navigate your data graph efficiently.
-   **Foundation for Features:** Resources are the basis for fetching, creating, updating, and deleting data through your API endpoints.

## Core Concepts: Anatomy of a Resource

Every JSON:API resource object shares a common structure, built around these key concepts (see the [JSON:API Resource Object specification](https://jsonapi.org/format/#document-resource-objects) for full details):

-   **Type (`type`):** A string that categorizes the resource (e.g., `"users"`, `"blogPosts"`). It tells clients *what kind* of data this is. This should generally be plural and consistent across your API.
-   **Identifier (`id`):** A unique string that distinguishes this specific resource instance from others of the *same type*. This is often your entity's primary key, converted to a string.
-   **Attributes (`attributes`):** An object containing the resource's own data fields (e.g., a user's `name` and `email`). Relationships are *not* included here.
-   **Relationships (`relationships`):** An object describing connections to *other* resources (e.g., a blog post's `author` or `comments`). See [Relationships](./relationships.md) for details.
-   **Links (`links`):** An object containing links related to the resource, typically including a `self` link pointing to the resource's own URL.

In this package, your Doctrine entity itself often acts as the resource, implementing `Sowl\JsonApi\ResourceInterface` to provide the necessary information, especially the `type`, `id`, and `relationships`. The `attributes` are typically handled by a dedicated [Transformer](./transformers.md).

## Implementing a Resource

To designate a Doctrine entity as a JSON:API resource, you need it to implement the `Sowl\JsonApi\ResourceInterface`.

### Basic Example: The User Resource

Here's a simple `User` entity implementing the interface:

```php {3,21-24,29-32,37-40,46-51}
#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User implements ResourceInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(type: 'string', unique: true)]
    private string $email;

    // Other properties and methods...

    /**
     * Returns the unique identifier for the resource. MUST be a string.
     */
    public function getId(): string
    {
        return (string) $this->id;
    }

    /**
     * Returns the JSON:API resource type.
     */
    public static function getResourceType(): string
    {
        return 'users';
    }

    /**
     * Returns the FQCN of the transformer for this resource.
     */
    public static function transformer(): string
    {
        return UserTransformer::class;
    }

    /**
     * Defines the relationships for this resource.
     * Use the WithRelationships trait for easier management.
     */
    public static function relationships(): RelationshipsCollection
    {
        // See Relationships documentation for how to define these.
        // For a basic user, maybe it's initially empty or has common ones.
        return new RelationshipsCollection([]);
    }
}
```

### Example with Relationships: The BlogPost Resource

Now, let's consider a `BlogPost` resource that has relationships: an `author` (To-One User) and `comments` (To-Many Comment). We'll use the `WithRelationships` trait here for convenience.

```php {5,47-56}
#[ORM\Entity]
#[ORM\Table(name: 'blog_posts')]
class BlogPost implements ResourceInterface
{
    use WithRelationships;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    private string $title;

    #[ORM\Column(type: 'text')]
    private string $content;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'author_id', referencedColumnName: 'id')]
    private User $author; // To-One relationship

    #[ORM\OneToMany(mappedBy: 'blogPost', targetEntity: Comment::class)]
    private Collection $comments; // To-Many relationship

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    // Getters for title, content, author, comments...

    public function getId(): string
    {
        return (string) $this->id;
    }

    public static function getResourceType(): string
    {
        return 'blogPosts';
    }

    public static function transformer(): string
    {
        return BlogPostTransformer::class;
    }

    public static function relationships(): RelationshipsCollection
    {
        return static::resolveRelationships(fn () => [
            // Field name 'author', related resource type User::class, relationship name 'author'
            ToOneRelationship::create('author', User::class, 'author'),

            // Field name 'comments', related resource type Comment::class, relationship name 'comments'
            ToManyRelationship::create('comments', Comment::class, 'comments'),
        ]);
    }
}
```

> **Important:** Remember to register your resource types (like `'users'` and `'blogPosts'`) and their corresponding entity classes in the `resources` array within your `config/jsonapi.php` configuration file. This allows the package to automatically map incoming request types to your entities.

## Best Practices for Resource Design

-   **Choose Meaningful Types:** Use clear, plural, lowercase (or consistently cased) names for your resource types (e.g., `users`, `blogPosts`, `productOrders`). Consistency is key.
-   **Use the `WithRelationships` Trait:** This trait simplifies defining and memoizing relationships, improving performance and readability.
-   **Keep Resources Focused:** A resource should represent a single, cohesive concept. Avoid creating overly broad resources.
-   **Register Your Resources:** Don't forget to add your resource `type` => `Entity::class` mapping to `config/jsonapi.php`.

By carefully defining your resources, you lay a solid foundation for a clean, efficient, and maintainable JSON:API.