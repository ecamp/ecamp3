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
  ...compat.extends(
    'eslint:recommended',
    'plugin:cypress/recommended',
    'plugin:prettier/recommended'
  ),

  includeIgnoreFile(gitignorePath),

  {
    languageOptions: {
      globals: {
        ...globals.node,
        ...globals.mocha,
      },
    },

    rules: {
      'prefer-const': 'error',
      'prettier/prettier': 'error',
    },
  },
]
