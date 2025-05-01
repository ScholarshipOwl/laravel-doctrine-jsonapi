---
# https://vitepress.dev/reference/default-theme-home-page
layout: home

hero:
  name: "Documentation"
  text: "Laravel Doctrine JSON:API"
  tagline: ""
  actions:
    - theme: brand
      text: Why?
      link: /why
    - theme: alt
      text: Quickstart
      link: /quickstart
    - theme: alt
      text: GitHub
      link: https://github.com/ScholarshipOwl/laravel-doctrine-jsonapi
  image:
    src: /images/home4.png
    alt: "JSON:API"

features:
  - title: Standardized API
    # icon: <img src="/images/icons/jsonapi.png" />
    icon: "{.}"
    details: Fully supports JSON:API v1.0, providing standardized requests, responses, errors, and relationships handling.
  - title: Laravel
    icon: <img src="/images/icons/laravel.svg" />
    details: Integrates with Laravel routing, middleware, DI, and service container for seamless developer experience.
  - title: Doctrine ORM
    icon: <img src="/images/icons/doctrine.svg" />
    details: Uses Doctrine ORM for entities, advanced queries, and lifecycle management. No Eloquent dependency.
  - title: AI Friendly
    icon: <img src="/images/icons/ai.svg" />
    details: Includes guides for agentic tools, IDE integration, and automation for intelligent, context-aware API workflows.

---
