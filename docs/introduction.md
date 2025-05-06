<p align="center">
  <img src="./images/introduction/hello2.png" alt="Hello" style="width: 100%; border-top-left-radius: 2rem; border-top-right-radius: 2rem;" />
</p>

**Laravel Doctrine JSON:API** is your go-to package for building robust JSON:API endpoints in [Laravel](https://laravel.com/), using the power and flexibility of [Doctrine](https://www.doctrine-project.org/) for data persistence. This package is built first and foremost on top of Laravel, embracing its modern framework features and developer-friendly experience. Doctrine ORM is seamlessly integrated to handle your data layer, and we make use of the [Laravel Doctrine](https://github.com/laravel-doctrine/orm) package under the hood—though the real magic comes from combining Laravel's strengths with Doctrine's advanced ORM capabilities.

## Why This Stack? 🤔

Choosing the right stack is a big deal! Let me walk you through why this combination might be exactly what you need.

<p align="left" style="margin-top: 4rem; margin-bottom: 0;">
  <img src="./images/introduction/why_laravel.png" alt="Why Laravel?" />
</p>

### Why Laravel?

[Laravel](https://laravel.com/) is a modern PHP framework that makes building web applications enjoyable and productive. Its codebase is clean, the documentation is top-notch, and the community is welcoming and always ready to help. While Laravel is primarily designed for web applications, it also offers a suite of features that make API server development straightforward.

Using a well-established framework to build your Application API is crucial for long-term success. Frameworks provide a strong foundation of tested, secure, and maintainable components—handling everything from routing and middleware to validation, authentication, and error handling. This not only accelerates development but also ensures your API follows industry best practices, is easier to maintain, and can scale as your project grows. By relying on a framework, you avoid reinventing the wheel and benefit from the collective expertise of the developer community.

From validation and authentication to authorization and simple route handlers, Laravel empowers you to build secure and maintainable APIs. If you come from a more traditional PHP background, Laravel is the perfect opportunity to embrace a new way of thinking and modernize your development approach.

<p align="left" style="margin-top: 4rem; margin-bottom: 0;">
  <img src="./images/introduction/why_doctrine.png" alt="Why Doctrine ORM?" />
</p>

### Why Doctrine ORM?

Before diving into the specifics of Doctrine ORM, it's important to understand why databases and persistence matter in API development. Most application APIs are designed to manage, store, and retrieve data—whether that's user information, business records, or content. A robust database layer ensures that your application's data is reliable, consistent, and secure over time. The framework you choose to interact with your database directly impacts how maintainable, scalable, and safe your application will be. This is where Object-Relational Mappers (ORMs) come in: they bridge the gap between your application's code and the underlying database, allowing you to work with data as objects instead of raw SQL. A good ORM abstracts away the complexities of database interactions, reduces boilerplate, and helps enforce best practices for data integrity and security.

[Doctrine ORM](https://www.doctrine-project.org/) is a mature and highly respected data mapper in the PHP ecosystem. It's been around for a long time and continues to receive strong community support. Doctrine's data mapper pattern is particularly well-suited for large-scale applications, offering a level of flexibility and separation that Active Record (as used in Laravel's Eloquent ORM) sometimes struggles to provide.

Since many of Laravel's features are ORM-agnostic, integrating Doctrine into your Laravel app is both feasible and powerful, giving you the best of both worlds.

<p align="left" style="margin-top: 4rem; margin-bottom: 0;">
  <img src="./images/introduction/why_jsonapi.png" alt="Why JSON:API?" />
</p>

### Why JSON:API?

Building a RESTful API is a proven approach for creating application backends that are easy to integrate with any web technology. [JSON:API](https://jsonapi.org/) takes this further by standardizing how resources are represented, eliminating the need to invent your own conventions.

Using established standards in API development is critical for long-term success. Standards foster interoperability, making it easier for different systems and teams to work together without confusion. They reduce ambiguity, lower the learning curve for new developers, and allow you to leverage a broad ecosystem of tools, libraries, and best practices. By following a widely adopted specification like JSON:API, you ensure your API is predictable, future-proof, and easy to integrate with other services and clients.

This makes your API predictable and easier to consume, whether by your own frontend or by third-party tools. Plus, adhering to the JSON:API specification means you can take advantage of a wide range of open-source libraries and integrations, accelerating your development process.


## What you get

With Laravel Doctrine JSON:API, you unlock a set of powerful features designed to help you build modern, maintainable, and standards-compliant APIs with ease:

- **JSON:API Compliance:** Effortlessly build APIs that adhere to the [JSON:API](https://jsonapi.org/) specification, ensuring consistency and interoperability across clients and services.

- **Laravel:** Fully compatible with modern [Laravel](https://laravel.com/) versions, following best practices and leveraging the latest framework features.

- **Doctrine ORM:** Leverage the power and flexibility of [Doctrine ORM](https://www.doctrine-project.org/) for advanced data mapping and database abstraction, while enjoying seamless integration with Laravel's ecosystem.

- **Documentation:** Generate beautiful, up-to-date API documentation automatically with [Scribe](https://scribe.knuckles.wtf/laravel/), including support for JSON:API-specific features and relationships.

- **Authorization:** Integrate with [Laravel's authorization system](https://laravel.com/docs/authorization) to enforce fine-grained access control for your API resources. [Policies](./guides/policies.md) are easy to set up and work out-of-the-box with Doctrine entities. RBAC is supported.

- **Automatic Routing:** Default RESTful endpoints are defined for you out of the box—including [related](https://jsonapi.org/format/#fetching-relationships) and [relationships](https://jsonapi.org/format/#document-resource-object-relationships) endpoints as described in the JSON:API specification. You can further customize or extend these routes to fit your application's needs, but the essentials are ready without any manual route registration.

- **First-Class Testing Utilities:** Use built-in traits to simplify testing with Doctrine ORM in Laravel, including database refresh, truncation, and assertion helpers compatible with [Laravel's testing tools](https://laravel.com/docs/testing).

All of this is provided with a focus on developer experience, maintainability, and future-proofing your API projects. Whether you're building a greenfield application or migrating a legacy system, Laravel Doctrine JSON:API gives you the tools to move fast and build right.

## Is This For You?

This package is ideal if you're looking to build an API server designed for longevity—think a lifespan of 10 years or more.

It's also a great fit if you're already comfortable with Doctrine ORM and want to leverage its strengths within a Laravel application. By combining Laravel's modern features with Doctrine's robust data mapping, you can create a future-proof, maintainable API server.

If that sounds like your use case, Laravel Doctrine JSON:API is an excellent alternative. Let's build something awesome together! 🚀

#### Alternatives

  * [Laravel JSON:API](https://laraveljsonapi.io/) - A Laravel package for building JSON:API-compliant APIs using Laravel and Eloquent ORM.