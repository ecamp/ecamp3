import { mainThreadLoaderFor, renderPdf } from './renderPdf.js'
import cloneDeep from 'lodash/cloneDeep.js'
import Worker from 'worker-iife:./renderPdf.worker.js'
import * as Comlink from 'comlink'

// During prod build, force vite to bundle the required fonts
const fonts = import.meta.glob('../../assets/fonts/OpenSans/*.ttf') // eslint-disable-line no-unused-vars

export const generatePdf = async (data) => {
  const prepareInMainThread = await mainThreadLoaderFor(data.config)
  if (typeof prepareInMainThread === 'function') {
    await prepareInMainThread(data.config)
  }

  const serializableData = prepareDataForSerialization(data)

  if (data.renderInWorker) {
    return await Comlink.wrap(new Worker()).renderPdfInWorker(serializableData)
  } else {
    // In Firefox, dynamic imports are only available in the main thread:
    // https://bugzilla.mozilla.org/show_bug.cgi?id=1540913
    // So we use dynamic imports if we are in the main thread, but static imports if we are in the worker.
    const renderingDependencies = (await import('./renderingDependencies.js')).default
    return await renderPdf(serializableData, renderingDependencies)
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
  return entity()?._meta?.self
}
