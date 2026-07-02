import { includeIgnoreFile } from '@eslint/compat'
import globals from 'globals'
import path from 'node:path'
import { fileURLToPath } from 'node:url'
import js from '@eslint/js'
import prettierRecommended from 'eslint-plugin-prettier/recommended'

const __filename = fileURLToPath(import.meta.url)
const __dirname = path.dirname(__filename)
const gitignorePath = path.resolve(__dirname, '.gitignore')

export default [
  {
    files: ['**/*.ts'],
  },
  js.configs.recommended,
  prettierRecommended,

  includeIgnoreFile(gitignorePath),

  {
    languageOptions: {
      globals: {
        ...globals.node,
      },

      ecmaVersion: 2022,
      sourceType: 'module',
    },

    rules: {
      'prefer-const': 'error',
      'prettier/prettier': 'error',

      'no-unused-vars': [
        'error',
        {
          argsIgnorePattern: '^_$',
        },
      ],
    },
  },
]
