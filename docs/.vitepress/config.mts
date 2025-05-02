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
          { text: 'Introduction', link: '/guide/' },
          { text: 'Quickstart', link: '/guide/quickstart' },
          { text: 'Installation', link: '/guide/installation' },
          { text: 'Configuration', link: '/guide/configuration' }
        ]
      },
      {
        text: 'Core Concepts',
        items: [
          { text: 'Resources & Entities', link: '/guide/resources' },
          { text: 'Transformers', link: '/guide/transformers' },
          { text: 'Relationships', link: '/guide/relationships' },
          { text: 'Requests & Responses', link: '/guide/requests-responses' }
        ]
      },
      {
        text: 'Guides',
        items: [
          { text: 'Advanced Usage', link: '/guides/advanced' },
          { text: 'Integration', link: '/guides/integration' },
          { text: 'Testing', link: '/guides/testing' },
          { text: 'Troubleshooting', link: '/guides/troubleshooting' }
        ]
      },
      {
        text: 'API Reference',
        items: [
          { text: 'Endpoints', link: '/api/' },
          { text: 'Configuration', link: '/api/configuration' },
          { text: 'Entities', link: '/api/entities' }
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
