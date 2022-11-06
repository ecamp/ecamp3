import prepareInMainThread from './documents/campPrint/index.js'
import cloneDeep from 'lodash/cloneDeep.js'

function browserSupportsWorkerType() {
  let supports = false
  const tester = {
    get type() {
      supports = true
      return 'module'
    }, // it's been called, it's supported
  }
  try {
    new Worker('blob://', tester)
  } finally {
    // we need the return statement inside finally for this type of feature detection
    // eslint-disable-next-line no-unsafe-finally
    return supports
  }
}

export const generatePdf = async (data) => {
  await prepareInMainThread(data.config)

  const serializableData = prepareDataForSerialization(data)

  // Firefox does not support the ESM workers which Vite generates in development.
  // Production is fine though.
  const workerSupported =
    process.env.NODE_ENV === 'production' || browserSupportsWorkerType()

  if (data.renderInWorker && workerSupported) {
    // eslint-disable-next-line no-undef
    const instance = new ComlinkWorker(
      new URL('./documents/campPrint/renderPdf.worker.js', import.meta.url)
    )
    return await instance.renderPdfInWorker(serializableData)
  } else {
    // In Firefox, dynamic imports are only available in the main thread:
    // https://bugzilla.mozilla.org/show_bug.cgi?id=1540913
    // So we use dynamic imports if we are in the main thread, but static imports if we are in the worker.
    return await (
      await import('./documents/campPrint/renderPdf.js')
    ).renderPdf(serializableData)
  }
}

function prepareDataForSerialization(data) {
  return {
    config: JSON.parse(
      JSON.stringify(replaceEntitiesWithRelativeUris(cloneDeep(data.config)))
    ),
    storeData: JSON.parse(JSON.stringify(data.storeData)),
    translationData: JSON.parse(JSON.stringify(data.translationData)),
  }
}

function replaceEntitiesWithRelativeUris(map) {
  Object.keys(map).forEach((key) => {
    const value = map[key]
    const relativeUri = relativeUriFor(value)
    if (relativeUri) {
      map[key] = relativeUri
    }
  })
  return map
}

function relativeUriFor(entity) {
  if (typeof entity !== 'function') {
    return entity
  }
  return entity()?._meta?.self
}
