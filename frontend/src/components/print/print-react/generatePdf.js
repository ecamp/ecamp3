import { prepareInMainThread } from '@/pdf/prepareInMainThread.mjs'
import cloneDeep from 'lodash/cloneDeep.js'

export const generatePdf = async (data) => {
  await prepareInMainThread(data.config)

  const serializableData = prepareDataForSerialization(data)

  if (data.renderInWorker) {
    // ComlinkWorker is provided by vite-plugin-comlink
    // eslint-disable-next-line no-undef
    const instance = new ComlinkWorker(
      new URL('./documents/campPrint/renderPdf.worker.js', import.meta.url)
    )
    return await instance.renderPdfInWorker(serializableData)
  } else {
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
