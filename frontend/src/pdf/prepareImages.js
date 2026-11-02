import { campImagePath } from '@/common/helpers/campImagePath.js'
import { getEnv } from '@/environment.js'

export async function prepareImages(storeData, convert = imageToPng, campUri = null) {
  const sources = new Set()
  function visit(value) {
    if (typeof value === 'string') {
      if (value.startsWith('{') || value.startsWith('[')) {
        try {
          visit(JSON.parse(value))
        } catch {
          return
        }
      } else if (value.includes('<img')) {
        const document = new DOMParser().parseFromString(value, 'text/html')
        for (const image of document.querySelectorAll('img')) {
          const src = image.getAttribute('src')
          const path = campImagePath(src)
          const campPath = campUri?.replace(/^\/api\//, '/').replace(/^\//, '')
          if (path && (!campPath || path.startsWith(`${campPath}/images/`)))
            sources.add(src)
        }
      }
    } else if (value && typeof value === 'object') {
      Object.values(value).forEach(visit)
    }
  }
  visit(storeData)
  const images = {}
  for (const src of sources) {
    const url = `${getEnv().API_ROOT_URL.replace(/\/$/, '')}/${campImagePath(src)}`
    images[src] = await convert(url)
  }
  return images
}

async function imageToPng(url) {
  const response = await fetch(url, {
    credentials: 'include',
    headers: { Accept: 'image/webp' },
  })
  if (!response.ok) throw new Error(`Image download failed (${response.status})`)
  const objectUrl = URL.createObjectURL(await response.blob())
  try {
    const image = new Image()
    image.src = objectUrl
    await image.decode()
    const canvas = document.createElement('canvas')
    canvas.width = image.naturalWidth
    canvas.height = image.naturalHeight
    canvas.getContext('2d').drawImage(image, 0, 0)
    return canvas.toDataURL('image/png')
  } finally {
    URL.revokeObjectURL(objectUrl)
  }
}
