import MultiLineToast from '@/components/toast/MultiLineToast.vue'
import i18n from '@/plugins/i18n'
import { transformViolations } from '@/helpers/serverError'

function multiLineToast(lines) {
  return {
    component: MultiLineToast,
    props: {
      lines,
      generalErrorText: i18n.tc('components.toast.multiLineToast.generalError'),
    },
  }
}

function violationsToFlatArray(e) {
  const violationsObject = transformViolations(e, i18n)
  const violations = Object.entries(violationsObject)
  if (violations.length === 0) {
    return []
  }
  if (violations.length === 1 && violationsObject[0]) {
    return [violationsObject[0]]
  }
  const toArray = (element) => {
    if (Array.isArray(element)) {
      return element
    }
    return [element]
  }
  const result = []
  for (const [key, value] of Object.entries(violationsObject)) {
    for (const message of toArray(value)) {
      result.push(`${key}: ${message}`)
    }
  }
  return result
}

export { multiLineToast, violationsToFlatArray }
