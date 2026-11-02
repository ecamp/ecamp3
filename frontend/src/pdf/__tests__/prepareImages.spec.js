import { expect, it, vi } from 'vitest'
import { prepareImages } from '../prepareImages.js'

it('prepares each internal image once from serialized store entries', async () => {
  const src = '/api/camps/123456789abc/images/abcdef123456'
  const convert = vi.fn(async () => 'data:image/png;base64,png')
  const data = {
    nodes: {
      a: JSON.stringify({
        data: {
          html: `<p><img src="${src}"><img src="${src}"><img src="https://example.org/image"></p>`,
        },
      }),
    },
  }
  expect(await prepareImages(data, convert)).toEqual({
    [src]: 'data:image/png;base64,png',
  })
  expect(convert).toHaveBeenCalledTimes(1)
  expect(convert).toHaveBeenCalledWith(src)
})

it('does not silently omit images when preparation fails', async () => {
  const data = { html: '<img src="/api/camps/123456789abc/images/abcdef123456">' }
  await expect(
    prepareImages(data, async () => {
      throw new Error('download failed')
    })
  ).rejects.toThrow('download failed')
})

it('does not download cached images from another camp', async () => {
  const convert = vi.fn()
  await prepareImages(
    { html: '<img src="/api/camps/aaaaaaaaaaaa/images/abcdef123456">' },
    convert,
    '/camps/123456789abc'
  )
  expect(convert).not.toHaveBeenCalled()
})
