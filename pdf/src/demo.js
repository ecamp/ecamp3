import pdf from './index.js'

import './assets/main.css'
;(async () => {
  const config = {
    camp: { name: 'Camp name' },
    contents: [
      {
        type: 'Picasso',
        options: {
          periods: ['/periods/1a2b3c4d'],
        },
      },
    ],
  }
  const storeData = {
    '/periods/1a2b3c4d': {
      _meta: { self: '/periods/1a2b3c4d' },
      description: 'Hauptlager',
    },
  }
  const store = {
    get(uri) {
      return storeData[uri]
    },
  }
  const blob = await pdf({
    config,
    store,
    $tc: (key) => key,
  }).toBlob()
  document.getElementById('pdf-iframe').src =
    URL.createObjectURL(blob) + '#navpanes=0&pagemode=none&zoom=page-fit'
})()
