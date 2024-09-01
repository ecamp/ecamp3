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
  ...compat.extends(
    'plugin:vue/vue3-recommended',
    'eslint:recommended',
    'plugin:prettier/recommended',
    '@vue/eslint-config-prettier'
  ),
  {
    ignores: ['dist/*.mjs'],
  },

  includeIgnoreFile(gitignorePath),

  {
    plugins: {
      'local-rules': localRules,
    },

    languageOptions: {
      globals: {
        ...globals.node,
        ...globals.jest,
      },

      parserOptions: {
        parser: '@babel/eslint-parser',
      },
    },

    rules: {
      'prefer-const': 'error',
      'prettier/prettier': 'error',

      'vue/component-tags-order': [
        'error',
        {
          order: ['template', 'script', 'style'],
        },
      ],

      'vue/multi-word-component-names': 'off',
      'vue/valid-v-for': 'off',
      'vue/no-reserved-component-names': 'off',

      'vue/no-unused-vars': [
        'error',
        {
          ignorePattern: '^_',
        },
      ],

      'no-unused-vars': [
        'error',
        {
          argsIgnorePattern: '^_$',
        },
      ],

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
