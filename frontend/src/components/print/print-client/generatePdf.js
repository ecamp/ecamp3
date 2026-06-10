import { prepareInMainThread } from '@/pdf/prepareInMainThread.js'
import { cloneDeep } from 'lodash-es'
import { proxy } from 'comlink'
import jsonStringifyReactiveValue from '@/components/print/jsonStringifyReactiveValue.js'
import axios from 'axios'
import { getEnv } from '@/environment.js'
import * as Sentry from '@sentry/browser'

const PRINT_URL = getEnv().PRINT_URL

export const generatePdf = async (data, onProgress) => {
  await prepareInMainThread(data.config)

  const serializableData = prepareDataForSerialization(data)
  const start = new Date()
  let status = 500
  try {
    const result = await dispatchRenderPdf(data, serializableData, onProgress)
    status = 200
    return result
  } finally {
    let userIdHash = ''
    if (window.crypto && serializableData.storeData?.auth?.user) {
      userIdHash = await hashUserId(serializableData.storeData?.auth?.user)
    }
    // noinspection ES6MissingAwait
    notifyPdfUsage({
      config: data.config,
      status,
      userIdHash,
      measurements: {
        total: (new Date() - start) / 1000,
      },
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

async function notifyPdfUsage(data) {
  try {
    await axios({
      baseURL: null,
      method: 'post',
      url: `${PRINT_URL}/api/logPdfUsage`,
      data,
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

async function hashUserId(userJson) {
  try {
    const user = JSON.parse(userJson)
    const encoder = new TextEncoder()
    const data = encoder.encode(user.id)
    const hashBuffer = await window.crypto.subtle.digest('SHA-256', data)
    const hashArray = Array.from(new Uint8Array(hashBuffer))
    return hashArray.map((byte) => byte.toString(16).padStart(2, '0')).join('')
  } catch (e) {
    Sentry.captureException(e)
    return ''
  }
}
