# Relationships

Imagine building an application where data is interconnected – a blog post has an author, an order belongs to a customer, and a user can have multiple roles. How do you represent these connections in your API cleanly and efficiently? This is where **JSON:API Relationships** shine.

Relationships are the **connective tissue** of your API, defining how different pieces of data (resources) link together. By embracing the JSON:API standard for relationships, you're not just linking data; you're building a **smarter, more intuitive, and highly efficient API**.

**On this page:**

-   [Why Embrace JSON:API Relationships?](#why-embrace-jsonapi-relationships)
-   [Core Concepts: The Building Blocks](#core-concepts-the-building-blocks)
-   [Real-World Examples: User Roles Relationships](#real-world-examples-user-roles-relationships)
    -   [Fetching Relationship Linkage (`.../relationships/roles`)](#fetching-relationship-linkage-relationshipsroles)
    -   [Fetching Related Resources (`.../roles`)](#fetching-related-resources-roles)
    -   [Adding to a To-Many Relationship (`POST .../relationships/roles`)](#adding-to-a-to-many-relationship-post-relationshipsroles)
    -   [Replacing a Relationship (`PATCH .../relationships/roles`)](#replacing-a-relationship-patch-relationshipsroles)
    -   [Removing from a To-Many Relationship (`DELETE .../relationships/roles`)](#removing-from-a-to-many-relationship-delete-relationshipsroles)
    -   [Managing a To-One Relationship (Example: User Profile's User)](#managing-a-to-one-relationship-example-user-profiles-user)
-   [Best Practices for Smooth Sailing](#best-practices-for-smooth-sailing)

---

## Why Embrace JSON:API Relationships?

Implementing relationships correctly unlocks a cascade of benefits, transforming how clients interact with your API and simplifying your backend logic:

-   **Fetch Data Intelligently:** Say goodbye to multiple, slow API calls! Clients can request related data (like a post *and* its author) in a single go using the `include` parameter. This drastically reduces network latency and speeds up applications.
-   **Build Consistent & Predictable APIs:** Relationships follow a clear, standardized structure. This predictability makes your API easier for developers to understand, consume, and integrate with, fostering a better developer experience.
-   **Navigate Your Data Graph:** The API itself becomes self-documenting regarding connections. Standardized links (`self` and `related`) allow clients to discover and traverse the relationships between resources effortlessly.
-   **Manage Connections with Precision:** Need to change a post's author or assign new roles to a user? JSON:API provides standard endpoints (`/relationships/...`) to modify these connections directly, without needing to fetch or update the entire resources involved. This is efficient and less error-prone.
-   **Decouple and Future-Proof:** By treating relationships as distinct from resource attributes, you gain flexibility. You can evolve your data model and its connections independently, making your API more resilient to future changes.
-   **Tap into a Rich Ecosystem:** Leverage a wide array of existing tools, libraries, and client SDKs built for JSON:API. Conforming to the standard means less boilerplate and faster development cycles.

By mastering relationships, you empower your API clients, streamline data management, and build applications that are both powerful and elegant.

## Core Concepts: The Building Blocks

Understanding relationships boils down to a few key ideas:

-   **Resource Linkage:** At its heart, a relationship defines a link between a primary resource (e.g., a `User`) and one or more related resources (e.g., their `Role` or `Roles`).
-   **Relationship Types:**
    -   **To-One:** Links a resource to *at most one* other resource (e.g., a `Comment` belongs to *one* `User`).
    -   **To-Many:** Links a resource to a collection of *zero or more* other resources (e.g., a `User` can have *many* `Roles`).
-   **Relationship Endpoints:** This package can automatically create dedicated URLs (like `/users/{id}/relationships/roles`) specifically for viewing and managing these links.
-   **Linkage Data:** The core of a relationship in the JSON payload is the "linkage" data – simple objects containing just the `type` and `id` of the related resource(s). This is distinct from fetching the full related resource data.
-   **Included Resources:** While relationship endpoints manage *links*, you can use the `include` query parameter on primary resource endpoints (like `GET /users/{id}?include=roles`) to fetch the primary resource *and* its related resources in one efficient request. In your [Transformers](/concepts/transformers), these are handled via the `includes` definition.

## Real-World Examples: User Roles Relationships

Let's see JSON:API relationships in action! These examples, based on the package's test suite, show how you can manage the connection between a `User` and their assigned `Roles`. Understanding these patterns will help you build dynamic and flexible features.

### Fetching Relationship Linkage (`.../relationships/roles`)

**When to use it:** You need to know *which* roles are connected to a user, but you don't need the full details (like the role names) right now. This is a lightweight way to check associations.

**Think:** "Just give me the IDs of the roles linked to this user."

**Request:**
```http
GET /api/users/{userId}/relationships/roles
```

**Response:** Shows linkage data for all connected roles.
```json
{
  "data": [
    { "id": "1", "type": "roles", "links": { "self": "/api/roles/1" } },
    { "id": "2", "type": "roles", "links": { "self": "/api/roles/2" } }
  ]
}
```

### Fetching Related Resources (`.../roles`)

**When to use it:** You need the *full details* (attributes, etc.) of the roles associated with a user, not just their IDs.

**Think:** "Show me the full information for all roles linked to this user."

**Request:**
```http
GET /api/users/{userId}/roles
```

**Response:** Contains full resource objects for each role.
```json
{
  "data": [
    {
      "id": "1", "type": "roles",
      "attributes": { "name": "Root" },
      "links": { "self": "/api/roles/1" }
    },
    {
      "id": "2", "type": "roles",
      "attributes": { "name": "User" },
      "links": { "self": "/api/roles/2" }
    }
    /* ... potentially more roles ... */
  ]
}
```
> **Pro Tip:** You can often achieve a similar result more efficiently by requesting the primary resource and including its relationships, e.g., `GET /api/users/{userId}?include=roles`. This fetches the user and their roles in a single request.

### Adding to a To-Many Relationship (`POST .../relationships/roles`)

**When to use it:** You want to link an *additional* role to a user without affecting their existing roles.

**Think:** "Grant this user the 'Moderator' role in addition to any they already have."

**Request:** Provide the linkage data for the role(s) to add.
```http
POST /api/users/{userId}/relationships/roles

{
  "data": [ { "type": "roles", "id": "3" } ] /* Add role with ID '3' */
}
```
**Response:** Shows the *complete* set of relationship linkages after the addition.
```json
{
  "data": [
    { "id": "2", "type": "roles", "links": { "self": "/api/roles/2" } }, /* Existing role */
    { "id": "3", "type": "roles", "links": { "self": "/api/roles/3" } }  /* Newly added role */
  ]
}
```

### Replacing a Relationship (`PATCH .../relationships/roles`)

**When to use it:** You want to set the *exact* list of roles for a user, removing any current roles and replacing them with the specified set. (For To-One relationships, use PATCH to link a different resource or set it to `null`).

**Think:** "Set this user's roles to *only* be 'User'. Remove all others."

**Request:** Provide the linkage data for the *complete* new set of roles.
```http
PATCH /api/users/{userId}/relationships/roles

{
  "data": [ { "type": "roles", "id": "2" } ] /* Set roles to only ID '2' */
}
```
**Response:** Shows the relationship linkage reflecting the change.
```json
{
  "data": [
    { "id": "2", "type": "roles", "links": { "self": "/api/roles/2" } }
  ]
}
```

### Removing from a To-Many Relationship (`DELETE .../relationships/roles`)

**When to use it:** You want to remove a *specific* role link from a user without affecting their other roles. The role resource itself isn't deleted, just the association.

**Think:** "Revoke the 'Root' role from this user, but leave their other roles intact."

**Request:** Provide the linkage data for the role(s) to remove.
```http
DELETE /api/users/{userId}/relationships/roles

{
  "data": [ { "type": "roles", "id": "1" } ] /* Remove role with ID '1' */
}
```
**Response:** Shows the remaining relationship linkages after the removal.
```json
{
  "data": [
    { "id": "2", "type": "roles", "links": { "self": "/api/roles/2" } } /* The other role remains */
  ]
}
```

### Managing a To-One Relationship (Example: User Profile's User)

Now, let's look at a **To-One** relationship. Imagine each `UserProfile` is linked to exactly one `User`.

#### Fetching To-One Relationship Linkage (`.../relationships/user`)

**When to use it:** You need to know *which* user is linked to this profile, but not their full details.

**Think:** "What's the ID of the user associated with this profile?"

**Request:**
```http
GET /api/userProfiles/{profileId}/relationships/user
```
**Response:** Shows linkage data for the single related user, or `null` if none is linked.
```json
{
  "data": { "id": "8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b", "type": "users" },
  "links": {
    "self": "/api/userProfiles/{profileId}/relationships/user",
    "related": "/api/userProfiles/{profileId}/user"
  }
}
```
*Response (If no user linked):*
```json
{
  "data": null,
  "links": { /* ... links ... */ }
}
```

#### Fetching the Related To-One Resource (`.../user`)

**When to use it:** You need the *full details* of the user linked to this profile.

**Think:** "Show me the full user resource for this profile."

**Request:**
```http
GET /api/userProfiles/{profileId}/user
```
**Response:** Contains the full resource object for the linked user.
```json
{
  "data": {
    "id": "8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b",
    "type": "users",
    "attributes": {
      "email": "test1email@test.com",
      "name": "testing user1"
    },
    "links": { /* ... links ... */ }
    /* ... other relationships potentially ... */
  }
}
```

#### Updating/Replacing a To-One Relationship (`PATCH .../relationships/user`)

**When to use it:** You want to change which user is linked to the profile, or link one if none was previously linked.

**Think:** "Associate this profile with user 'f1d2f365...' instead."

**Request:** Provide the linkage data for the *new* user.
```http
PATCH /api/userProfiles/{profileId}/relationships/user

{
  "data": { "type": "users", "id": "f1d2f365-e9aa-4844-8eb7-36e0df7a396d" }
}
```
**Response:** Typically a `204 No Content` on success, or potentially the updated relationship linkage.

#### Clearing a To-One Relationship (`PATCH .../relationships/user`)

**When to use it:** You want to remove the association between the profile and its user (unlink them).

**Think:** "Disassociate any user from this profile."

**Request:** Provide `null` as the data.
```http
PATCH /api/userProfiles/{profileId}/relationships/user

{
  "data": null
}
```
**Response:** Typically a `204 No Content` on success.

---

These patterns provide a powerful and standardized way to manage the intricate connections within your application's data.

## Best Practices for Smooth Sailing

Keep these pointers in mind as you work with relationships to build robust and maintainable APIs:

-   **Define Connections Clearly:** Map out your data connections explicitly. Ensure relationships are well-defined in your Doctrine entities and corresponding JSON:API [Transformers](/concepts/transformers). This clarity prevents confusion and makes your API predictable.
-   **Leverage the Package's Power:** Utilize the built-in helpers, conventions, and automatic endpoint generation provided by this package. It handles much of the JSON:API complexity, saving you time and ensuring standard compliance.
-   **Fetch Smartly, Not Excessively:** While `include` is great for reducing requests, be mindful of performance. Avoid requesting deeply nested relationships (e.g., `?include=a.b.c.d`) or including large collections unless truly necessary. Design your endpoints and client requests to fetch only the data needed for a given view or action.
-   **Understand Endpoint Nuances:** Know *when* to use the relationship endpoint (`.../relationships/`) for managing links versus the related resource endpoint (`.../roles`) or compound documents (`?include=roles`) for fetching full resource data. Choosing the right approach optimizes performance and clarity.

Mastering relationships is a cornerstone of building flexible, efficient, and developer-friendly APIs with JSON:API. Embrace them to elevate your application's data interactions!