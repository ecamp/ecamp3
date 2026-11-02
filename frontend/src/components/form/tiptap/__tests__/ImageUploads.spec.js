import { afterEach, describe, expect, it, vi } from 'vitest'
import { Editor } from '@tiptap/vue-3'
import Document from '@tiptap/extension-document'
import Paragraph from '@tiptap/extension-paragraph'
import Text from '@tiptap/extension-text'
import CampImage from '../CampImage.js'
import ImageUploads, { insertImageFiles } from '../ImageUploads.js'

let editor
const src = '/api/camps/123456789abc/images/abcdef123456'
function createEditor(content = '<p>hello</p>') {
  editor = new Editor({
    extensions: [Document, Paragraph, Text, CampImage, ImageUploads],
    content,
  })
  return editor
}
afterEach(() => editor?.destroy())

describe('image uploads', () => {
  it('maps the placeholder while text is edited and never serializes upload state', async () => {
    createEditor()
    let finish
    const pending = insertImageFiles(
      editor,
      ['file'],
      3,
      () =>
        new Promise((resolve) => {
          finish = resolve
        }),
      'Uploading'
    )
    expect(editor.getHTML()).toBe('<p>hello</p>')
    editor.commands.insertContentAt(1, 'new ')
    finish({ src })
    await pending
    expect(editor.getHTML()).toContain(`new he<img src="${src}"`)
    expect(editor.getHTML()).not.toContain('Uploading')
  })

  it('does not insert an image whose placeholder was deleted', async () => {
    createEditor()
    let finish
    const pending = insertImageFiles(
      editor,
      ['file'],
      3,
      () =>
        new Promise((resolve) => {
          finish = resolve
        }),
      'Uploading'
    )
    editor.commands.deleteRange({ from: 1, to: 6 })
    finish({ src })
    await pending
    expect(editor.getHTML()).not.toContain('<img')
  })

  it('clears failed uploads and permits a retry', async () => {
    createEditor()
    await expect(
      insertImageFiles(
        editor,
        ['file'],
        1,
        async () => {
          throw new Error('failed')
        },
        'Uploading'
      )
    ).rejects.toThrow('failed')
    await insertImageFiles(editor, ['file'], 1, async () => ({ src }), 'Uploading')
    expect(editor.getHTML()).toContain('<img')
  })

  it('keeps multiple images in selection order', async () => {
    createEditor()
    const upload = vi.fn(async (file) => ({ src, alt: file }))
    await insertImageFiles(editor, ['first', 'second'], 1, upload, 'Uploading')
    expect(editor.getHTML().indexOf('alt="first"')).toBeLessThan(
      editor.getHTML().indexOf('alt="second"')
    )
  })

  it('accepts camp images but excludes external and embedded image URLs', () => {
    createEditor(
      `<p><img src="${src}" alt="tent" width="320"><img src="https://example.org/image.png"><img src="data:image/png;base64,AA=="></p>`
    )
    expect(editor.getHTML()).toContain('alt="tent"')
    expect(editor.getHTML()).toContain('width="320"')
    expect(editor.getHTML()).not.toContain('example.org')
    expect(editor.getHTML()).not.toContain('base64')
  })
})
