const htmlToPdfElementMap = {
  'pdf-document': 'DOCUMENT',
  'pdf-page': 'PAGE',
  'pdf-view': 'VIEW',
  'pdf-text': 'TEXT',
}

function noop(fn) {
  throw Error(`no-op: ${fn}`)
}

function patchProp(el, key, prevVal, nextVal) {
  console.log('patchProp', el, key, prevVal, nextVal)
  el.props[key] = nextVal
}

function insert(child, parent, anchor) {
  console.log('insert', child, parent, anchor)
  if (child.type === 'TEXT_INSTANCE' && child.value === '') {
    return
  }
  if (child.type === 'PAGE' && parent.type !== 'DOCUMENT') {
    throw Error('Tag <Page> can only be used at the top level of a document.')
  }
  if (child.type !== 'PAGE' && parent.type === 'DOCUMENT') {
    throw Error(
      'Only <Page> tags can be used at the top level of a document. Please wrap your elements in a <Page> element.'
    )
  }
  if (child.type === 'TEXT_INSTANCE' && parent.type !== 'TEXT') {
    // Plain text instances have to be wrapped inside <text> elements in react-pdf.
    // For convenience, we automate this here.
    parent.children.push({
      box: {},
      children: [child],
      props: {},
      style: {},
      type: 'TEXT',
    })
    return
  }
  if (child.type === 'DOCUMENT') {
    if (parent.type !== undefined) {
      throw Error('Tag <Document> can only be used at the top level.')
    }
    if (parent.doc !== undefined) {
      throw Error('Only one <Document> tag can be used.')
    }
    parent.doc = child
    child.parent = null
    return
  }
  parent.children.push(child)
  child.parent = parent
}

function createElement(tag) {
  console.log('createElement', tag)
  if (!(tag in htmlToPdfElementMap)) {
    throw Error(`Tag <${tag}> cannot be used inside a pdf!`)
  }
  return {
    box: {},
    children: [],
    props: {},
    style: {},
    type: htmlToPdfElementMap[tag],
  }
}

function createText(text) {
  console.log('createText', text)
  return {
    type: 'TEXT_INSTANCE',
    value: text,
  }
}

function setElementText(element, text) {
  console.log('setElementText', element, text)
  insert(createText(text), element, null)
}

function parentNode() {
  console.log('parentNode')
  return null
}

function createComment() {
  noop('createComment')
}
function setText() {
  noop('setText')
}
function nextSibling() {
  noop('nextSibling')
}
function querySelector() {
  noop('querySelector')
}
function setScopeId() {
  noop('setScopeId')
}
function cloneNode() {
  noop('cloneNode')
}
function insertStaticContent() {
  noop('insertStaticContent')
}
function forcePatchProp() {
  noop('forcePatchProp')
}
function remove() {
  noop('remove')
}

const nodeOps = {
  patchProp,
  insert,
  createElement,
  createText,
  setElementText,
  parentNode,
  createComment,
  setText,
  nextSibling,
  querySelector,
  setScopeId,
  cloneNode,
  insertStaticContent,
  forcePatchProp,
  remove,
}

export { nodeOps }
