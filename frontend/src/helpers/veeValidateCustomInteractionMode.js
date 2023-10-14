const eagerIfChanged = ({ errors, flags }) => {
  const debounce = 350 /// 350 ms for debounce
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
    debounce
  }
}
export { eagerIfChanged }
