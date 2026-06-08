export function createLocalStorageFake() {
  const localStorageFake = {
    getItem: (key) => {
      return localStorageFake.store[key] || null
    },
    setItem: (key, value) => {
      localStorageFake.store[key] = String(value)
    },
    removeItem: (key) => {
      delete localStorageFake.store[key]
    },
    clear: () => {
      localStorageFake.store = {}
    },
    store: {},
  }
  return localStorageFake
}
