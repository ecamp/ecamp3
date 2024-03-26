import { apiPropsMixin } from '@/mixins/apiPropsMixin'

const eagerIfChanged = ({ errors, flags }) => {
  const debounce = apiPropsMixin.props.autoSaveDelay.default
  if (flags.pristine) {
    return {
      on: ['input'],
      debounce,
    }
  }

  if (errors.length) {
    return {
      on: ['input', 'change'],
    }
  }

  return {
    on: ['change', 'blur'],
    debounce,
  }
}
export { eagerIfChanged }
