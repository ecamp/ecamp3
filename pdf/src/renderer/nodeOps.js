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
  if (key === 'style') {
    // React-pdf treats style as a separate attribute
    el.style = nextVal
  } else {
    el.props[key] = nextVal
  }
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
      parent: parent,
    })
    return
  }
  if (child.type === 'DOCUMENT') {
    if (parent.type !== undefined) {
      throw Error('Tag <Document> can only be used at the top level.')
    }
    if (parent.doc !== undefined) {
      // Ignore this case, this can happen during hot reloading. Just keep the last document component.
      //throw Error('Only one <Document> tag can be used.')
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

// Operations which Vue uses while hot reloading.
function parentNode(element) {
  console.log('parentNode', element)
  return element.parent || null
}

function nextSibling(element) {
  console.log('nextSibling', element)
  if (!element.parent) return null
  const nextSiblingIndex = element.parent.children.findIndex((el) => el === element) + 1
  return element.parent.children[nextSiblingIndex] || null
}

function remove(element) {
  console.log('remove', element)
  if (!element.parent) return null
  const index = element.parent.children.findIndex((el) => el === element)
  element.parent.children.splice(index, 1)
}

function createComment() {
  noop('createComment')
}
function setText() {
  noop('setText')
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
