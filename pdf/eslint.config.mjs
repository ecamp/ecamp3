import vueEslintConfigPrettier from '@vue/eslint-config-prettier'

import { includeIgnoreFile } from '@eslint/compat'
import localRules from 'eslint-plugin-local-rules'
import vueEslint from 'eslint-plugin-vue'
import globals from 'globals'
import path from 'node:path'
import { fileURLToPath } from 'node:url'
import js from '@eslint/js'

const __filename = fileURLToPath(import.meta.url)
const __dirname = path.dirname(__filename)
const gitignorePath = path.resolve(__dirname, '.gitignore')

export default [
  js.configs.recommended,
  ...vueEslint.configs['flat/recommended'],
  {
    ignores: ['dist/*.mjs'],
  },

  includeIgnoreFile(gitignorePath),

  vueEslintConfigPrettier,

  {
    plugins: {
      'local-rules': localRules,
    },

    languageOptions: {
      globals: {
        ...globals.node,
        ...globals.jest,
        self: 'writable',
      },
    },

    rules: {
      'prefer-const': 'error',
      'prettier/prettier': 'error',

      'vue/block-order': [
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

      'local-rules/matchingTranslationKeys': [
        'error',
        {
          ignoreKeysRegex:
            '^(global|entity|contentNode\\.[a-z][a-zA-Z]+|print\\.(global|activity|cover|picasso|program|config|story|safetyConsiderations|toc|activityList))\\..+',
          translationKeyPropRegex: '[a-zA-Z0-9]-i18n-key$',
        },
      ],
    },
  },
]
