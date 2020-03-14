export const debounce = jest.fn().mockImplementation(
  function (callback, delay) {
    var timer
    return function () {
      clearTimeout(timer)
      var args = [].slice.call(arguments)
      timer = setTimeout(function () {
        callback.apply(this, args)
      }, 100)
    }
  })
