# Introduction

Laravel Doctrine JSON:API is a package for the Laravel that allows developers to create JSON:API endpoints using the Doctrine ORM for data persistence.

It's built on top of [Laravel Doctrine](https://github.com/laravel-doctrine/orm) and [Laravel JSON:API](https://github.com/cloudcreativity/laravel-json-api).

## Why?
In this paraghraph we explain why and for whom suited this library.
The proposed stack is special and maybe not suited for all the possible needs.

### Why Laravel ?

  * Laravel is modern PHP Framework suited for building web applications. It's codebase and documentation including community is very friendly and easy to get started with.
  * But Laravel designed for building first of all web applications but not Application API servers that gonna have to work with different database and expose only API.
  * Laravel is not the best choice for building API servers.
  * But it's great framework having great features that suites for building API servers, validation, authentication, authorization!, simple route handlers, and of course laravel allows oldschool PHP developers to start thinking a new way.

### Why Doctrine ORM?

  * Doctrine ORM is the very good, long live ORM project with a lot of support 
  * Doctrine is Data Mapper and there are opinions that it's more suited for big application development than Active record used in default eloquent laravel ORM.
  * Many of laravel features are ORM agnostic so Doctrine can be easily

### Why JSON:API?

  * Building a RESTful API is a great approach for building Application APIs allowing using maximum of HTTP technology and integration into any web technologies.
  * JSON:API standartizing the approach for building the RESTful API, so there are no need to define the standard of resources schema allowing simples integration into any system.
  * Allows using external Open Source libraries with the standard.


### Alternatives
You should consider looking for some alternatives if the proposed stack

  * [https://laraveljsonapi.io/](https://laraveljsonapi.io/) - Build JSON:API APIs with Laravel and Eloguent ORM.

## Use Cases
  * You want to build Application API server that gonna live for minimum 10 years and more.
  * You need to build JSON:API server you familiar with Doctrine ORM and know exactly what you're doing. ( Laravel gonna help you to start thinking a new way and gonna help build the API with this library.