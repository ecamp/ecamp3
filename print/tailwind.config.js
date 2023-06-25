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
          },
        },
      }),
    },
  },
  plugins: [require('@tailwindcss/typography')],
}
