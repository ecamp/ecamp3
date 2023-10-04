const eagerIfChanged = ({ errors, flags }) => {
  if (flags.pristine) {
    return {
      on: ['input'],
    }
  }

  if (errors.length) {
    return {
      on: ['input', 'change'],
    }
  }

  return {
    on: ['change', 'blur'],
  }
}
export { eagerIfChanged }
