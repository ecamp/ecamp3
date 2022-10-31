const path = require('path')
const utils = require('eslint-plugin-vue/lib/utils/index.js')
const matchingTranslationKeys = require('../src/common/eslint-local-rules/matchingTranslationKeys.js')

module.exports = {
  'matching-translation-keys': matchingTranslationKeys(path, utils),
}
