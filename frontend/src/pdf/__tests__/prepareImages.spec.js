import { afterEach, beforeEach, expect, it, vi } from 'vitest'
import * as environment from '@/environment.js'
import { prepareImages } from '../prepareImages.js'

const imageSrc = `/api/images/${'abcdef0123456789'.repeat(4)}/content`
const camp = '/camps/123456789abc'

beforeEach(() => {
  vi.spyOn(environment, 'getEnv').mockReturnValue({ API_ROOT_URL: '/api' })
})

afterEach(() => {
  vi.restoreAllMocks()
})

function storeWithHtml(...html) {
  return {
    '/activities/1': {
      camp: { href: camp },
      rootContentNode: { href: '/content_nodes/1' },
    },
    ...Object.fromEntries(
      html.map((html, index) => [
        `/content_node/single_texts/${index}`,
        { root: { href: '/content_nodes/1' }, data: { html } },
      ])
    ),
  }
}

it('prepares each internal image once across content nodes of the camp', async () => {
  const src = imageSrc
  const convert = vi.fn(async () => 'data:image/png;base64,png')
  const data = storeWithHtml(
    `<p><img src="${src}"><img src="${src}"><img src="https://example.org/image"></p>`,
    `<p><img src="${src}"></p>`
  )
  expect(await prepareImages(data, convert, camp)).toEqual({
    [src]: 'data:image/png;base64,png',
  })
  expect(convert).toHaveBeenCalledTimes(1)
  expect(convert).toHaveBeenCalledWith(src)
})

it('does not silently omit images when preparation fails', async () => {
  const data = storeWithHtml(`<img src="${imageSrc}">`)
  await expect(
    prepareImages(
      data,
      async () => {
        throw new Error('download failed')
      },
      camp
    )
  ).rejects.toThrow('download failed')
})

it('does not download cached images from another camp', async () => {
  const src = `/api/images/${'a'.repeat(64)}/content`
  const otherSrc = `/api/images/${'b'.repeat(64)}/content`
  const convert = vi.fn(async () => 'data:image/png;base64,png')
  const storeData = {
    activity: { camp: { href: camp }, rootContentNode: { href: '/roots/1' } },
    otherActivity: {
      camp: { href: '/camps/aaaaaaaaaaaa' },
      rootContentNode: { href: '/roots/2' },
    },
    note: { root: { href: '/roots/1' }, data: { html: `<img src="${src}">` } },
    otherNote: {
      root: { href: '/roots/2' },
      data: { html: `<img src="${otherSrc}">` },
    },
  }

  expect(await prepareImages(storeData, convert, camp)).toEqual({
    [src]: 'data:image/png;base64,png',
  })
  expect(convert).toHaveBeenCalledExactlyOnceWith(src)
})

it('downloads images from the configured API root', async () => {
  vi.mocked(environment.getEnv).mockReturnValue({
    API_ROOT_URL: 'https://api.example.org/custom/',
  })
  const convert = vi.fn(async () => 'data:image/png;base64,png')

  expect(
    await prepareImages(storeWithHtml(`<img src="${imageSrc}">`), convert, camp)
  ).toEqual({ [imageSrc]: 'data:image/png;base64,png' })
  expect(convert).toHaveBeenCalledExactlyOnceWith(
    `https://api.example.org/custom/images/${'abcdef0123456789'.repeat(4)}/content`
  )
})
