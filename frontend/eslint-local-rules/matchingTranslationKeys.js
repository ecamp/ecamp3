const path = require('path')
// TODO move this file to common and get this require to still work, in frontend and print
const utils = require('eslint-plugin-vue/lib/utils/index.js')

/**
 * Convert a file path to our convention for translation key structures
 */
function pathToTranslationKeyStructure(str) {
  return str
    // convert words to camel case
    .replace(/[-_](\w)/gu, (_, c) => (c ? c.toUpperCase() : ''))
    // convert slashes to dots with lower case letter after them
    .replace(/\/(\w)/gu, (_, c) => '.' + (c ? c.toLowerCase() : ''))
}

/**
 * Escape a string for use in a regular expression
 */
function escapeForRegExp(str) {
  return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')
}

/**
 * Get the name param node from the given CallExpression
 */
function getCalledMethodName(node) {
  const nameLiteralNode = node.arguments[0]
  if (nameLiteralNode && utils.isStringLiteral(nameLiteralNode)) {
    const name = utils.getStringLiteralValue(nameLiteralNode)
    if (name != null) {
      return { name, loc: nameLiteralNode.loc }
    }
  }

  // cannot check
  return null
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
    function verifyTranslationKey(node, filepath) {
      const translationKey = node.name || ''
      if (translationKey.match(/^(global|entity|contentNode\.[a-z][a-zA-Z]+)\..+/)) {
        // Some global keys are allowed everywhere
        return
      }

      const expectedPrefix = pathToTranslationKeyStructure(filepath)

      if (!translationKey.match(new RegExp(`^${escapeForRegExp(expectedPrefix)}\\.`))) {
        context.report({
          node,
          message:
            'Translation key `{{translationKey}}` should start with `{{expectedPrefix}}.`, based on file path `{{filepath}}`.',
          data: { expectedPrefix, translationKey, filepath },
        })
      }
    }

    const nodeVisitor = {
      CallExpression(node) {
        const extension = path.extname(context.getFilename())
        if (!['.vue'].includes(extension)) {
          // We only consider .vue component files
          return
        }

        const filepath = context.getFilename()
          // remove filesystem from the start
          // TODO make this more flexible by considering node_modules location
          .replace(/^\/app\/src\//, '')
          .replace(new RegExp(`${escapeForRegExp(extension)}$`), '')

        const callee = node.callee
        const nameWithLoc = getCalledMethodName(node)
        if (!nameWithLoc) {
          // cannot check
          return
        }
        if (callee.type !== 'Identifier' || !['$tc', 'tc'].includes(callee.name)) {
          // Not a translation call
          return
        }
        verifyTranslationKey(nameWithLoc, filepath)
      }
    }

    return utils.defineTemplateBodyVisitor(context,
      // template visitor
      nodeVisitor,
      // script visitor
      utils.isScriptSetup(context)
        ? utils.defineScriptSetupVisitor(context, nodeVisitor)
        : utils.defineVueVisitor(context, nodeVisitor))
  }
}
