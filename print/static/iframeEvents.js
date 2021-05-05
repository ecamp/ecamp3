/* global Paged */

/**
 * Register event listeners to listen to iFrame ancestor postMessages
 */
window.addEventListener('message', (event) => {
  if (event.data) {
    if (event.data.event_id === 'reload') {
      window.location.reload()
    } else if (event.data.event_id === 'print') {
      window.print()
    }
  }
})

/**
 * Register event listeners to listen to PagedJS events
 */
class PagedEventHandler extends Paged.Handler {
  // eslint-disable-next-line no-useless-constructor
  constructor(chunker, polisher, caller) {
    super(chunker, polisher, caller)
  }

  // send message to parent frame when preview has finished
  afterPreview(pages) {
    // eslint-disable-next-line no-console
    console.log('PagedJS: rendering preview finished')
    window.parent.postMessage(
      {
        event_id: 'pagedjs_done',
        pages: pages.length,
      },
      '*'
    )
  }

  // send message to parent frame after each page
  afterPageLayout(pageElement, page, breakToken) {
    window.parent.postMessage(
      {
        event_id: 'pagedjs_progress',
        page: page.position,
      },
      '*'
    )
  }
}

Paged.registerHandlers(PagedEventHandler)
