import { expect, it, vi } from 'vitest'
import { prepareRichText } from '../prepareRichText.js'

it('loads internal images once and embeds them in the print HTML', async () => {
  const src = '/api/camps/123456789abc/images/abcdef123456'
  const load = vi.fn(async () => 'data:image/webp;base64,aGVsbG8=')
  const html = await prepareRichText(
    `<p>Text<img src="${src}" alt="tent" width="320"><img src="${src}"></p>`,
    load
  )
  expect(load).toHaveBeenCalledTimes(1)
  expect(load).toHaveBeenCalledWith('camps/123456789abc/images/abcdef123456')
  expect(html).toContain('data:image/webp;base64,aGVsbG8=')
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
