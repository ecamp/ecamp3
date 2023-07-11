import { styleStore } from './styleStore.js'
import camelCase from 'lodash/camelCase.js'

export const htmlToPdfElementMap = {
  Document: 'DOCUMENT',
  Page: 'PAGE',
  View: 'VIEW',
  Image: 'IMAGE',
  Text: 'TEXT',
  Link: 'LINK',
  Note: 'NOTE',
  Canvas: 'CANVAS',
  Svg: 'SVG',
  Line: 'LINE',
  Polyline: 'POLYLINE',
  Polygon: 'POLYGON',
  Path: 'PATH',
  Rect: 'RECT',
  Circle: 'CIRCLE',
  Ellipse: 'ELLIPSE',
  Tspan: 'TSPAN',
  G: 'G',
  Stop: 'STOP',
  Defs: 'DEFS',
  ClipPath: 'CLIP_PATH',
  LinearGradient: 'LINEAR_GRADIENT',
  RadialGradient: 'RADIAL_GRADIENT',
}

function noop(fn) {
  throw Error(`no-op: ${fn}`)
}

function patchProp(el, key, prevVal, nextVal) {
  if (key === 'style') {
    // React-pdf treats style as a separate attribute, not as a normal prop.
    // Also, they use camelCase property names instead of the kebab-case which CSS uses.
    const transformed = Object.fromEntries(
      Object.entries(nextVal || {}).map(([key, value]) => [camelCase(key), value])
    )
    el.style = Object.assign(el.style, transformed)
  } else if (key === 'class') {
    const styles = nextVal.split(' ').map((styleClass) => styleStore[styleClass] || {})
    el.style = Object.assign(el.style, ...styles)
  } else {
    el.props[camelCase(key)] = nextVal
  }
}

function insert(child, parent, _) {
  if (!child || !parent) return
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
    // Plain text instances have to be wrapped inside <Text> elements in react-pdf.
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

function createElement(tag, isSVG, isCustomizedBuiltIn, vnodeProps) {
  if (!(tag in htmlToPdfElementMap)) {
    throw Error(
      `Tag <${tag}> cannot be used inside a pdf. Did you forget to import a Vue component?`
    )
  }
  const camelCasedProps = Object.fromEntries(
    Object.entries(vnodeProps || {}).map(([key, value]) => [camelCase(key), value])
  )
  return {
    type: htmlToPdfElementMap[tag],
    box: {},
    style: {},
    props: camelCasedProps,
    children: [],
  }
}

function createText(text) {
  return {
    type: 'TEXT_INSTANCE',
    value: text,
  }
}

function setElementText(element, text) {
  //console.log('setElementText', element, text)
  insert(createText(text), element, null)
}

// Operations which Vue uses while hot reloading.
function parentNode(element) {
  //console.log('parentNode', element)
  return element?.parent || null
}

function nextSibling(element) {
  //console.log('nextSibling', element)
  if (!element?.parent) return null
  const nextSiblingIndex = element.parent.children.findIndex((el) => el === element) + 1
  return element.parent.children[nextSiblingIndex] || null
}

function remove(element) {
  //console.log('remove', element)
  if (!element?.parent) return null
  const index = element.parent.children.findIndex((el) => el === element)
  element.parent.children.splice(index, 1)
}

function createComment() {
  //noop('createComment')
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
