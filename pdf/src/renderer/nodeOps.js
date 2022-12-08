function noop(fn) {
  throw Error(`no-op: ${fn}`);
}

const nodeOpsFor = (doc) => ({
  patchProp: (el, key, prevVal, nextVal) => {
    console.log("patchProp", el, key, prevVal, nextVal);
  },

  insert: (child, parent, anchor) => {
    console.log("insert", child, parent, anchor);
  },

  createElement: (tag) => {
    console.log("createElement", tag);
  },

  createText: (text) => {
    console.log("createText", text);
  },

  parentNode: () => {
    console.log("parentNode");
    return null;
  },

  createComment: () => noop("createComment"),
  setText: () => noop("setText"),
  setElementText: () => noop("setElementText"),
  nextSibling: () => noop("nextSibling"),
  querySelector: () => noop("querySelector"),
  setScopeId: () => noop("setScopeId"),
  cloneNode: () => noop("cloneNode"),
  insertStaticContent: () => noop("insertStaticContent"),
  forcePatchProp: () => noop("forcePatchProp"),
  remove: () => noop("remove"),
});

export { nodeOpsFor };
