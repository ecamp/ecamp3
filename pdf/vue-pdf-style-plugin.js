import { parse } from 'css'
import camelCase from 'lodash/camelCase.js'

const vuePdfStylePlugin = {
  name: 'vue-pdf-style-plugin',
  transform(code, id) {
    if (!/vue&type=pdf-style/.test(id)) {
      return
    }
    const parsed = parse(code)
    const rules = parsed.stylesheet.rules
    const transformedRules = transformCssRules(rules)
    return `export default (component) => {
      component.pdfStyle = ${JSON.stringify(transformedRules)};
    }`
  },
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
