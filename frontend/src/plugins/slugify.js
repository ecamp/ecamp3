import slugify from 'slugify'

slugify.extend({
  '@': '(at)',
  '.': ' ',
  ':': ' ',
})

export { slugify }
