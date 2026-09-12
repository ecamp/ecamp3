import { parse } from 'css'
import camelCase from 'lodash-es/camelCase.js'

export const vueStyleReactPdfPlugin = {
  name: 'vue-pdf-style-plugin',
  enforce: 'pre',
  transform(code, id) {
    if (id.endsWith('.vue')) {
      return transformReactPdfStyleBlocks(code)
    }
  },
}

export const vuePdfStylePlugin = {
  name: 'vue-pdf-style-plugin',
  transform(code, id) {
    if (!/vue&type=pdf-style/.test(id)) {
      return
    }
    const parsed = parse(code)
    const rules = parsed.stylesheet.rules
    const transformedRules = transformCssRules(rules)
    return {
      code: `export default (component) => {
        component.pdfStyle = ${JSON.stringify(transformedRules)};
      }`,
      // The generated code has nothing in common with the CSS input, so there is nothing to map.
      map: { mappings: '' },
    }
  },
}

function transformReactPdfStyleBlocks(code) {
  const reactPdfStyleBlock =
    /<style\b(?=[^>]*\blang=(["'])react-pdf\1)[^>]*>[\s\S]*?<\/style>/g

  const transformedCode = code.replace(reactPdfStyleBlock, (block) =>
    block.replace(/<style\b[^>]*>/, '<pdf-style>').replace(/<\/style>$/, '</pdf-style>')
  )

  if (transformedCode === code) {
    return
  }

  return {
    code: transformedCode,
    map: null,
  }
}

function transformCssRules(rules) {
  return rules.reduce((transformed, rule) => {
    rule.selectors.forEach((selector) => {
      if (!/^\.[a-zA-Z][a-zA-Z0-9_-]*$/.test(selector)) {
        console.error(
          'Only simple single-class selectors are supported in pdf-style. Got the selector',
          selector
        )
        return
      }
      const className = selector.substring(1)
      transformed[className] = transformed[className] || {}
      rule.declarations.forEach((declaration) => {
        // TODO validate and warn on invalid properties or values or property-value combinations
        const camelCasedProperty = camelCase(declaration.property)
        return (transformed[className][camelCasedProperty] = declaration.value)
      })
    })
    return transformed
  }, {})
}

export default vuePdfStylePlugin
