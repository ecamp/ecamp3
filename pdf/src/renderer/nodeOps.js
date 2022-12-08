const htmlToPdfElementMap = {
  page: 'PAGE'
}

function noop(fn) {
  throw Error(`no-op: ${fn}`)
}

const nodeOpsFor = (doc) => ({
  patchProp: (el, key, prevVal, nextVal) => {
    console.log('patchProp', el, key, prevVal, nextVal)
  },

  insert: (child, parent, anchor) => {
    console.log('insert', child, parent, anchor)
    parent.children.push(child)
  },

  createElement: (tag) => {
    console.log('createElement', tag)
    if (!tag in htmlToPdfElementMap) {
      throw Error(`Tag <${tag}> cannot be used inside a pdf!`)
    }
    return {
      box: {},
      children: [],
      props: {},
      style: {},
      type: htmlToPdfElementMap[tag],
    }
  },

  createText: (text) => {
    console.log('createText', text)
    return {
      box: {},
      children: [{
        type: 'TEXT_INSTANCE',
        value: text,
      }],
      props: {},
      style: {},
      type: 'TEXT',
    }
  },

  parentNode: () => {
    console.log('parentNode')
    return null
  },

  createComment: () => noop('createComment'),
  setText: () => noop('setText'),
  setElementText: () => noop('setElementText'),
  nextSibling: () => noop('nextSibling'),
  querySelector: () => noop('querySelector'),
  setScopeId: () => noop('setScopeId'),
  cloneNode: () => noop('cloneNode'),
  insertStaticContent: () => noop('insertStaticContent'),
  forcePatchProp: () => noop('forcePatchProp'),
  remove: () => noop('remove'),
})

export { nodeOpsFor }
