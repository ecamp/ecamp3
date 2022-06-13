/* global Paged */

/**
 * Register event listeners to listen to iFrame ancestor postMessages
 */
window.addEventListener('message', (event) => {
  if (event.origin !== window.FRONTEND_URL) {
    return
  }

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
    window.parent.postMessage(
      {
        event_id: 'pagedjs_done',
        pages: pages.length,
      },
      window.FRONTEND_URL
    )
  }

  // send message to parent frame after each page
  afterPageLayout(pageElement, page) {
    window.parent.postMessage(
      {
        event_id: 'pagedjs_progress',
        page: page.position,
      },
      window.FRONTEND_URL
    )
  }
}

Paged.registerHandlers(PagedEventHandler)
