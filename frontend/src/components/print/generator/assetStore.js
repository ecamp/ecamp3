const DATABASE = 'ecamp-print'
const STORE = 'assets'

function openDatabase() {
  return new Promise((resolve, reject) => {
    const request = indexedDB.open(DATABASE, 1)
    request.onupgradeneeded = () => request.result.createObjectStore(STORE)
    request.onsuccess = () => resolve(request.result)
    request.onerror = () => reject(request.error)
  })
}

async function run(mode, operation) {
  const database = await openDatabase()
  try {
    return await new Promise((resolve, reject) => {
      const request = operation(database.transaction(STORE, mode).objectStore(STORE))
      request.onsuccess = () => resolve(request.result)
      request.onerror = () => reject(request.error)
    })
  } finally {
    database.close()
  }
}

/**
 * Keeps a file in the browser and returns the id under which it can be loaded again.
 */
export async function saveAsset(file) {
  const assetId = crypto.randomUUID()
  await run('readwrite', (store) => store.put(file, assetId))
  return assetId
}

export function loadAsset(assetId) {
  return run('readonly', (store) => store.get(assetId))
}

export function deleteAsset(assetId) {
  return run('readwrite', (store) => store.delete(assetId))
}
