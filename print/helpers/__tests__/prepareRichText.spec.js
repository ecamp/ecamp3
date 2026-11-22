import { expect, it, vi } from 'vitest'
import { prepareRichText } from '../prepareRichText.js'

it('loads internal images once and embeds them in the print HTML', async () => {
  const path = `images/${'abcdef0123456789'.repeat(4)}/content`
  const src = `/api/${path}`
  const load = vi.fn(async () => 'data:image/avif;base64,aGVsbG8=')
  const html = await prepareRichText(
    `<p>Text<img src="${src}" alt="tent" width="320"><img src="${src}"></p>`,
    load
  )
  expect(load).toHaveBeenCalledTimes(1)
  expect(load).toHaveBeenCalledWith(path)
  expect(html).toContain('data:image/avif;base64,aGVsbG8=')
  expect(html).toContain('alt="tent"')
  expect(html).toContain('width="320"')
})

it('removes unsafe markup and never downloads external images', async () => {
  const load = vi.fn()
  const html = await prepareRichText(
    '<script>alert(1)</script><img src="https://example.org/image"><img src="data:image/png;base64,AA==">',
    load
  )
  expect(load).not.toHaveBeenCalled()
  expect(html).not.toContain('<img')
  expect(html).not.toContain('script')
})
