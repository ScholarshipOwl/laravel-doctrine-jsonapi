# Quickstart

You can create a new project using the following command:

```bash
composer create-project sowl/laravel-doctrine-jsonapi-skeleton jsonapi

cd jsonapi
```

After that, you can run the following command to start the development server:

```bash
composer run dev
```

You can now access the API at `http://localhost:8000`.

It's comes with a simple authentication system and a user entity.

And RBAC authorization system, we predefined roles.

## OpenAPI

API documentation is available at [http://localhost:8000/docs](http://localhost:8000/docs) it's served by [Scalar.](https://scalar.com/).

![Scalar](./images/quickstart/scalar.png)

You can start interacting with the API using the Test Request functionality.

  * [Register](http://localhost:8000/docs#tag/users/POST/users)
  * [Login](http://localhost:8000/docs#tag/auth/POST/auth/login)
  * [Fetch authenticated user](http://localhost:8000/docs#tag/users/GET/users/me)

## Continue

You can continue reading the [Authentication](authentication.md) section.

Start creating your own entities run migrations, controllers and default routes available from the skeleton.
