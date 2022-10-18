const path = require('path')
// TODO move this file to common and get this require to still work, in frontend and print
const utils = require('eslint-plugin-vue/lib/utils/index.js')

/**
 * Convert a file path to our convention for translation key structures
 */
function pathToTranslationKeyStructure(str) {
  return (
    str
      // convert words to camel case
      .replace(/[-_](\w)/gu, (_, c) => (c ? c.toUpperCase() : ''))
      // convert slashes to dots with lower case letter after them
      .replace(/\/(\w)/gu, (_, c) => '.' + (c ? c.toLowerCase() : ''))
  )
}

/**
 * Escape a string for use in a regular expression
 */
function escapeForRegExp(str) {
  return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')
}

/**
 * Get the first method argument, including source location, from the given CallExpression
 */
function getFirstMethodArgument(node) {
  const nameLiteralNode = node.arguments[0]
  if (nameLiteralNode && utils.isStringLiteral(nameLiteralNode)) {
    const value = utils.getStringLiteralValue(nameLiteralNode)
    if (value != null) {
      return { name: value, value, loc: nameLiteralNode.loc }
    }
  }

  // cannot check
  return null
}

/**
 * Get the called method name from the given CallExpression
 */
function getCalledMethodName(node) {
  const callee = utils.skipChainExpression(node.callee)
  if (callee.type === 'Identifier') {
    return callee.name
  }
  if (callee.type === 'MemberExpression') {
    return utils.getStaticPropertyName(callee)
  }
  return null
}

function shouldProcessFile(filename) {
  return (
    // We only process .vue component and .js files
    (filename.endsWith('.vue') || filename.endsWith('.js')) &&
    // ignore test files
    !filename.endsWith('.spec.js') &&
    !filename.match(/\/__tests__\//)
  )
}

module.exports = {
  meta: {
    hasSuggestions: false, // We cannot easily auto-fix the translation JSON, so we can't make an automatic fix suggestion
    type: 'suggestion',
    docs: {
      description:
        "require translation keys in Vue components to match the component's file name",
    },
  },
  create(context) {
    function verifyTranslationKey(methodArgument, filepath) {
      const translationKey = methodArgument.value || ''
      if (translationKey.match(/^(global|entity|contentNode\.[a-z][a-zA-Z]+)\..+/)) {
        // Some global keys are allowed everywhere
        return
      }

      const expectedPrefix = pathToTranslationKeyStructure(filepath)

      if (!translationKey.match(new RegExp(`^${escapeForRegExp(expectedPrefix)}\\.`))) {
        context.report({
          node: methodArgument,
          message:
            'Translation key `{{translationKey}}` should start with `{{expectedPrefix}}.`, based on file path `{{filepath}}`.',
          data: { expectedPrefix, translationKey, filepath },
        })
      }
    }

    const nodeVisitor = {
      CallExpression(node) {
        const filename = context.getFilename()
        const extension = path.extname(filename)
        const filesystemPrefix = context.getCwd()
        const filepath = context
          .getFilename()
          .replace(new RegExp(`^${escapeForRegExp(filesystemPrefix)}/`), '')
          // Optionally, remove src/ from the file path. We have this in frontend, but not in print
          .replace(/^src\//, '')
          .replace(new RegExp(`${escapeForRegExp(extension)}$`), '')

        const calledMethodName = getCalledMethodName(node)
        if (!['$tc', 'tc'].includes(calledMethodName)) {
          // Not a translation call
          return
        }

        const methodArgument = getFirstMethodArgument(node)
        if (!methodArgument) {
          // Cannot find the value for the first argument, so we cannot check
          return
        }

        verifyTranslationKey(methodArgument, filepath)
      },
    }

    const filename = context.getFilename()
    if (!shouldProcessFile(filename)) {
      // Return no visitor logic if the file is not of interest to us
      return {}
    }

    return utils.defineTemplateBodyVisitor(
      context,
      // template visitor
      nodeVisitor,
      // script visitor
      utils.isScriptSetup(context)
        ? utils.defineScriptSetupVisitor(context, nodeVisitor)
        : nodeVisitor
    )
  },
}
