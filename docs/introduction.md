<p align="center">
  <img src="./images/introduction/hello.png" alt="Hello" />
</p>

**Laravel Doctrine JSON:API** is your go-to package for building robust JSON:API endpoints in [Laravel](https://laravel.com/), using the power and flexibility of [Doctrine](https://www.doctrine-project.org/) for data persistence. This package is built first and foremost on top of Laravel, embracing its modern framework features and developer-friendly experience. Doctrine ORM is seamlessly integrated to handle your data layer, and we make use of the [Laravel Doctrine](https://github.com/laravel-doctrine/orm) package under the hood—though the real magic comes from combining Laravel's strengths with Doctrine's advanced ORM capabilities.

## Why This Stack? 🤔

Choosing the right stack is a big deal! Let me walk you through why this combination might be exactly what you need.

<p align="left" style="margin-top: 4rem; margin-bottom: 0;">
  <img src="./images/introduction/why_laravel.png" alt="Why Laravel?" />
</p>

### Why Laravel?

Laravel is a modern PHP framework that makes building web applications enjoyable and productive. Its codebase is clean, the documentation is top-notch, and the community is welcoming and always ready to help. While Laravel is primarily designed for web applications, it also offers a suite of features that make API server development straightforward.

From validation and authentication to authorization and simple route handlers, Laravel empowers you to build secure and maintainable APIs. If you come from a more traditional PHP background, Laravel is the perfect opportunity to embrace a new way of thinking and modernize your development approach.

<p align="left" style="margin-top: 4rem; margin-bottom: 0;">
  <img src="./images/introduction/why_doctrine.png" alt="Why Doctrine ORM?" />
</p>

### Why Doctrine ORM?

Doctrine ORM is a mature and highly respected data mapper in the PHP ecosystem. It's been around for a long time and continues to receive strong community support. Doctrine's data mapper pattern is particularly well-suited for large-scale applications, offering a level of flexibility and separation that Active Record (as used in Laravel's Eloquent ORM) sometimes struggles to provide.

Since many of Laravel's features are ORM-agnostic, integrating Doctrine into your Laravel app is both feasible and powerful, giving you the best of both worlds.

<p align="left" style="margin-top: 4rem; margin-bottom: 0;">
  <img src="./images/introduction/why_jsonapi.png" alt="Why JSON:API?" />
</p>

### Why JSON:API?

Building a RESTful API is a proven approach for creating application backends that are easy to integrate with any web technology. [JSON:API](https://jsonapi.org/) takes this further by standardizing how resources are represented, eliminating the need to invent your own conventions.

This makes your API predictable and easier to consume, whether by your own frontend or by third-party tools. Plus, adhering to the JSON:API specification means you can take advantage of a wide range of open-source libraries and integrations, accelerating your development process.

## Is This For You?

This package is ideal if you're looking to build an API server designed for longevity—think a lifespan of 10 years or more.

It's also a great fit if you're already comfortable with Doctrine ORM and want to leverage its strengths within a Laravel application. By combining Laravel's modern features with Doctrine's robust data mapping, you can create a future-proof, maintainable API server.

If that sounds like your use case, Laravel Doctrine JSON:API is an excellent alternative. Let's build something awesome together! 🚀

#### Alternatives

  * [Laravel JSON:API](https://laraveljsonapi.io/) - A Laravel package for building JSON:API-compliant APIs using Laravel and Eloquent ORM.
