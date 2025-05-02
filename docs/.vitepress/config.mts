import { defineConfig } from 'vitepress'

// https://vitepress.dev/reference/site-config
export default defineConfig({
  title: "Laravel Doctrine JSON:API",
  description: "Implement feature-rich [JSON:API](https://jsonapi.org/) compliant APIs in your [Laravel](https://laravel.com/) applications using [Doctrine ORM](https://www.doctrine-project.org/).",
  themeConfig: {
    nav: [
      { text: 'Guide', link: '/guide/' },
      { text: 'API', link: '/api/' },
      { text: 'Examples', link: '/examples/' },
      { text: 'Why', link: '/why' },
    ],
    sidebar: [
      {
        text: 'Getting Started',
        items: [
          { text: 'Introduction', link: '/introduction' },
          { text: 'Installation', link: '/installation' },
          { text: 'Configuration', link: '/configuration' },
          { text: 'Quickstart', link: '/quickstart' },
        ]
      },
      {
        text: 'Concepts',
        items: [
          { text: 'Resources & Entities', link: '/concepts/resources' },
          { text: 'Transformers', link: '/concepts/transformers' },
          { text: 'Relationships', link: '/concepts/relationships' },
          { text: 'Requests & Responses', link: '/concepts/requests-responses' }
        ]
      },
      {
        text: 'OpenAPI (Scribe)',
        items: [
          { text: 'Generation', link: '/openapi' },
          { text: 'Attributes', link: '/openapi/attributes' },
          { text: 'Configuration', link: '/openapi/configurations' },
          { text: 'Strategies', link: '/openapi/strategies' },
        ]
      },
      {
        text: 'Guides',
        items: [
          { text: 'Default Controller', link: '/guides/defaultcontroller' },
          { text: 'Resource Interface', link: '/guides/resourceInterface' },
          { text: 'Validation', link: '/guides/validation' },
          { text: 'Filters', link: '/guides/filters' },
          { text: 'Policies', link: '/guides/policies' },
          { text: 'Meta', link: '/guides/meta' },
          { text: 'Factories', link: '/guides/factories' },
          { text: 'Testing', link: '/guides/testing' },
          { text: 'Troubleshooting', link: '/guides/troubleshooting' },
        ]
      },
      {
        text: 'API Reference',
        items: [
          { text: 'Resource Manager', link: '/api/ResourceManager' },
          { text: 'Resource Interface', link: '/api/ResourceInterface' },
          { text: 'Abstract Action', link: '/api/AbstractAction' },
          { text: 'Abstract Transformer', link: '/api/AbstractTransformer' },
          { text: 'Authorize Middleware', link: '/api/AuthorizeMiddleware' },
          { text: 'Filter Parsers', link: '/api/FilterParsers' },
        ]
      },
      {
        text: 'Examples',
        items: [
          { text: 'Basic Example', link: '/examples/basic' },
          { text: 'Real-World Example', link: '/examples/real-world' }
        ]
      }
    ],

    socialLinks: [
      { icon: 'github', link: 'https://github.com/ScholarshipOwl/laravel-doctrine-jsonapi' }
    ]
  }
})
