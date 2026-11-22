import DOMPurify from 'isomorphic-dompurify'
import { campImagePath } from '@/common/helpers/campImagePath.js'

export async function prepareRichText(html, loadImage) {
  const document = DOMPurify.sanitize(html, { RETURN_DOM: true })
  const images = new Map()
  for (const image of document.querySelectorAll('img')) {
    const path = campImagePath(image.getAttribute('src'))
    if (!path) {
      image.remove()
      continue
    }
    if (!images.has(path)) images.set(path, await loadImage(path))
    image.setAttribute('src', images.get(path))
  }
  return document.innerHTML
}
