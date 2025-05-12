# Transformers: Shaping Your API Output

If **Resources** are the "nouns" of your API, then **Transformers** are the skilled artisans that shape how those nouns are presented to the world. They act as a crucial **presentation layer**, taking your internal Doctrine entities and meticulously converting them into the well-structured, JSON:API-compliant format your clients expect.

Transformers are essential for:

-   **Decoupling:** They separate your API's public structure from your internal database schema or entity design. You can refactor your entities without breaking your API contract.
-   **Control & Security:** You decide *exactly* which data fields (`attributes`) and connections (`relationships`) are exposed, preventing accidental leakage of sensitive information.
-   **Consistency:** They ensure all resources of the same type are formatted identically, making your API predictable and reliable.
-   **Formatting & Enrichment:** You can format data (like dates), add computed values, or modify attribute names specifically for the API response.

This package leverages the powerful [Fractal](https://fractal.thephpleague.com/) library for transformations. Understanding how to build effective transformers is key to creating a clean and robust API.

**On this page:**

-   [The Role of Transformers](#the-role-of-transformers)
-   [Core Concepts: Anatomy of a Transformer](#core-concepts-anatomy-of-a-transformer)
-   [Implementing a Transformer](#implementing-a-transformer)
    -   [Basic Example: UserTransformer](#basic-example-usertransformer)
    -   [Example with Relationships & Formatting: BlogPostTransformer](#example-with-relationships--formatting-blogposttransformer)
-   [Including Relationships (Side-Loading)](#including-relationships-side-loading)
-   [Best Practices for Transformers](#best-practices-for-transformers)

---

## The Role of Transformers

A transformer's primary job is to take an input object (your Doctrine entity) and return an array representing its JSON:API `attributes`. It also defines how related resources (relationships) can be included in the response.

For example:
```php
// Inside a hypothetical ProductTransformer
public function transform(Product $product): array
{
    return [
        'sku' => $product->getSku(),
        'name' => $product->getName(),
        'price' => $product->getPrice()->getAmount(), // Assuming a Price object
        'currency' => $product->getPrice()->getCurrency(),
    ];
}
```

## Core Concepts: Anatomy of a Transformer

Transformers in this package typically extend `Sowl\JsonApi\AbstractTransformer` and utilize these key components:

-   **`transform(ResourceInterface $resource)`:** The main method required by the transformer. It receives the resource entity and **must return an array** containing the key-value pairs that will form the `attributes` object in the final JSON:API output.
-   **`$availableIncludes`:** An array property listing the names of relationships that *can* be included if requested by the client via the `?include=` query parameter (e.g., `?include=author,comments`).
-   **`$defaultIncludes`:** An array property listing the names of relationships that should *always* be included in the response, regardless of the `?include=` parameter. Use this sparingly for essential related data.
-   **`include[RelationshipName](ResourceInterface $resource)`:** Methods responsible for handling the inclusion of specific relationships. The method name must follow the pattern `include` + `StudlyCasedRelationshipName` (e.g., `includeAuthor`, `includeComments`). These methods receive the primary resource and should return a `Fractal\Resource\Item` for to-one relationships or `Fractal\Resource\Collection` for to-many relationships, using the appropriate transformer for the related resource.

## Implementing a Transformer

### Basic Example: UserTransformer

Let's start with a simple transformer for our `User` entity.

```php
<?php

namespace App\Transformers;

use App\Entities\User;
use Sowl\JsonApi\AbstractTransformer;
use Sowl\JsonApi\ResourceInterface; // Use interface for type hinting

class UserTransformer extends AbstractTransformer
{
    /**
     * Transforms the User entity into a JSON:API attribute array.
     */
    public function transform(User $user): array
    {
        return [
            'id'         => $user->getId(),
            'name'       => $user->getName(),
            'email'      => $user->getEmail(),
            'createdAt'  => $user->getCreatedAt()?->toISOString(),
            // DO NOT include relationship data here.
        ];
    }

    // Example include method if 'user_profile' relationship exists
    /*
    public function includeUserProfile(User $user): ?\League\Fractal\Resource\Item
    {
        $profile = $user->getProfile();
        return $profile ? $this->item($profile, new UserProfileTransformer(), Profile::getResourceType()) : null;
    }
    */
}
```

### Example with Relationships & Formatting: BlogPostTransformer

Now, a more complex example for a `BlogPost` with `author` (To-One) and `comments` (To-Many) relationships.

```php
<?php

namespace App\Transformers;

use App\Entities\BlogPost;
use App\Entities\User;
use App\Entities\Comment;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Sowl\JsonApi\AbstractTransformer;
use Sowl\JsonApi\ResourceInterface;
use Illuminate\Support\Str; // For summary generation example

class BlogPostTransformer extends AbstractTransformer
{
    /**
     * Relationships that can be included via ?include=
     * Names here correspond to the include methods below (camelCase)
     * and the relationship names defined in the BlogPost entity.
     */
    protected array $availableIncludes = [
        'author',
        'comments',
    ];

    /**
     * Relationships to include by default.
     */
    protected array $defaultIncludes = [
        // 'author', // Perhaps include the author by default?
    ];

    /**
     * Transforms the BlogPost entity into its JSON:API attribute representation.
     */
    public function transform(ResourceInterface $blogPost): array
    {
        /** @var BlogPost $blogPost */
        return [
            'id'        => $blogPost->getId(),
            'title'     => $blogPost->getTitle(),
            'content'   => $blogPost->getContent(),
            // Example: Add a computed attribute (not directly on the entity)
            'summary'   => Str::limit($blogPost->getContent(), 150),
            // Example: Format a DateTime object
            'publishedAt' => $blogPost->getPublishedAt()?->toIso8601String(),
            'createdAt' => $blogPost->getCreatedAt()?->toIso8601String(),
            'updatedAt' => $blogPost->getUpdatedAt()?->toIso8601String(),
        ];
    }

    /**
     * Include the Author (To-One relationship).
     * Method name: include + 'Author' (capitalized from $availableIncludes)
     */
    public function includeAuthor(BlogPost $blogPost): Item
    {
        $author = $blogPost->getAuthor();

        // If no author is associated, return null
        if (is_null($author)) {
             return $this->null();
        }

        // Create a Fractal Item resource for the author, using its transformer
        // The third argument 'users' is the JSON:API type for the related resource.
        return $this->item($author, new UserTransformer(), User::getResourceType());
    }

    /**
     * Include the Comments (To-Many relationship).
     * Method name: include + 'Comments' (capitalized from $availableIncludes)
     */
    public function includeComments(BlogPost $blogPost): Collection
    {
        $comments = $blogPost->getComments();

        // Create a Fractal Collection resource for the comments
        // Use the CommentTransformer and the Comment's JSON:API type.
        return $this->collection($comments, new CommentTransformer(), Comment::getResourceType());
    }
}

```

## Including Relationships (Side-Loading)

When a client requests `GET /api/blogPosts/123?include=author,comments`, the package does the following:

1.  Fetches the `BlogPost` with ID `123`.
2.  Uses `BlogPostTransformer`'s `transform()` method to get the post's `attributes`.
3.  Checks `availableIncludes` and sees `author` and `comments` are requested and available.
4.  Calls `includeAuthor()` on the transformer. This returns a Fractal `Item` resource for the author.
5.  Calls `includeComments()` on the transformer. This returns a Fractal `Collection` resource for the comments.
6.  Fractal compiles everything, including the primary resource data and the "included" author and comment resources, into a single JSON:API response document.

## Best Practices for Transformers

-   **Filter Attributes Carefully:** Only expose the data necessary for your API clients. Avoid exposing internal IDs (other than the main resource `id`), sensitive data (like passwords), or fields irrelevant to the client.
-   **Be Consistent:** Use consistent naming conventions for attributes across different transformers where applicable (e.g., always use `createdAt`, not `created_at` sometimes and `creationDate` others).
-   **Format for the Client:** Format dates, numbers, or other values in a way that is easily consumable by API clients (e.g., ISO 8601 dates).

Transformers are a powerful tool for crafting clean, secure, and maintainable APIs. By mastering them, you gain fine-grained control over your API's presentation layer.