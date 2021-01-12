export const debounce = jest.fn().mockImplementation(
  function (callback, delay) {
    let timer
    return function (...args) {
      clearTimeout(timer)
      var args = [].slice.call(arguments)
      timer = setTimeout(() => {
        callback.apply(this, args)
      }, 100)
    }
  })
