import { Extension } from '@tiptap/core'
import { Plugin, PluginKey } from '@tiptap/pm/state'
import { Decoration, DecorationSet } from '@tiptap/pm/view'

export const imageUploadsKey = new PluginKey('imageUploads')

export default Extension.create({
  name: 'imageUploads',
  addProseMirrorPlugins() {
    return [
      new Plugin({
        key: imageUploadsKey,
        state: {
          init: () => DecorationSet.empty,
          apply(transaction, decorations) {
            decorations = decorations.map(transaction.mapping, transaction.doc)
            const action = transaction.getMeta(imageUploadsKey)
            if (action?.add) {
              const { id, position, label } = action.add
              const widget = Decoration.widget(
                position,
                () => {
                  const element = document.createElement('span')
                  element.className = 'image-upload-placeholder'
                  element.setAttribute('role', 'status')
                  element.textContent = label
                  return element
                },
                { id }
              )
              decorations = decorations.add(transaction.doc, [widget])
            }
            if (action?.remove) {
              decorations = decorations.remove(
                decorations.find(
                  undefined,
                  undefined,
                  (spec) => spec.id === action.remove
                )
              )
            }
            return decorations
          },
        },
        props: { decorations: (state) => imageUploadsKey.getState(state) },
      }),
    ]
  },
})

export async function insertImageFiles(editor, files, position, upload, label) {
  const pending = Array.from(files).map((file) => ({ file, id: {} }))
  for (const { id } of pending) {
    editor.view.dispatch(
      editor.state.tr.setMeta(imageUploadsKey, { add: { id, position, label } })
    )
  }
  const errors = []
  for (const { file, id } of pending) {
    try {
      const image = await upload(file)
      if (editor.isDestroyed) return
      const placeholder = imageUploadsKey
        .getState(editor.state)
        .find(undefined, undefined, (spec) => spec.id === id)[0]
      if (placeholder) {
        editor.view.dispatch(
          editor.state.tr
            .insert(placeholder.from, editor.schema.nodes.image.create(image))
            .setMeta(imageUploadsKey, { remove: id })
        )
      }
    } catch (error) {
      errors.push(error)
    } finally {
      if (!editor.isDestroyed) {
        editor.view.dispatch(editor.state.tr.setMeta(imageUploadsKey, { remove: id }))
      }
    }
  }
  if (errors.length) throw errors[0]
}
