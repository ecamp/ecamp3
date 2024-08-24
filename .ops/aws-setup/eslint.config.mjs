import { includeIgnoreFile } from '@eslint/compat'
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
  ...compat.extends('eslint:recommended', 'plugin:prettier/recommended', 'prettier'),

  includeIgnoreFile(gitignorePath),

  {
    languageOptions: {
      globals: {
        ...globals.node,
      },

      ecmaVersion: 2022,
      sourceType: 'module',

      parserOptions: {
        parser: '@babel/eslint-parser',
      },
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
