import playwrightEslint from 'eslint-plugin-playwright'
import { includeIgnoreFile } from '@eslint/compat'
import globals from 'globals'
import path from 'node:path'
import { fileURLToPath } from 'node:url'
import js from '@eslint/js'
import prettierRecommended from 'eslint-plugin-prettier/recommended'
import tseslint from 'typescript-eslint'

const __filename = fileURLToPath(import.meta.url)
const __dirname = path.dirname(__filename)

const gitignorePath = path.resolve(__dirname, '.gitignore')

export default tseslint.config(
  js.configs.recommended,
  ...tseslint.configs.recommended,
  playwrightEslint.configs['flat/recommended'],
  prettierRecommended,
  {
    ignores: ['data/', 'playwright-report/', 'test-results/', 'dist/'],
  },

  includeIgnoreFile(gitignorePath),

  {
    languageOptions: {
      globals: {
        ...globals.node,
      },
    },

    rules: {
      'prefer-const': 'error',
      'prettier/prettier': 'error',
      'playwright/expect-expect': [
        'error',
        {
          assertFunctionNames: [
            'expect',
            'expectCacheHit',
            'expectCacheMiss',
            'expectCachePass',
            'waitForCacheMiss',
          ],
        },
      ],
      '@typescript-eslint/no-unused-vars': [
        'error',
        { argsIgnorePattern: '^_', varsIgnorePattern: '^_' },
      ],
    },
  }
)
