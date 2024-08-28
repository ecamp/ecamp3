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
    'plugin:vue/recommended',
    'plugin:vue/vue3-recommended',
    'plugin:vue-scoped-css/vue3-recommended',
    'eslint:recommended',
    'plugin:prettier/recommended',
    '@vue/eslint-config-prettier'
  ),
  {
    ignores: ['data/', 'dist/', 'public/twemoji/'],
  },

  includeIgnoreFile(gitignorePath),

  {
    plugins: {
      'local-rules': localRules,
    },

    languageOptions: {
      globals: {
        ...globals.node,
      },

      parserOptions: {
        ecmaVersion: '6',
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
      'vue/no-deprecated-destroyed-lifecycle': 'off',
      'vue/no-deprecated-dollar-listeners-api': 'off',
      'vue/no-deprecated-dollar-scopedslots-api': 'off',
      'vue/no-deprecated-filter': 'warn',
      'vue/no-deprecated-props-default-this': 'off',
      'vue/no-deprecated-slot-attribute': 'off',
      'vue/no-deprecated-slot-scope-attribute': 'off',
      'vue/no-deprecated-v-bind-sync': 'off',
      'vue/no-deprecated-v-on-native-modifier': 'warn',
      'vue/no-v-for-template-key-on-child': 'off',
      'vue/no-v-model-argument': 'warn',
      'vue/require-explicit-emits': 'off',

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
            '^(global|entity|contentNode\\.[a-z][a-zA-Z]+|print\\.(global|activity|cover|picasso|program|story|toc))\\..+',
          translationKeyPropRegex: '[a-zA-Z0-9]-i18n-key$',
        },
      ],

      'vue/no-mutating-props': [
        'error',
        {
          shallowOnly: true,
        },
      ],
    },
  },
]
