import reactToWebComponent from 'react-to-webcomponent'
import PrintPreview from '@/components/print/PrintPreview'
import React from 'react'
import ReactDOM from 'react-dom'

const Ecamp3PrintPreview = reactToWebComponent(PrintPreview, React, ReactDOM)

// Webcomponents don't support "undefining" so far, see
// https://github.com/WICG/webcomponents/issues/754
// So for now, hot module reloading won't work. At least we can make sure that
// no error is thrown when HMR occurs or the user navigates away and back.
if (!customElements.get('ecamp3-print-preview')) {
  customElements.define('ecamp3-print-preview', Ecamp3PrintPreview)
}
