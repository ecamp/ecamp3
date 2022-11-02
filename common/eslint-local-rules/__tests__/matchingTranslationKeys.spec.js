import rule from '../matchingTranslationKeys.js'
import { RuleTester } from 'eslint'
import path from 'path'
import process from 'process'
import utils from 'eslint-plugin-vue/lib/utils/index.js'

const ruleTester = new RuleTester({
  parser: require.resolve('vue-eslint-parser'),
  root: true,
  env: {
    node: true,
    jest: true,
  },
  extends: [
    'plugin:vue/recommended',
    'eslint:recommended',
    'plugin:prettier/recommended',
    '@vue/eslint-config-prettier',
  ],
  parserOptions: {
    parser: '@babel/eslint-parser',
  },
  plugins: ['eslint-plugin-local-rules'],
})
const ruleInstance = rule(path, utils)

const options = [
  {
    ignoreKeysRegex: '^(global|entity|contentNode\\.[a-z][a-zA-Z]+)\\..+',
    translationKeyPropRegex: '[a-zA-Z0-9]-i18n-key$',
  },
]

const cwd = process.cwd()

ruleTester.run('local-rules/matching-translation-keys', ruleInstance, {
  valid: [
    {
      name: 'allows correct key in js',
      code: '$tc("components.hello.world")',
      options: options,
      filename: cwd + '/src/components/hello.js',
    },
    {
      name: 'allows correct key in vue component js',
      code: '<script>$tc("components.helloWorld.foo")</script>',
      options: options,
      filename: cwd + '/src/components/HelloWorld.vue',
    },
    {
      name: 'allows correct key in vue component setup script',
      code: '<script setup>const translation = $tc("components.helloWorld.foo")</script>',
      options: options,
      filename: cwd + '/src/components/HelloWorld.vue',
    },
    {
      name: 'allows correct key in scoped use in vue component js',
      code: '<script>export default { computed: { translate() { return this.$tc("components.helloWorld.foo") } } }</script>',
      options: options,
      filename: cwd + '/src/components/HelloWorld.vue',
    },
    {
      name: 'allows correct key in vue component template mustache syntax',
      code: '<template>{{ $tc("components.helloWorld.foo") }}</template>',
      options: options,
      filename: cwd + '/src/components/HelloWorld.vue',
    },
    {
      name: 'allows correct key in vue component template v-bind',
      code: '<template><div :title="$tc(\'components.helloWorld.foo\')"></div></template>',
      options: options,
      filename: cwd + '/src/components/HelloWorld.vue',
    },
    {
      name: 'allows correct key in vue component template i18n prop, based on translationKeyPropRegex',
      code: '<template><div title-i18n-key="components.helloWorld.foo"></div></template>',
      options: options,
      filename: cwd + '/src/components/HelloWorld.vue',
    },
    {
      name: 'allows correct key in vue component template v-bind i18n prop, based on translationKeyPropRegex',
      code: '<template><div :title-i18n-key="\'components.helloWorld.foo\'"></div></template>',
      options: options,
      filename: cwd + '/src/components/HelloWorld.vue',
    },
    {
      name: 'ignores valueless prop in vue component template i18n prop, based on translationKeyPropRegex',
      code: '<template><div title-i18n-key></div></template>',
      options: options,
      filename: cwd + '/src/components/HelloWorld.vue',
    },
    {
      name: 'allows global key, based on ignoreKeysRegex',
      code: '$tc("global.something")',
      options: options,
      filename: cwd + '/src/components/hello.js',
    },
    {
      name: 'allows correct key with complex directory names',
      code: '$tc("components.camelCase.kebabCase.pascalCase.withPeriod.hello.world")',
      options: options,
      filename:
        cwd + '/src/components/camelCase/kebab-case/PascalCase/with.period/hello.js',
    },
    {
      name: 'allows correct key with single quotes',
      code: "$tc('components.hello.world')",
      options: options,
      filename: cwd + '/src/components/hello.js',
    },
    {
      name: 'allows correct key with single quotes',
      code: '$tc(\'components.hello.world\', 0, { test: "foo" })',
      options: options,
      filename: cwd + '/src/components/hello.js',
    },
    {
      name: 'allows correct key with backticks',
      code: '$tc(`components.hello.world`)',
      options: options,
      filename: cwd + '/src/components/hello.js',
    },
    {
      name: 'allows correct key with arguments',
      code: '$tc(\'components.hello.world\', 0, { test: "foo" })',
      options: options,
      filename: cwd + '/src/components/hello.js',
    },
    {
      name: 'allows correct key when used without dollar sign',
      code: 'tc(\'components.hello.world\', 0, { test: "foo" })',
      options: options,
      filename: cwd + '/src/components/hello.js',
    },
    {
      name: 'ignores call without arguments',
      code: '$tc()',
      options: options,
      filename: cwd + '/src/components/hello.js',
    },
    {
      name: 'ignores unrelated file type',
      code: "$tc('hello.world')",
      options: options,
      filename: cwd + '/src/components/hello.json',
    },
    {
      name: 'ignores test file',
      code: "$tc('hello.world')",
      options: options,
      filename: cwd + '/src/components/hello.spec.js',
    },
    {
      name: 'ignores test helper file',
      code: "$tc('hello.world')",
      options: options,
      filename: cwd + '/src/components/__tests__/hello.js',
    },
    {
      name: 'ignores e2e test file',
      code: "$tc('hello.world')",
      options: options,
      filename: cwd + '/src/e2e/hello.js',
    },
    {
      name: 'accepts source file paths which do not start with /src',
      code: '$tc("components.hello.world")',
      options: options,
      filename: cwd + '/components/hello.js',
    },
  ],

  invalid: [
    {
      name: 'lints incorrect key in js',
      code: '$tc("hello.world")',
      options: options,
      filename: cwd + '/src/components/hello.js',
      errors: [
        {
          message:
            'Translation key `hello.world` should start with `components.hello.`, based on file path `components/hello`.',
        },
      ],
    },
    {
      name: 'lints incorrect key in vue component js',
      code: '<script>$tc("hello.world")</script>',
      options: options,
      filename: cwd + '/src/components/HelloWorld.vue',
      errors: [
        {
          message:
            'Translation key `hello.world` should start with `components.helloWorld.`, based on file path `components/HelloWorld`.',
        },
      ],
    },
    {
      name: 'lints incorrect key in vue component setup script',
      code: '<script setup>const translation = $tc("hello.world")</script>',
      options: options,
      filename: cwd + '/src/components/HelloWorld.vue',
      errors: [
        {
          message:
            'Translation key `hello.world` should start with `components.helloWorld.`, based on file path `components/HelloWorld`.',
        },
      ],
    },
    {
      name: 'lints correct key in scoped use in vue component js',
      code: '<script>export default { computed: { translate() { return this.$tc("hello.world") } } }</script>',
      options: options,
      filename: cwd + '/src/components/HelloWorld.vue',
      errors: [
        {
          message:
            'Translation key `hello.world` should start with `components.helloWorld.`, based on file path `components/HelloWorld`.',
        },
      ],
    },
    {
      name: 'lints incorrect key in vue component template mustache syntax',
      code: '<template>{{ $tc("hello.world") }}</template>',
      options: options,
      filename: cwd + '/src/components/HelloWorld.vue',
      errors: [
        {
          message:
            'Translation key `hello.world` should start with `components.helloWorld.`, based on file path `components/HelloWorld`.',
        },
      ],
    },
    {
      name: 'lints incorrect key in vue component template v-bind',
      code: '<template><div :title="$tc(\'hello.world\')"></div></template>',
      options: options,
      filename: cwd + '/src/components/HelloWorld.vue',
      errors: [
        {
          message:
            'Translation key `hello.world` should start with `components.helloWorld.`, based on file path `components/HelloWorld`.',
        },
      ],
    },
    {
      name: 'lints incorrect key in component template i18n prop, based on translationKeyPropRegex',
      code: '<template><div title-i18n-key="hello.world"></div></template>',
      options: options,
      filename: cwd + '/src/components/HelloWorld.vue',
      errors: [
        {
          message:
            'Translation key `hello.world` should start with `components.helloWorld.`, based on file path `components/HelloWorld`.',
        },
      ],
    },
    {
      name: 'lints incorrect key in vue component template v-bind i18n prop, based on translationKeyPropRegex',
      code: '<template><div :title-i18n-key="\'hello.world\'"></div></template>',
      options: options,
      filename: cwd + '/src/components/HelloWorld.vue',
      errors: [
        {
          message:
            'Translation key `hello.world` should start with `components.helloWorld.`, based on file path `components/HelloWorld`.',
        },
      ],
    },
    {
      name: 'lints incorrect global key, based on ignoreKeysRegex',
      code: '$tc("something.containing.global.hello.world")',
      options: options,
      filename: cwd + '/src/components/hello.js',
      errors: [
        {
          message:
            'Translation key `something.containing.global.hello.world` should start with `components.hello.`, based on file path `components/hello`.',
        },
      ],
    },
    {
      name: 'lints incorrect key with single quotes',
      code: "$tc('hello.world')",
      options: options,
      filename: cwd + '/src/components/hello.js',
      errors: [
        {
          message:
            'Translation key `hello.world` should start with `components.hello.`, based on file path `components/hello`.',
        },
      ],
    },
    {
      name: 'lints incorrect key with backticks',
      code: '$tc(`hello.world`)',
      options: options,
      filename: cwd + '/src/components/hello.js',
      errors: [
        {
          message:
            'Translation key `hello.world` should start with `components.hello.`, based on file path `components/hello`.',
        },
      ],
    },
    {
      name: 'lints incorrect key with arguments',
      code: '$tc(\'hello.world\', 0, { test: "foo" })',
      options: options,
      filename: cwd + '/src/components/hello.js',
      errors: [
        {
          message:
            'Translation key `hello.world` should start with `components.hello.`, based on file path `components/hello`.',
        },
      ],
    },
    {
      name: 'lints incorrect key when used without dollar sign',
      code: 'tc(\'hello.world\', 0, { test: "foo" })',
      options: options,
      filename: cwd + '/src/components/hello.js',
      errors: [
        {
          message:
            'Translation key `hello.world` should start with `components.hello.`, based on file path `components/hello`.',
        },
      ],
    },
    {
      name: 'lints empty key in js',
      code: '$tc("")',
      options: options,
      filename: cwd + '/src/components/hello.js',
      errors: [
        {
          message:
            'Translation key `` should start with `components.hello.`, based on file path `components/hello`.',
        },
      ],
    },
    {
      name: 'lints in file with path which does not start with src/',
      code: '$tc("hello.world")',
      options: options,
      filename: cwd + '/components/hello.js',
      errors: [
        {
          message:
            'Translation key `hello.world` should start with `components.hello.`, based on file path `components/hello`.',
        },
      ],
    },
  ],
})
