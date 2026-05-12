import { prepareInMainThread } from '@/pdf/prepareInMainThread.js'
import { cloneDeep } from 'lodash-es'
import { proxy } from 'comlink'
import jsonStringifyReactiveValue from '@/components/print/jsonStringifyReactiveValue.js'

export const generatePdf = async (data, onProgress) => {
  await prepareInMainThread(data.config)

  const serializableData = prepareDataForSerialization(data)

  if (data.renderInWorker) {
    // ComlinkWorker is provided by vite-plugin-comlink
    // eslint-disable-next-line no-undef
    const instance = new ComlinkWorker(new URL('./renderPdf.worker.js', import.meta.url))
    return await instance.renderPdfInWorker(serializableData, proxy(onProgress))
  } else {
    return await (await import('./renderPdf.js')).renderPdf(serializableData, onProgress)
  }
}

function prepareDataForSerialization(data) {
  return {
    config: JSON.parse(
      jsonStringifyReactiveValue(replaceEntitiesWithRelativeUris(cloneDeep(data.config)))
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
