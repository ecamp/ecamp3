module.exports = {
  prefix: 'tw-',
  content: [
    './components/**/*.{js,vue,ts}',
    './layouts/**/*.vue',
    './pages/**/*.vue',
    './plugins/**/*.{js,ts}',
    './nuxt.config.{js,ts}',
  ],
  theme: {
    extend: {
      typography: () => ({
        DEFAULT: {
          css: {
            color: '#000',
            p: {
              marginTop: '0!important',
              marginBottom: '0.3em!important',
              minHeight: '0.6em!important',
            },
            ol: {
              marginTop: 0,
              marginBottom: '0.3em!important',
              paddingLeft: '1.125em!important',
            },
            ul: {
              marginTop: 0,
              marginBottom: '0.3em!important',
              paddingLeft: '1.2em!important',
            },
            li: {
              marginTop: '0!important',
              marginBottom: '0.3em!important',
            },
            'ol > li': {
              paddingLeft: '0.2em',
            },
            'ul > li': {
              paddingLeft: '0',
            },
            'ul ul, ul ol, ol ul, ol ol': {
              marginTop: '0',
              marginBottom: '0.5em',
            },
            '--tw-prose-counters': 'black',
            '--tw-prose-bullets': 'black',
          },
        },
        neutral: {
          css: {
            '--tw-prose-counters': 'black',
            '--tw-prose-bullets': 'black',
          },
        },
      }),
    },
  },
  plugins: [require('@tailwindcss/typography')],
}
