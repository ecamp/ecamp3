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
          },
        },
      }),
    },
  },
  plugins: [require('@tailwindcss/typography')],
}
