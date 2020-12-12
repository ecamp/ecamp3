export const debounce = jest.fn().mockImplementation(
  function (callback, delay) {
    var timer
    let thisArg
    return function (...args) {
      clearTimeout(timer)
      thisArg = this
      var args = [].slice.call(arguments)
      timer = setTimeout(function () {
        callback.apply(thisArg, args)
      }, 100)
    }
  })
