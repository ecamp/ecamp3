import { printComponentFor, renderPdf } from './renderPdf.js'
import { cloneDeep } from 'lodash'
import Worker from 'worker-iife:./renderPdf.worker.js'
import * as Comlink from 'comlink'

export const generatePdf = async (data) => {
  const component = printComponentFor(data.config)
  if (typeof component.prepareInMainThread === 'function') {
    await component.prepareInMainThread(data.config)
  }

  const serializableData = prepareDataForSerialization(data)

  if (data.renderInWorker) {
    return {
      ...(await Comlink.wrap(new Worker()).renderPdfInWorker(serializableData)),
      filename: 'web-worker.pdf'
    }
  } else {
    return {
      ...(await renderPdf(serializableData)),
      filename: 'main-thread.pdf'
    }
  }
}

function prepareDataForSerialization (data) {
  return {
    config: JSON.parse(JSON.stringify(replaceEntitiesWithRelativeUris(cloneDeep(data.config)))),
    storeData: JSON.parse(JSON.stringify(data.storeData)),
    translationData: JSON.parse(JSON.stringify(data.translationData))
  }
}

function replaceEntitiesWithRelativeUris (map) {
  Object.keys(map).forEach(key => {
    const value = map[key]
    const relativeUri = relativeUriFor(value)
    if (relativeUri) {
      map[key] = relativeUri
    }
  })
  return map
}

function relativeUriFor (entity) {
  if (typeof entity !== 'function') {
    return entity
  }
  const baseUrl = window.environment.API_ROOT_URL
  return entity()?._meta?.self?.replace(new RegExp('^' + baseUrl), '')
}
