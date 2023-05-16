import MultiLineToast from '@/components/toast/MultiLineToast.vue'
import i18n from '@/plugins/i18n'
import { violationsToFlatArray } from '@/helpers/serverError'

function multiLineToast(lines) {
  return {
    component: MultiLineToast,
    props: {
      lines,
      generalErrorText: i18n.tc('components.toast.toasts.multiLineToast.generalError'),
    },
  }
}

function errorToMultiLineToast(error) {
  return multiLineToast(violationsToFlatArray(error, i18n))
}

export { errorToMultiLineToast, multiLineToast }
