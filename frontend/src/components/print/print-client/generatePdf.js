import { prepareInMainThread } from '@/pdf/prepareInMainThread.js'
import cloneDeep from 'lodash-es/cloneDeep.js'
import { proxy } from 'comlink'
import jsonStringifyReactiveValue from '@/components/print/jsonStringifyReactiveValue.js'
import axios from 'axios'

export const generatePdf = async (data, onProgress) => {
  await prepareInMainThread(data.config)

  const serializableData = prepareDataForSerialization(data)
  const start = new Date()
  let status = 500
  try {
    const result = await dispatchRenderPdf(data, serializableData, onProgress)
    status = 200;
    const timeTaken = (new Date() - start) / 1000
    serializableData.measurements = {
      total: timeTaken,
    }
    return result
  } finally {
    // noinspection ES6MissingAwait
    notifyPdfUsage(data.config, {
      status,
      timeTaken: (new Date() - start) / 1000,
    })
  }
}

async function dispatchRenderPdf(data, serializableData, onProgress) {
  if (data.renderInWorker) {
    // ComlinkWorker is provided by vite-plugin-comlink
    // eslint-disable-next-line no-undef
    const instance = new ComlinkWorker(new URL('./renderPdf.worker.js', import.meta.url))
    return instance.renderPdfInWorker(serializableData, proxy(onProgress))
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

async function notifyPdfUsage(config, usageData) {
  try {
    await axios({
      baseURL: null,
      method: 'post',
      url: `/log`,
      body: {
        ...usageData,
        config: jsonStringifyReactiveValue(config),
      },
      withCredentials: false,
      headers: {
        common: {
          'Cache-Control': 'no-cache',
          Pragma: 'no-cache',
          Expires: '0',
        },
      },
    })
  } catch {
    /* empty */
  }
}
