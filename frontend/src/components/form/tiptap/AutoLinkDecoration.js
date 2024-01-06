import { Extension } from '@tiptap/vue-2'
import { Plugin, PluginKey } from '@tiptap/pm/state'
import { Decoration, DecorationSet } from '@tiptap/pm/view'
import LinkifyIt from 'linkify-it'

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
            try {
              if (url.charAt(0) === '/') {
                return
              }
              const link = new URL(url)
              if (!link.host.includes('.') && !link.port) {
                return
              }
              if (!['http:', 'https:'].includes(link.protocol)) {
                return
              }
              decorations.push(
                Decoration.inline(pos + index, pos + lastIndex, {
                  nodeName: 'a',
                  href: url,
                  class: 'autolink',
                  target: '_blank',
                  rel: 'noopener noreferrer',
                })
              )
            } catch (error) {
              /* It can't be parsed as an url */
            }
          })
        }
      })
      return DecorationSet.create(doc, decorations)
    }

    return [
      new Plugin({
        key: new PluginKey('autoLinkDecoration'),
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
