import { defineConfig } from 'vitepress'

// https://vitepress.dev/reference/site-config
export default defineConfig({
  lang: 'en-US',
  title: "Laravel Doctrine JSON:API",
  description: "Implement feature-rich JSON:API compliant APIs in your Laravel applications using Doctrine ORM.",

  themeConfig: {
    // https://vitepress.dev/reference/default-theme-config
    nav: [
      {
        text: 'Guide',
        link: '/guide/what-is-jsonapi',
        activeMatch: '^/guide/'
      },
      {
        text: 'Reference',
        link: '/reference/',
        activeMatch: '^/reference/'
      },
    ],

    sidebar: {
      '/guide/': {
        base: '/guide/',
        items: [
          {
            text: 'Introduction',
            collapsed: false,
            items: [
              { text:  'What is JSON:API?', link: '/what-is-jsonapi' },
              { text: 'Getting started', link: '/getting-started' },
              { text: 'Installation', link: '/installation' },
              { text: 'Configuration', link: '/configuration' }
            ]
          },
          {
            text: 'Usage',
            collapsed: false,
            items: [
              { text: 'Routing', link: '/routing' },
              { text: 'Actions', link: '/actions' },
              { text: 'Models', link: '/models' },
              { text: 'Repositories', link: '/repositories' },
              { text: 'Transformers', link: '/transformers' },
              { text: 'Controllers', link: '/controllers' },
              { text: 'Resources', link: '/resources' },
              { text: 'Policies', link: '/policies' },
              { text: 'Requests', link: '/requests' },
              { text: 'Responses', link: '/responses' },
              { text: 'Pagination', link: '/pagination' },
              { text: 'Error Handling', link: '/errors' },
              { text: 'Testing', link: '/testing' }
            ]
          },
          {
            text: 'Advanced',
            collapsed: false,
            items: [
              { text: 'Custom Actions', link: '/custom/actions' },
              { text: 'Custom Controllers', link: '/custom/controllers' },
              { text: 'Custom Transformers', link: '/custom/transformers' },
              { text: 'Custom Requests', link: '/custom/requests' },
              { text: 'Custom Responses', link: '/custom/responses' },
              { text: 'Custom Pagination', link: '/custom/pagination' },
              { text: 'Custom Error Handling', link: '/custom/errors' },
            ]
          },
        ]
      },
      '/reference': {
        base: '/reference/',
        items: [
          { text: 'Introduction', link: '/introduction' },
        ]
      }
    },

    socialLinks: [
      { icon: 'github', link: 'https://github.com/ScholarshipOwl/laravel-doctrine-jsonapi' }
    ]
  }
})
