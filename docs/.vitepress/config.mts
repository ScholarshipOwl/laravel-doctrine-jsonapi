import { defineConfig } from 'vitepress'
import { resolve, relative } from 'path'
import { cpSync } from 'fs'

// https://vitepress.dev/reference/site-config
export default defineConfig({
  title: "Laravel Doctrine JSON:API",
  description: "Implement feature-rich [JSON:API](https://jsonapi.org/) compliant APIs in your [Laravel](https://laravel.com/) applications using [Doctrine ORM](https://www.doctrine-project.org/).",
  ignoreDeadLinks: true,
  base: process.env.DOCS_BASE_URL || '', // For the github pages: '/laravel-doctrine-jsonapi/'
  buildEnd: (config) => {
    const src = resolve(config.srcDir, 'images')
    const dest = resolve(config.outDir, 'images')

    const srcRelative = relative(process.cwd(), src)
    const outRelative = relative(process.cwd(), dest)
    console.log(`✓ copying images: ${srcRelative} -> ${outRelative}`)

    cpSync(src, dest, { recursive: true });
  },
  themeConfig: {
    nav: [
      { text: 'Quickstart', link: '/quickstart' },
      { text: 'Guides', link: '/guides/' },
      { text: 'API', link: '/api/' },
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
          { text: 'Resources', link: '/concepts/resources' },
          { text: 'Transformers', link: '/concepts/transformers' },
          { text: 'Relationships', link: '/concepts/relationships' },
          { text: 'Action', link: '/concepts/action' },
          { text: 'Request', link: '/concepts/request' },
          { text: 'Response', link: '/concepts/response' }
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
        text: 'OpenAPI (Scribe)',
        items: [
          { text: 'Generation', link: '/openapi/' },
          { text: 'Attributes', link: '/openapi/attributes' },
          { text: 'Configuration', link: '/openapi/configurations' },
          { text: 'Strategies', link: '/openapi/strategies' },
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
      }
    ],

    socialLinks: [
      { icon: 'github', link: 'https://github.com/ScholarshipOwl/laravel-doctrine-jsonapi' }
    ]
  },
})
