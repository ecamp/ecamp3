import createMatchingTranslationKeys from '../matchingTranslationKeys.js'
import { RuleTester } from 'eslint'
import { describe, it } from 'vitest'
import localRules from 'eslint-plugin-local-rules'
import globals from 'globals'
import utils from 'eslint-plugin-vue/lib/utils/index.js'
import eslintParser from 'vue-eslint-parser'

RuleTester.describe = describe.skip
RuleTester.it = it

const ruleTester = new RuleTester({
  plugins: {
    'local-rules': localRules,
  },

  languageOptions: {
    parser: eslintParser,
    globals: {
      ...globals.node,
      ...globals.jest,
    },

    parserOptions: {
      parser: '@babel/eslint-parser',
    },
  },
})
const ruleInstance = createMatchingTranslationKeys(utils)

const options = [
  {
    ignoreKeysRegex: '^(global|entity|contentNode\\.[a-z][a-zA-Z]+)\\..+',
    translationKeyPropRegex: '[a-zA-Z0-9]-i18n-key$',
  },
]

ruleTester.run('local-rules/matching-translation-keys', ruleInstance, {
  valid: [
    {
      name: 'allows correct key in js',
      code: '$t("components.hello.world")',
      options: options,
      filename: '/src/components/hello.js',
    },
    {
      name: 'allows correct key in vue component js',
      code: '<script>$t("components.helloWorld.foo")</script>',
      options: options,
      filename: '/src/components/HelloWorld.vue',
    },
    {
      name: 'allows correct key in vue component setup script',
      code: '<script setup>const translation = $t("components.helloWorld.foo")</script>',
      options: options,
      filename: '/src/components/HelloWorld.vue',
    },
    {
      name: 'allows correct key in scoped use in vue component js',
      code: '<script>export default { computed: { translate() { return this.$t("components.helloWorld.foo") } } }</script>',
      options: options,
      filename: '/src/components/HelloWorld.vue',
    },
    {
      name: 'allows correct key in vue component template mustache syntax',
      code: '<template>{{ $t("components.helloWorld.foo") }}</template>',
      options: options,
      filename: '/src/components/HelloWorld.vue',
    },
    {
      name: 'allows correct key in vue component template v-bind',
      code: '<template><div :title="$t(\'components.helloWorld.foo\')"></div></template>',
      options: options,
      filename: '/src/components/HelloWorld.vue',
    },
    {
      name: 'allows correct key in vue component template i18n prop, based on translationKeyPropRegex',
      code: '<template><div title-i18n-key="components.helloWorld.foo"></div></template>',
      options: options,
      filename: '/src/components/HelloWorld.vue',
    },
    {
      name: 'allows correct key in vue component template v-bind i18n prop, based on translationKeyPropRegex',
      code: '<template><div :title-i18n-key="\'components.helloWorld.foo\'"></div></template>',
      options: options,
      filename: '/src/components/HelloWorld.vue',
    },
    {
      name: 'ignores valueless prop in vue component template i18n prop, based on translationKeyPropRegex',
      code: '<template><div title-i18n-key></div></template>',
      options: options,
      filename: '/src/components/HelloWorld.vue',
    },
    {
      name: 'allows global key, based on ignoreKeysRegex',
      code: '$t("global.something")',
      options: options,
      filename: '/src/components/hello.js',
    },
    {
      name: 'allows correct key with complex directory names',
      code: '$t("components.camelCase.kebabCase.pascalCase.withPeriod.hello.world")',
      options: options,
      filename: '/src/components/camelCase/kebab-case/PascalCase/with.period/hello.js',
    },
    {
      name: 'allows correct key with single quotes',
      code: "$t('components.hello.world')",
      options: options,
      filename: '/src/components/hello.js',
    },
    {
      name: 'allows correct key with single quotes',
      code: '$t(\'components.hello.world\', 0, { test: "foo" })',
      options: options,
      filename: '/src/components/hello.js',
    },
    {
      name: 'allows correct key with backticks',
      code: '$t(`components.hello.world`)',
      options: options,
      filename: '/src/components/hello.js',
    },
    {
      name: 'allows correct key with arguments',
      code: '$t("components.hello.world", 0, { test: "foo" })',
      options: options,
      filename: '/src/components/hello.js',
    },
    {
      name: 'allows correct key when used without dollar sign',
      code: 'tc(\'components.hello.world\', 0, { test: "foo" })',
      options: options,
      filename: '/src/components/hello.js',
    },
    {
      name: 'ignores call without arguments',
      code: '$t()',
      options: options,
      filename: '/src/components/hello.js',
    },
    {
      name: 'ignores unrelated file type',
      code: "$t('hello.world')",
      options: options,
      filename: '/src/components/hello.json',
    },
    {
      name: 'ignores test file',
      code: "$t('hello.world')",
      options: options,
      filename: '/src/components/hello.spec.js',
    },
    {
      name: 'ignores test helper file',
      code: "$t('hello.world')",
      options: options,
      filename: '/src/components/__tests__/hello.js',
    },
    {
      name: 'ignores e2e test file',
      code: "$t('hello.world')",
      options: options,
      filename: '/src/e2e/hello.js',
    },
    {
      name: 'accepts source file paths which do not start with /src',
      code: '$t("components.hello.world")',
      options: options,
      filename: '/components/hello.js',
    },
  ],

  invalid: [
    {
      name: 'lints incorrect key in js',
      code: '$t("hello.world")',
      options: options,
      filename: '/src/components/hello.js',
      errors: [
        {
          message:
            'Translation key `hello.world` should start with `components.hello.`, based on file path `components/hello`.',
        },
      ],
    },
    {
      name: 'lints incorrect key in vue component js',
      code: '<script>$t("hello.world")</script>',
      options: options,
      filename: '/src/components/HelloWorld.vue',
      errors: [
        {
          message:
            'Translation key `hello.world` should start with `components.helloWorld.`, based on file path `components/HelloWorld`.',
        },
      ],
    },
    {
      name: 'lints incorrect key in vue component setup script',
      code: '<script setup>const translation = $t("hello.world")</script>',
      options: options,
      filename: '/src/components/HelloWorld.vue',
      errors: [
        {
          message:
            'Translation key `hello.world` should start with `components.helloWorld.`, based on file path `components/HelloWorld`.',
        },
      ],
    },
    {
      name: 'lints correct key in scoped use in vue component js',
      code: '<script>export default { computed: { translate() { return this.$t("hello.world") } } }</script>',
      options: options,
      filename: '/src/components/HelloWorld.vue',
      errors: [
        {
          message:
            'Translation key `hello.world` should start with `components.helloWorld.`, based on file path `components/HelloWorld`.',
        },
      ],
    },
    {
      name: 'lints incorrect key in vue component template mustache syntax',
      code: '<template>{{ $t("hello.world") }}</template>',
      options: options,
      filename: '/src/components/HelloWorld.vue',
      errors: [
        {
          message:
            'Translation key `hello.world` should start with `components.helloWorld.`, based on file path `components/HelloWorld`.',
        },
      ],
    },
    {
      name: 'lints incorrect key in vue component template v-bind',
      code: '<template><div :title="$t(\'hello.world\')"></div></template>',
      options: options,
      filename: '/src/components/HelloWorld.vue',
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
      filename: '/src/components/HelloWorld.vue',
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
      filename: '/src/components/HelloWorld.vue',
      errors: [
        {
          message:
            'Translation key `hello.world` should start with `components.helloWorld.`, based on file path `components/HelloWorld`.',
        },
      ],
    },
    {
      name: 'lints incorrect global key, based on ignoreKeysRegex',
      code: '$t("something.containing.global.hello.world")',
      options: options,
      filename: '/src/components/hello.js',
      errors: [
        {
          message:
            'Translation key `something.containing.global.hello.world` should start with `components.hello.`, based on file path `components/hello`.',
        },
      ],
    },
    {
      name: 'lints incorrect key with single quotes',
      code: "$t('hello.world')",
      options: options,
      filename: '/src/components/hello.js',
      errors: [
        {
          message:
            'Translation key `hello.world` should start with `components.hello.`, based on file path `components/hello`.',
        },
      ],
    },
    {
      name: 'lints incorrect key with backticks',
      code: '$t(`hello.world`)',
      options: options,
      filename: '/src/components/hello.js',
      errors: [
        {
          message:
            'Translation key `hello.world` should start with `components.hello.`, based on file path `components/hello`.',
        },
      ],
    },
    {
      name: 'lints incorrect key with arguments',
      code: '$t(\'hello.world\', 0, { test: "foo" })',
      options: options,
      filename: '/src/components/hello.js',
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
      filename: '/src/components/hello.js',
      errors: [
        {
          message:
            'Translation key `hello.world` should start with `components.hello.`, based on file path `components/hello`.',
        },
      ],
    },
    {
      name: 'lints empty key in js',
      code: '$t("")',
      options: options,
      filename: '/src/components/hello.js',
      errors: [
        {
          message:
            'Translation key `` should start with `components.hello.`, based on file path `components/hello`.',
        },
      ],
    },
    {
      name: 'lints in file with path which does not start with src/',
      code: '$t("hello.world")',
      options: options,
      filename: '/components/hello.js',
      errors: [
        {
          message:
            'Translation key `hello.world` should start with `components.hello.`, based on file path `components/hello`.',
        },
      ],
    },
  ],
})
