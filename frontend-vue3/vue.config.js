export const pluginOptions = {
  jestSerializer: {
    attributesToClear: ['id', 'for'],
    formatting: {
      indent_char: ' ',
      indent_inner_html: true,
      indent_size: 5,
      inline: [],
      sep: '\n',
      wrap_attributes: 'force-aligned',
    },
  },
}

export default {
  devServer: {
    useLocalIp: false,
    allowedHosts: ['ecamp3', 'localhost', '127.0.0.1'],
  },

  transpileDependencies: ['vuetify'],
  pluginOptions,
  css: {
    sourceMap: true,
  },
}
