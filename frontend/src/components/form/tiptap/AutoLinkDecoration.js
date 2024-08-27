import { Extension } from '@tiptap/vue-2'
import { Plugin, PluginKey } from '@tiptap/pm/state'
import { Decoration, DecorationSet } from '@tiptap/pm/view'
import LinkifyIt from 'linkify-it'

export const AutoLinkKey = new PluginKey('autoLinkDecoration')

export const AutoLinkDecoration = Extension.create({
  name: 'autoLinkDecoration',
  addProseMirrorPlugins() {
    const linkify = new LinkifyIt()
    /**
     * @param {import('prosemirror-model').Node} doc
     */
    const urlDecoration = (doc) => {
      const decorations = []
      doc.descendants((node, pos) => {
        if (node.isText && linkify.pretest(node.text)) {
          const matches = linkify.match(node.text)
          matches?.forEach(({ index, lastIndex, url }) => {
            if (url.charAt(0) === '/') {
              return
            }
            let link
            try {
              link = new URL(url)
            } catch {
              /* It can't be parsed as an url */
            }
            if (!link.host.includes('.') && !link.port) {
              return
            }
            if (!['http:', 'https:'].includes(link.protocol)) {
              return
            }
            const attrs = {
              nodeName: 'a',
              href: url,
              class: 'autolink',
              target: '_blank',
              rel: 'noopener noreferrer',
            }
            if (this.editor.isEditable) {
              attrs.onclick = `(event.metaKey || event.ctrlKey || event.detail > 1) && window.open("${link}", "_blank");`
            }
            decorations.push(
              Decoration.inline(pos + index, pos + lastIndex, attrs, {
                start: pos + index,
                end: pos + lastIndex,
              })
            )
          })
        }
      })
      return DecorationSet.create(doc, decorations)
    }

    return [
      new Plugin({
        key: AutoLinkKey,
        state: {
          init: (_, { doc }) => {
            return urlDecoration(doc)
          },
          apply: (tr, oldState) => {
            return tr.docChanged ? urlDecoration(tr.doc) : oldState
          },
        },
        props: {
          decorations(state) {
            return this.getState(state)
          },
        },
      }),
    ]
  },
})
