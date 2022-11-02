module.exports = (path, utils) => {
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
   * Read the value of a string literal
   */
  function getStringLiteral(node) {
    if (node && utils.isStringLiteral(node)) {
      const value = utils.getStringLiteralValue(node)
      if (value != null) {
        return { name: value, value, loc: node.loc }
      }
    }

    // cannot check
    return null
  }

  /**
   * Get the first method argument, including source location, from the given CallExpression
   */
  function getFirstMethodArgument(node) {
    return getStringLiteral(node.arguments[0])
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

  /**
   * Gets the key of the given attribute node.
   * E.g. given a node which represents the attribute id="my-header",
   * this function would return "id"
   */
  function getKeyName(attr) {
    if (attr.directive) {
      if (attr.key.name.name !== 'bind') {
        // We are only interested in plain attrs and in v-bind attrs (which start with a colon)
        return null
      }
      return attr.key.argument?.name
    }
    return attr.key.name
  }

  /**
   * Gets the value of the given attribute node.
   * E.g. given a node which represents the attribute id="my-header",
   * this function would return "my-header"
   */
  function getPropValue(attr) {
    const valueNode = attr.value
    let value = null
    if (valueNode.type === 'VExpressionContainer') {
      // The attribute is probably using v-bind (or prefix : colon), so it has a dynamic value.
      // Try to read it as a string literal, and give up otherwise.
      value = getStringLiteral(valueNode.expression)
    } else if (valueNode.type === 'VLiteral') {
      value = valueNode
    }
    return value
  }

  function shouldProcessFile(filename) {
    if (!(filename.endsWith('.vue') || filename.endsWith('.js'))) {
      // We only process .vue component and .js files
      return false
    }
    if (
      filename.endsWith('.spec.js') ||
      filename.match(/\/__tests__\//) ||
      filename.match(/\/e2e\//)
    ) {
      // ignore test files
      return false
    }
    return true
  }

  return {
    meta: {
      hasSuggestions: false, // We cannot easily auto-fix the translation JSON, so we can't make an automatic fix suggestion
      type: 'suggestion',
      docs: {
        description:
          "require translation keys in Vue components to match the component's file name",
      },
      schema: [
        {
          type: 'object',
          properties: {
            ignoreKeysRegex: {
              type: 'string',
            },
            translationKeyPropRegex: {
              type: 'string',
            },
          },
          additionalProperties: false,
        },
      ],
    },
    create(context) {
      const filename = context.getFilename()
      if (!shouldProcessFile(filename)) {
        // Return no visitor logic if the file is not of interest to us
        return {}
      }

      const extension = path.extname(filename)
      const filesystemPrefix = context.getCwd()
      const filepath = context
        .getFilename()
        .replace(new RegExp(`^${escapeForRegExp(filesystemPrefix)}/`), '')
        // Optionally, remove src/ from the file path. We have this in frontend, but not in print
        .replace(/^src\//, '')
        .replace(new RegExp(`${escapeForRegExp(extension)}$`), '')

      const ignoreKeysRegex = context.options?.[0]?.ignoreKeysRegex
      const _ignoreKeysRegex = new RegExp(ignoreKeysRegex)

      function verifyTranslationKey(methodArgument, filepath) {
        const translationKey = methodArgument.value || ''
        if (ignoreKeysRegex && translationKey.match(_ignoreKeysRegex)) {
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

      const translationKeyPropRegex = context.options?.[0]?.translationKeyPropRegex
      const _translationKeyPropRegex = new RegExp(translationKeyPropRegex)

      /**
       * Some props of some of our components accept a translation key, which they pass into
       * the $tc function internally. We can check these translation keys as well.
       */
      const attributeVisitor = {
        'VElement > VStartTag > VAttribute': function (attr) {
          const keyName = getKeyName(attr)
          if (!keyName || !keyName.match(_translationKeyPropRegex)) {
            // We are only interested in a very specific selection of props
            return
          }

          const value = getPropValue(attr)
          if (value === null) {
            // Could not extract value, or value was null. Either way, there is nothing to verify
            return
          }

          verifyTranslationKey(value, filepath)
        },
      }

      return utils.defineTemplateBodyVisitor(
        context,
        // template visitor
        {
          ...nodeVisitor,
          ...(translationKeyPropRegex ? attributeVisitor : {}),
        },
        // script visitor
        utils.isScriptSetup(context)
          ? utils.defineScriptSetupVisitor(context, nodeVisitor)
          : nodeVisitor
      )
    },
  }
}
