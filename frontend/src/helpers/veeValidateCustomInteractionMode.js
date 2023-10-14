const eagerIfChanged = ({ errors, flags }) => {
  if (flags.pristine) {
    return {
      on: ['input'],
      debounce: 1000, // 1 sec debounce,
    }
  }

  if (errors.length) {
    return {
      on: ['input', 'change'],
    }
  }

  return {
    on: ['change', 'blur'],
    debounce: 500,
  }
}
export { eagerIfChanged }
