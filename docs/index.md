---
# https://vitepress.dev/reference/default-theme-home-page
layout: home

hero:
  name: "Documentation"
  text: "Laravel Doctrine JSON:API"
  tagline: ""
  actions:
    - theme: brand
      text: Introduction
      link: /introduction
    - theme: alt
      text: Quickstart
      link: /quickstart
    - theme: alt
      text: GitHub
      link: https://github.com/ScholarshipOwl/laravel-doctrine-jsonapi
  image:
    src: images/home4.png
    alt: "JSON:API"

features:
  - title: Standardized API
    # icon: <img src="images/icons/jsonapi.png" />
    icon: "{.}"
    details: Fully supports JSON:API v1.0, providing standardized requests, responses, errors, and relationships handling.
  - title: Laravel
    icon: <svg width="32" height="32" viewBox="0 0 50 52" xmlns="http://www.w3.org/2000/svg"><path d="M49.626 11.564a.809.809 0 0 1 .028.209v10.972a.8.8 0 0 1-.402.694l-9.209 5.302V39.25c0 .286-.152.55-.4.694L20.42 51.01c-.044.025-.092.041-.14.058-.018.006-.035.017-.054.022a.805.805 0 0 1-.41 0c-.022-.006-.042-.018-.063-.026-.044-.016-.09-.03-.132-.054L.402 39.944A.801.801 0 0 1 0 39.25V6.334c0-.072.01-.142.028-.21.006-.023.02-.044.028-.067.015-.042.029-.085.051-.124.015-.026.037-.047.055-.071.023-.032.044-.065.071-.093.023-.023.053-.04.079-.06.029-.024.055-.05.088-.069h.001l9.61-5.533a.802.802 0 0 1 .8 0l9.61 5.533h.002c.032.02.059.045.088.068.026.02.055.038.078.06.028.029.048.062.072.094.017.024.04.045.054.071.023.04.036.082.052.124.008.023.022.044.028.068a.809.809 0 0 1 .028.209v20.559l8.008-4.611v-10.51c0-.07.01-.141.028-.208.007-.024.02-.045.028-.068.016-.042.03-.085.052-.124.015-.026.037-.047.054-.071.024-.032.044-.065.072-.093.023-.023.052-.04.078-.06.03-.024.056-.05.088-.069h.001l9.611-5.533a.801.801 0 0 1 .8 0l9.61 5.533c.034.02.06.045.09.068.025.02.054.038.077.06.028.029.048.062.072.094.018.024.04.045.054.071.023.039.036.082.052.124.009.023.022.044.028.068zm-1.574 10.718v-9.124l-3.363 1.936-4.646 2.675v9.124l8.01-4.611zm-9.61 16.505v-9.13l-4.57 2.61-13.05 7.448v9.216l17.62-10.144zM1.602 7.719v31.068L19.22 48.93v-9.214l-9.204-5.209-.003-.002-.004-.002c-.031-.018-.057-.044-.086-.066-.025-.02-.054-.036-.076-.058l-.002-.003c-.026-.025-.044-.056-.066-.084-.02-.027-.044-.05-.06-.078l-.001-.003c-.018-.03-.029-.066-.042-.1-.013-.03-.03-.058-.038-.09v-.001c-.01-.038-.012-.078-.016-.117-.004-.03-.012-.06-.012-.09v-.002-21.481L4.965 9.654 1.602 7.72zm8.81-5.994L2.405 6.334l8.005 4.609 8.006-4.61-8.006-4.608zm4.164 28.764l4.645-2.674V7.719l-3.363 1.936-4.646 2.675v20.096l3.364-1.937zM39.243 7.164l-8.006 4.609 8.006 4.609 8.005-4.61-8.005-4.608zm-.801 10.605l-4.646-2.675-3.363-1.936v9.124l4.645 2.674 3.364 1.937v-9.124zM20.02 38.33l11.743-6.704 5.87-3.35-8-4.606-9.211 5.303-8.395 4.833 7.993 4.524z" fill="currentColor" fill-rule="evenodd"/></svg>
    details: Integrates with Laravel routing, middleware, DI, and service container for seamless developer experience.
  - title: Doctrine ORM
    icon: <svg width="38px" height="38px" viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M22.325 12.138c-0.005-0.004-0.009-0.008-0.009-0.008-0.051-0.064-0.107-0.124-0.163-0.18l-7.698-7.698c-0.919-0.919-2.408-0.919-3.328 0s-0.919 2.408 0 3.328l2.485 2.485c-4.161 1.056-7.236 4.829-7.236 9.323 0 5.316 4.307 9.623 9.623 9.623s9.623-4.307 9.623-9.623c-0-2.891-1.275-5.487-3.297-7.249zM22.129 20.811l-5.56 5.56c-0.334 0.333-0.774 0.5-1.21 0.5s-0.877-0.167-1.21-0.5c-0.667-0.667-0.667-1.753 0-2.421l2.639-2.639h-5.705c-0.945 0-1.711-0.766-1.711-1.711s0.766-1.711 1.711-1.711h5.705l-2.639-2.639c-0.667-0.667-0.667-1.753 0-2.421s1.754-0.667 2.421 0l5.56 5.56c0.667 0.667 0.667 1.753 0 2.421z"></path></svg>
    details: Uses Doctrine ORM for entities, advanced queries, and lifecycle management. No Eloquent dependency.
  - title: AI Friendly
    icon: <svg width="32" height="32" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M16 4 L18 14 L28 16 L18 18 L16 28 L14 18 L4 16 L14 14 Z"/><path fill="currentColor" d="M7 7 L7.7 9.3 L10 10 L7.7 10.7 L7 13 L6.3 10.7 L4 10 L6.3 9.3 Z"/><path fill="currentColor" d="M25 25 L25.5 26.5 L27 27 L25.5 27.5 L25 29 L24.5 27.5 L23 27 L24.5 26.5 Z"/></svg>
    details: Includes guides for agentic tools, IDE integration, and automation for intelligent, context-aware API workflows.

---
