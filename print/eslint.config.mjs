import { includeIgnoreFile } from '@eslint/compat'
import localRules from 'eslint-plugin-local-rules'
import globals from 'globals'
import path from 'node:path'
import { fileURLToPath } from 'node:url'
import js from '@eslint/js'
import { FlatCompat } from '@eslint/eslintrc'

const __filename = fileURLToPath(import.meta.url)
const __dirname = path.dirname(__filename)
const compat = new FlatCompat({
  baseDirectory: __dirname,
  recommendedConfig: js.configs.recommended,
  allConfig: js.configs.all,
})
const gitignorePath = path.resolve(__dirname, '.gitignore')

export default [
  {
    files: ['**/*.ts'],
  },
  ...compat.extends(
    'plugin:vue/vue3-recommended',
    'plugin:vue-scoped-css/vue3-recommended',
    '@nuxt/eslint-config',
    'eslint:recommended',
    'plugin:prettier/recommended'
  ),
  {
    ignores: ['common/**/*', '.nuxt/', '.output/', 'coverage/'],
  },

  includeIgnoreFile(gitignorePath),

  {
    plugins: {
      'local-rules': localRules,
    },

    languageOptions: {
      globals: {
        ...globals.browser,
        ...globals.node,
      },
    },

    rules: {
      'no-undef': 'off',
      'no-console': 'off',
      'prettier/prettier': 'error',
      'prefer-const': 'error',
      'vue/multi-word-component-names': 'off',

      'local-rules/matching-translation-keys': [
        'error',
        {
          ignoreKeysRegex:
            '^(global|entity|contentNode\\.[a-z][a-zA-Z]+|print\\.(global|activity|cover|picasso|program|config|summary|toc))\\..+',
          translationKeyPropRegex: '[a-zA-Z0-9]-i18n-key$',
        },
      ],
    },
  },
]
