import { prepareInMainThread } from '@/pdf/prepareInMainThread.js'
import { cloneDeep } from 'lodash-es'
import { proxy } from 'comlink'
import { endpointSymbol } from 'vite-plugin-comlink/symbol'
import jsonStringifyReactiveValue from '@/components/print/jsonStringifyReactiveValue.js'

export const generatePdf = async (data, onProgress, signal) => {
  signal?.throwIfAborted()
  await prepareInMainThread(data.config)
  signal?.throwIfAborted()

  const serializableData = prepareDataForSerialization(data)
  return dispatchRenderPdf(data, serializableData, onProgress, signal)
}

async function dispatchRenderPdf(data, serializableData, onProgress, signal) {
  if (data.renderInWorker) {
    // ComlinkWorker is provided by vite-plugin-comlink
    // eslint-disable-next-line no-undef
    const instance = new ComlinkWorker(new URL('./renderPdf.worker.js', import.meta.url))
    let abort
    const aborted = new Promise((resolve, reject) => {
      abort = () => reject(signal.reason)
      signal?.addEventListener('abort', abort, { once: true })
    })
    try {
      signal?.throwIfAborted()
      return await Promise.race([
        instance.renderPdfInWorker(serializableData, proxy(onProgress)),
        aborted,
      ])
    } finally {
      signal?.removeEventListener('abort', abort)
      instance[endpointSymbol].terminate()
    }
  } else {
    return (await import('./renderPdf.js')).renderPdf(serializableData, onProgress)
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
