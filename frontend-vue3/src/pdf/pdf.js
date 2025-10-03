import dayjs from "../common/helpers/dayjs.js";
import camelCase from "lodash-es/camelCase.js";
import * as primitives from "@react-pdf/primitives";
import FontStore from "@react-pdf/font";
import layoutDocument from "@react-pdf/layout";
import PDFDocument from "@react-pdf/pdfkit";
import renderPDF from "@react-pdf/render";
import InterDisplay from "@/assets/fonts/Inter/InterDisplay-Regular.ttf";
import InterDisplayItalic from "@/assets/fonts/Inter/InterDisplay-Italic.ttf";
import InterDisplayMedium from "@/assets/fonts/Inter/InterDisplay-Medium.ttf";
import InterDisplaySemiBold from "@/assets/fonts/Inter/InterDisplay-SemiBold.ttf";
import InterDisplayBold from "@/assets/fonts/Inter/InterDisplay-Bold.ttf";
import InterDisplayBoldItalic from "@/assets/fonts/Inter/InterDisplay-BoldItalic.ttf";
import maxBy from "lodash-es/maxBy.js";
import Color from "colorjs.io";
import runes from "runes";
import minBy from "lodash-es/minBy.js";
import sortBy from "lodash-es/sortBy.js";
import keyBy from "lodash-es/keyBy.js";
import groupBy from "lodash-es/groupBy.js";
import uniqWith from "lodash-es/uniqWith.js";
import { wordHyphenation } from "@react-pdf/textkit";
const styleStore = {};
const htmlToPdfElementMap = { ...primitives };
delete htmlToPdfElementMap.__esModule;
delete htmlToPdfElementMap.TextInstance;
function noop(fn) {
  throw Error(`no-op: ${fn}`);
}
function patchProp(el, key, prevVal, nextVal) {
  if (["debug", "fixed"].includes(key) && Object.values(htmlToPdfElementMap).includes(el.type)) {
    el.props[camelCase(key)] = nextVal !== false;
  } else if (key === "style") {
    const transformed = Object.fromEntries(
      Object.entries(nextVal || {}).map(([key2, value]) => [camelCase(key2), value])
    );
    el.style = Object.assign(el.style, transformed);
  } else if (key === "class") {
    const styles = nextVal.split(" ").map((styleClass) => styleStore[styleClass] || {});
    el.style = Object.assign(el.style, ...styles);
  } else {
    el.props[camelCase(key)] = nextVal;
  }
}
function insert(child, parent, _) {
  if (!child || !parent) return;
  if (child.type === "TEXT_INSTANCE" && child.value === "") {
    return;
  }
  if (child.type === "PAGE" && parent.type !== "DOCUMENT") {
    throw Error("Tag <Page> can only be used at the top level of a document.");
  }
  if (child.type !== "PAGE" && parent.type === "DOCUMENT") {
    throw Error(
      "Only <Page> tags can be used at the top level of a document. Please wrap your elements in a <Page> element."
    );
  }
  if (child.type === "TEXT_INSTANCE" && parent.type !== "TEXT") {
    parent.children.push({
      box: {},
      children: [child],
      props: {},
      style: {},
      type: "TEXT",
      parent
    });
    return;
  }
  if (child.type === "DOCUMENT") {
    if (parent.type !== void 0) {
      throw Error("Tag <Document> can only be used at the top level.");
    }
    if (parent.doc !== void 0) ;
    parent.doc = child;
    child.parent = null;
    return;
  }
  parent.children.push(child);
  child.parent = parent;
}
function createElement(tag, isSVG, isCustomizedBuiltIn, vnodeProps) {
  if (!(tag in htmlToPdfElementMap)) {
    throw Error(
      `Tag <${tag}> cannot be used inside a pdf. Did you forget to import a Vue component?`
    );
  }
  const camelCasedProps = Object.fromEntries(
    Object.entries(vnodeProps || {}).map(([key, value]) => [camelCase(key), value])
  );
  return {
    type: htmlToPdfElementMap[tag],
    box: {},
    style: {},
    props: camelCasedProps,
    children: []
  };
}
function createText(text) {
  return {
    type: "TEXT_INSTANCE",
    value: text
  };
}
function setElementText(element, text) {
  insert(createText(text), element);
}
function parentNode(element) {
  return (element == null ? void 0 : element.parent) || null;
}
function nextSibling(element) {
  if (!(element == null ? void 0 : element.parent)) return null;
  const nextSiblingIndex = element.parent.children.findIndex((el) => el === element) + 1;
  return element.parent.children[nextSiblingIndex] || null;
}
function remove$1(element) {
  if (!(element == null ? void 0 : element.parent)) return null;
  const index = element.parent.children.findIndex((el) => el === element);
  element.parent.children.splice(index, 1);
}
function createComment() {
}
function setText() {
  noop("setText");
}
function querySelector() {
  noop("querySelector");
}
function setScopeId() {
  noop("setScopeId");
}
function cloneNode() {
  noop("cloneNode");
}
function insertStaticContent() {
  noop("insertStaticContent");
}
function forcePatchProp() {
  noop("forcePatchProp");
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
  remove: remove$1
};
/**
* @vue/shared v3.5.17
* (c) 2018-present Yuxi (Evan) You and Vue contributors
* @license MIT
**/
/*! #__NO_SIDE_EFFECTS__ */
// @__NO_SIDE_EFFECTS__
function makeMap(str) {
  const map2 = /* @__PURE__ */ Object.create(null);
  for (const key of str.split(",")) map2[key] = 1;
  return (val) => val in map2;
}
const EMPTY_OBJ = !!(process.env.NODE_ENV !== "production") ? Object.freeze({}) : {};
const EMPTY_ARR = !!(process.env.NODE_ENV !== "production") ? Object.freeze([]) : [];
const NOOP = () => {
};
const NO = () => false;
const isOn = (key) => key.charCodeAt(0) === 111 && key.charCodeAt(1) === 110 && // uppercase letter
(key.charCodeAt(2) > 122 || key.charCodeAt(2) < 97);
const isModelListener = (key) => key.startsWith("onUpdate:");
const extend = Object.assign;
const remove = (arr, el) => {
  const i2 = arr.indexOf(el);
  if (i2 > -1) {
    arr.splice(i2, 1);
  }
};
const hasOwnProperty$1 = Object.prototype.hasOwnProperty;
const hasOwn = (val, key) => hasOwnProperty$1.call(val, key);
const isArray = Array.isArray;
const isMap = (val) => toTypeString(val) === "[object Map]";
const isSet = (val) => toTypeString(val) === "[object Set]";
const isFunction = (val) => typeof val === "function";
const isString = (val) => typeof val === "string";
const isSymbol = (val) => typeof val === "symbol";
const isObject = (val) => val !== null && typeof val === "object";
const isPromise = (val) => {
  return (isObject(val) || isFunction(val)) && isFunction(val.then) && isFunction(val.catch);
};
const objectToString = Object.prototype.toString;
const toTypeString = (value) => objectToString.call(value);
const toRawType = (value) => {
  return toTypeString(value).slice(8, -1);
};
const isPlainObject = (val) => toTypeString(val) === "[object Object]";
const isIntegerKey = (key) => isString(key) && key !== "NaN" && key[0] !== "-" && "" + parseInt(key, 10) === key;
const isReservedProp = /* @__PURE__ */ makeMap(
  // the leading comma is intentional so empty string "" is also included
  ",key,ref,ref_for,ref_key,onVnodeBeforeMount,onVnodeMounted,onVnodeBeforeUpdate,onVnodeUpdated,onVnodeBeforeUnmount,onVnodeUnmounted"
);
const isBuiltInDirective = /* @__PURE__ */ makeMap(
  "bind,cloak,else-if,else,for,html,if,model,on,once,pre,show,slot,text,memo"
);
const cacheStringFunction = (fn) => {
  const cache = /* @__PURE__ */ Object.create(null);
  return (str) => {
    const hit = cache[str];
    return hit || (cache[str] = fn(str));
  };
};
const camelizeRE = /-(\w)/g;
const camelize = cacheStringFunction(
  (str) => {
    return str.replace(camelizeRE, (_, c2) => c2 ? c2.toUpperCase() : "");
  }
);
const hyphenateRE = /\B([A-Z])/g;
const hyphenate = cacheStringFunction(
  (str) => str.replace(hyphenateRE, "-$1").toLowerCase()
);
const capitalize = cacheStringFunction((str) => {
  return str.charAt(0).toUpperCase() + str.slice(1);
});
const toHandlerKey = cacheStringFunction(
  (str) => {
    const s2 = str ? `on${capitalize(str)}` : ``;
    return s2;
  }
);
const hasChanged = (value, oldValue) => !Object.is(value, oldValue);
const invokeArrayFns = (fns, ...arg) => {
  for (let i2 = 0; i2 < fns.length; i2++) {
    fns[i2](...arg);
  }
};
const def = (obj, key, value, writable = false) => {
  Object.defineProperty(obj, key, {
    configurable: true,
    enumerable: false,
    writable,
    value
  });
};
const looseToNumber = (val) => {
  const n2 = parseFloat(val);
  return isNaN(n2) ? val : n2;
};
let _globalThis;
const getGlobalThis = () => {
  return _globalThis || (_globalThis = typeof globalThis !== "undefined" ? globalThis : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : typeof global !== "undefined" ? global : {});
};
function normalizeStyle(value) {
  if (isArray(value)) {
    const res = {};
    for (let i2 = 0; i2 < value.length; i2++) {
      const item = value[i2];
      const normalized = isString(item) ? parseStringStyle(item) : normalizeStyle(item);
      if (normalized) {
        for (const key in normalized) {
          res[key] = normalized[key];
        }
      }
    }
    return res;
  } else if (isString(value) || isObject(value)) {
    return value;
  }
}
const listDelimiterRE = /;(?![^(]*\))/g;
const propertyDelimiterRE = /:([^]+)/;
const styleCommentRE = /\/\*[^]*?\*\//g;
function parseStringStyle(cssText) {
  const ret = {};
  cssText.replace(styleCommentRE, "").split(listDelimiterRE).forEach((item) => {
    if (item) {
      const tmp = item.split(propertyDelimiterRE);
      tmp.length > 1 && (ret[tmp[0].trim()] = tmp[1].trim());
    }
  });
  return ret;
}
function normalizeClass(value) {
  let res = "";
  if (isString(value)) {
    res = value;
  } else if (isArray(value)) {
    for (let i2 = 0; i2 < value.length; i2++) {
      const normalized = normalizeClass(value[i2]);
      if (normalized) {
        res += normalized + " ";
      }
    }
  } else if (isObject(value)) {
    for (const name in value) {
      if (value[name]) {
        res += name + " ";
      }
    }
  }
  return res.trim();
}
const isRef$1 = (val) => {
  return !!(val && val["__v_isRef"] === true);
};
const toDisplayString = (val) => {
  return isString(val) ? val : val == null ? "" : isArray(val) || isObject(val) && (val.toString === objectToString || !isFunction(val.toString)) ? isRef$1(val) ? toDisplayString(val.value) : JSON.stringify(val, replacer, 2) : String(val);
};
const replacer = (_key, val) => {
  if (isRef$1(val)) {
    return replacer(_key, val.value);
  } else if (isMap(val)) {
    return {
      [`Map(${val.size})`]: [...val.entries()].reduce(
        (entries, [key, val2], i2) => {
          entries[stringifySymbol(key, i2) + " =>"] = val2;
          return entries;
        },
        {}
      )
    };
  } else if (isSet(val)) {
    return {
      [`Set(${val.size})`]: [...val.values()].map((v) => stringifySymbol(v))
    };
  } else if (isSymbol(val)) {
    return stringifySymbol(val);
  } else if (isObject(val) && !isArray(val) && !isPlainObject(val)) {
    return String(val);
  }
  return val;
};
const stringifySymbol = (v, i2 = "") => {
  var _a;
  return (
    // Symbol.description in es2019+ so we need to cast here to pass
    // the lib: es2016 check
    isSymbol(v) ? `Symbol(${(_a = v.description) != null ? _a : i2})` : v
  );
};
/**
* @vue/reactivity v3.5.17
* (c) 2018-present Yuxi (Evan) You and Vue contributors
* @license MIT
**/
function warn(msg, ...args) {
  console.warn(`[Vue warn] ${msg}`, ...args);
}
let activeEffectScope;
class EffectScope {
  constructor(detached = false) {
    this.detached = detached;
    this._active = true;
    this._on = 0;
    this.effects = [];
    this.cleanups = [];
    this._isPaused = false;
    this.parent = activeEffectScope;
    if (!detached && activeEffectScope) {
      this.index = (activeEffectScope.scopes || (activeEffectScope.scopes = [])).push(
        this
      ) - 1;
    }
  }
  get active() {
    return this._active;
  }
  pause() {
    if (this._active) {
      this._isPaused = true;
      let i2, l;
      if (this.scopes) {
        for (i2 = 0, l = this.scopes.length; i2 < l; i2++) {
          this.scopes[i2].pause();
        }
      }
      for (i2 = 0, l = this.effects.length; i2 < l; i2++) {
        this.effects[i2].pause();
      }
    }
  }
  /**
   * Resumes the effect scope, including all child scopes and effects.
   */
  resume() {
    if (this._active) {
      if (this._isPaused) {
        this._isPaused = false;
        let i2, l;
        if (this.scopes) {
          for (i2 = 0, l = this.scopes.length; i2 < l; i2++) {
            this.scopes[i2].resume();
          }
        }
        for (i2 = 0, l = this.effects.length; i2 < l; i2++) {
          this.effects[i2].resume();
        }
      }
    }
  }
  run(fn) {
    if (this._active) {
      const currentEffectScope = activeEffectScope;
      try {
        activeEffectScope = this;
        return fn();
      } finally {
        activeEffectScope = currentEffectScope;
      }
    } else if (!!(process.env.NODE_ENV !== "production")) {
      warn(`cannot run an inactive effect scope.`);
    }
  }
  /**
   * This should only be called on non-detached scopes
   * @internal
   */
  on() {
    if (++this._on === 1) {
      this.prevScope = activeEffectScope;
      activeEffectScope = this;
    }
  }
  /**
   * This should only be called on non-detached scopes
   * @internal
   */
  off() {
    if (this._on > 0 && --this._on === 0) {
      activeEffectScope = this.prevScope;
      this.prevScope = void 0;
    }
  }
  stop(fromParent) {
    if (this._active) {
      this._active = false;
      let i2, l;
      for (i2 = 0, l = this.effects.length; i2 < l; i2++) {
        this.effects[i2].stop();
      }
      this.effects.length = 0;
      for (i2 = 0, l = this.cleanups.length; i2 < l; i2++) {
        this.cleanups[i2]();
      }
      this.cleanups.length = 0;
      if (this.scopes) {
        for (i2 = 0, l = this.scopes.length; i2 < l; i2++) {
          this.scopes[i2].stop(true);
        }
        this.scopes.length = 0;
      }
      if (!this.detached && this.parent && !fromParent) {
        const last = this.parent.scopes.pop();
        if (last && last !== this) {
          this.parent.scopes[this.index] = last;
          last.index = this.index;
        }
      }
      this.parent = void 0;
    }
  }
}
function getCurrentScope() {
  return activeEffectScope;
}
let activeSub;
const pausedQueueEffects = /* @__PURE__ */ new WeakSet();
class ReactiveEffect {
  constructor(fn) {
    this.fn = fn;
    this.deps = void 0;
    this.depsTail = void 0;
    this.flags = 1 | 4;
    this.next = void 0;
    this.cleanup = void 0;
    this.scheduler = void 0;
    if (activeEffectScope && activeEffectScope.active) {
      activeEffectScope.effects.push(this);
    }
  }
  pause() {
    this.flags |= 64;
  }
  resume() {
    if (this.flags & 64) {
      this.flags &= -65;
      if (pausedQueueEffects.has(this)) {
        pausedQueueEffects.delete(this);
        this.trigger();
      }
    }
  }
  /**
   * @internal
   */
  notify() {
    if (this.flags & 2 && !(this.flags & 32)) {
      return;
    }
    if (!(this.flags & 8)) {
      batch(this);
    }
  }
  run() {
    if (!(this.flags & 1)) {
      return this.fn();
    }
    this.flags |= 2;
    cleanupEffect(this);
    prepareDeps(this);
    const prevEffect = activeSub;
    const prevShouldTrack = shouldTrack;
    activeSub = this;
    shouldTrack = true;
    try {
      return this.fn();
    } finally {
      if (!!(process.env.NODE_ENV !== "production") && activeSub !== this) {
        warn(
          "Active effect was not restored correctly - this is likely a Vue internal bug."
        );
      }
      cleanupDeps(this);
      activeSub = prevEffect;
      shouldTrack = prevShouldTrack;
      this.flags &= -3;
    }
  }
  stop() {
    if (this.flags & 1) {
      for (let link = this.deps; link; link = link.nextDep) {
        removeSub(link);
      }
      this.deps = this.depsTail = void 0;
      cleanupEffect(this);
      this.onStop && this.onStop();
      this.flags &= -2;
    }
  }
  trigger() {
    if (this.flags & 64) {
      pausedQueueEffects.add(this);
    } else if (this.scheduler) {
      this.scheduler();
    } else {
      this.runIfDirty();
    }
  }
  /**
   * @internal
   */
  runIfDirty() {
    if (isDirty(this)) {
      this.run();
    }
  }
  get dirty() {
    return isDirty(this);
  }
}
let batchDepth = 0;
let batchedSub;
let batchedComputed;
function batch(sub, isComputed = false) {
  sub.flags |= 8;
  if (isComputed) {
    sub.next = batchedComputed;
    batchedComputed = sub;
    return;
  }
  sub.next = batchedSub;
  batchedSub = sub;
}
function startBatch() {
  batchDepth++;
}
function endBatch() {
  if (--batchDepth > 0) {
    return;
  }
  if (batchedComputed) {
    let e2 = batchedComputed;
    batchedComputed = void 0;
    while (e2) {
      const next = e2.next;
      e2.next = void 0;
      e2.flags &= -9;
      e2 = next;
    }
  }
  let error;
  while (batchedSub) {
    let e2 = batchedSub;
    batchedSub = void 0;
    while (e2) {
      const next = e2.next;
      e2.next = void 0;
      e2.flags &= -9;
      if (e2.flags & 1) {
        try {
          ;
          e2.trigger();
        } catch (err) {
          if (!error) error = err;
        }
      }
      e2 = next;
    }
  }
  if (error) throw error;
}
function prepareDeps(sub) {
  for (let link = sub.deps; link; link = link.nextDep) {
    link.version = -1;
    link.prevActiveLink = link.dep.activeLink;
    link.dep.activeLink = link;
  }
}
function cleanupDeps(sub) {
  let head;
  let tail = sub.depsTail;
  let link = tail;
  while (link) {
    const prev = link.prevDep;
    if (link.version === -1) {
      if (link === tail) tail = prev;
      removeSub(link);
      removeDep(link);
    } else {
      head = link;
    }
    link.dep.activeLink = link.prevActiveLink;
    link.prevActiveLink = void 0;
    link = prev;
  }
  sub.deps = head;
  sub.depsTail = tail;
}
function isDirty(sub) {
  for (let link = sub.deps; link; link = link.nextDep) {
    if (link.dep.version !== link.version || link.dep.computed && (refreshComputed(link.dep.computed) || link.dep.version !== link.version)) {
      return true;
    }
  }
  if (sub._dirty) {
    return true;
  }
  return false;
}
function refreshComputed(computed2) {
  if (computed2.flags & 4 && !(computed2.flags & 16)) {
    return;
  }
  computed2.flags &= -17;
  if (computed2.globalVersion === globalVersion) {
    return;
  }
  computed2.globalVersion = globalVersion;
  if (!computed2.isSSR && computed2.flags & 128 && (!computed2.deps && !computed2._dirty || !isDirty(computed2))) {
    return;
  }
  computed2.flags |= 2;
  const dep = computed2.dep;
  const prevSub = activeSub;
  const prevShouldTrack = shouldTrack;
  activeSub = computed2;
  shouldTrack = true;
  try {
    prepareDeps(computed2);
    const value = computed2.fn(computed2._value);
    if (dep.version === 0 || hasChanged(value, computed2._value)) {
      computed2.flags |= 128;
      computed2._value = value;
      dep.version++;
    }
  } catch (err) {
    dep.version++;
    throw err;
  } finally {
    activeSub = prevSub;
    shouldTrack = prevShouldTrack;
    cleanupDeps(computed2);
    computed2.flags &= -3;
  }
}
function removeSub(link, soft = false) {
  const { dep, prevSub, nextSub } = link;
  if (prevSub) {
    prevSub.nextSub = nextSub;
    link.prevSub = void 0;
  }
  if (nextSub) {
    nextSub.prevSub = prevSub;
    link.nextSub = void 0;
  }
  if (!!(process.env.NODE_ENV !== "production") && dep.subsHead === link) {
    dep.subsHead = nextSub;
  }
  if (dep.subs === link) {
    dep.subs = prevSub;
    if (!prevSub && dep.computed) {
      dep.computed.flags &= -5;
      for (let l = dep.computed.deps; l; l = l.nextDep) {
        removeSub(l, true);
      }
    }
  }
  if (!soft && !--dep.sc && dep.map) {
    dep.map.delete(dep.key);
  }
}
function removeDep(link) {
  const { prevDep, nextDep } = link;
  if (prevDep) {
    prevDep.nextDep = nextDep;
    link.prevDep = void 0;
  }
  if (nextDep) {
    nextDep.prevDep = prevDep;
    link.nextDep = void 0;
  }
}
let shouldTrack = true;
const trackStack = [];
function pauseTracking() {
  trackStack.push(shouldTrack);
  shouldTrack = false;
}
function resetTracking() {
  const last = trackStack.pop();
  shouldTrack = last === void 0 ? true : last;
}
function cleanupEffect(e2) {
  const { cleanup } = e2;
  e2.cleanup = void 0;
  if (cleanup) {
    const prevSub = activeSub;
    activeSub = void 0;
    try {
      cleanup();
    } finally {
      activeSub = prevSub;
    }
  }
}
let globalVersion = 0;
class Link {
  constructor(sub, dep) {
    this.sub = sub;
    this.dep = dep;
    this.version = dep.version;
    this.nextDep = this.prevDep = this.nextSub = this.prevSub = this.prevActiveLink = void 0;
  }
}
class Dep {
  // TODO isolatedDeclarations "__v_skip"
  constructor(computed2) {
    this.computed = computed2;
    this.version = 0;
    this.activeLink = void 0;
    this.subs = void 0;
    this.map = void 0;
    this.key = void 0;
    this.sc = 0;
    this.__v_skip = true;
    if (!!(process.env.NODE_ENV !== "production")) {
      this.subsHead = void 0;
    }
  }
  track(debugInfo) {
    if (!activeSub || !shouldTrack || activeSub === this.computed) {
      return;
    }
    let link = this.activeLink;
    if (link === void 0 || link.sub !== activeSub) {
      link = this.activeLink = new Link(activeSub, this);
      if (!activeSub.deps) {
        activeSub.deps = activeSub.depsTail = link;
      } else {
        link.prevDep = activeSub.depsTail;
        activeSub.depsTail.nextDep = link;
        activeSub.depsTail = link;
      }
      addSub(link);
    } else if (link.version === -1) {
      link.version = this.version;
      if (link.nextDep) {
        const next = link.nextDep;
        next.prevDep = link.prevDep;
        if (link.prevDep) {
          link.prevDep.nextDep = next;
        }
        link.prevDep = activeSub.depsTail;
        link.nextDep = void 0;
        activeSub.depsTail.nextDep = link;
        activeSub.depsTail = link;
        if (activeSub.deps === link) {
          activeSub.deps = next;
        }
      }
    }
    if (!!(process.env.NODE_ENV !== "production") && activeSub.onTrack) {
      activeSub.onTrack(
        extend(
          {
            effect: activeSub
          },
          debugInfo
        )
      );
    }
    return link;
  }
  trigger(debugInfo) {
    this.version++;
    globalVersion++;
    this.notify(debugInfo);
  }
  notify(debugInfo) {
    startBatch();
    try {
      if (!!(process.env.NODE_ENV !== "production")) {
        for (let head = this.subsHead; head; head = head.nextSub) {
          if (head.sub.onTrigger && !(head.sub.flags & 8)) {
            head.sub.onTrigger(
              extend(
                {
                  effect: head.sub
                },
                debugInfo
              )
            );
          }
        }
      }
      for (let link = this.subs; link; link = link.prevSub) {
        if (link.sub.notify()) {
          ;
          link.sub.dep.notify();
        }
      }
    } finally {
      endBatch();
    }
  }
}
function addSub(link) {
  link.dep.sc++;
  if (link.sub.flags & 4) {
    const computed2 = link.dep.computed;
    if (computed2 && !link.dep.subs) {
      computed2.flags |= 4 | 16;
      for (let l = computed2.deps; l; l = l.nextDep) {
        addSub(l);
      }
    }
    const currentTail = link.dep.subs;
    if (currentTail !== link) {
      link.prevSub = currentTail;
      if (currentTail) currentTail.nextSub = link;
    }
    if (!!(process.env.NODE_ENV !== "production") && link.dep.subsHead === void 0) {
      link.dep.subsHead = link;
    }
    link.dep.subs = link;
  }
}
const targetMap = /* @__PURE__ */ new WeakMap();
const ITERATE_KEY = Symbol(
  !!(process.env.NODE_ENV !== "production") ? "Object iterate" : ""
);
const MAP_KEY_ITERATE_KEY = Symbol(
  !!(process.env.NODE_ENV !== "production") ? "Map keys iterate" : ""
);
const ARRAY_ITERATE_KEY = Symbol(
  !!(process.env.NODE_ENV !== "production") ? "Array iterate" : ""
);
function track(target, type, key) {
  if (shouldTrack && activeSub) {
    let depsMap = targetMap.get(target);
    if (!depsMap) {
      targetMap.set(target, depsMap = /* @__PURE__ */ new Map());
    }
    let dep = depsMap.get(key);
    if (!dep) {
      depsMap.set(key, dep = new Dep());
      dep.map = depsMap;
      dep.key = key;
    }
    if (!!(process.env.NODE_ENV !== "production")) {
      dep.track({
        target,
        type,
        key
      });
    } else {
      dep.track();
    }
  }
}
function trigger(target, type, key, newValue, oldValue, oldTarget) {
  const depsMap = targetMap.get(target);
  if (!depsMap) {
    globalVersion++;
    return;
  }
  const run = (dep) => {
    if (dep) {
      if (!!(process.env.NODE_ENV !== "production")) {
        dep.trigger({
          target,
          type,
          key,
          newValue,
          oldValue,
          oldTarget
        });
      } else {
        dep.trigger();
      }
    }
  };
  startBatch();
  if (type === "clear") {
    depsMap.forEach(run);
  } else {
    const targetIsArray = isArray(target);
    const isArrayIndex = targetIsArray && isIntegerKey(key);
    if (targetIsArray && key === "length") {
      const newLength = Number(newValue);
      depsMap.forEach((dep, key2) => {
        if (key2 === "length" || key2 === ARRAY_ITERATE_KEY || !isSymbol(key2) && key2 >= newLength) {
          run(dep);
        }
      });
    } else {
      if (key !== void 0 || depsMap.has(void 0)) {
        run(depsMap.get(key));
      }
      if (isArrayIndex) {
        run(depsMap.get(ARRAY_ITERATE_KEY));
      }
      switch (type) {
        case "add":
          if (!targetIsArray) {
            run(depsMap.get(ITERATE_KEY));
            if (isMap(target)) {
              run(depsMap.get(MAP_KEY_ITERATE_KEY));
            }
          } else if (isArrayIndex) {
            run(depsMap.get("length"));
          }
          break;
        case "delete":
          if (!targetIsArray) {
            run(depsMap.get(ITERATE_KEY));
            if (isMap(target)) {
              run(depsMap.get(MAP_KEY_ITERATE_KEY));
            }
          }
          break;
        case "set":
          if (isMap(target)) {
            run(depsMap.get(ITERATE_KEY));
          }
          break;
      }
    }
  }
  endBatch();
}
function reactiveReadArray(array) {
  const raw = toRaw(array);
  if (raw === array) return raw;
  track(raw, "iterate", ARRAY_ITERATE_KEY);
  return isShallow(array) ? raw : raw.map(toReactive);
}
function shallowReadArray(arr) {
  track(arr = toRaw(arr), "iterate", ARRAY_ITERATE_KEY);
  return arr;
}
const arrayInstrumentations = {
  __proto__: null,
  [Symbol.iterator]() {
    return iterator(this, Symbol.iterator, toReactive);
  },
  concat(...args) {
    return reactiveReadArray(this).concat(
      ...args.map((x) => isArray(x) ? reactiveReadArray(x) : x)
    );
  },
  entries() {
    return iterator(this, "entries", (value) => {
      value[1] = toReactive(value[1]);
      return value;
    });
  },
  every(fn, thisArg) {
    return apply(this, "every", fn, thisArg, void 0, arguments);
  },
  filter(fn, thisArg) {
    return apply(this, "filter", fn, thisArg, (v) => v.map(toReactive), arguments);
  },
  find(fn, thisArg) {
    return apply(this, "find", fn, thisArg, toReactive, arguments);
  },
  findIndex(fn, thisArg) {
    return apply(this, "findIndex", fn, thisArg, void 0, arguments);
  },
  findLast(fn, thisArg) {
    return apply(this, "findLast", fn, thisArg, toReactive, arguments);
  },
  findLastIndex(fn, thisArg) {
    return apply(this, "findLastIndex", fn, thisArg, void 0, arguments);
  },
  // flat, flatMap could benefit from ARRAY_ITERATE but are not straight-forward to implement
  forEach(fn, thisArg) {
    return apply(this, "forEach", fn, thisArg, void 0, arguments);
  },
  includes(...args) {
    return searchProxy(this, "includes", args);
  },
  indexOf(...args) {
    return searchProxy(this, "indexOf", args);
  },
  join(separator) {
    return reactiveReadArray(this).join(separator);
  },
  // keys() iterator only reads `length`, no optimisation required
  lastIndexOf(...args) {
    return searchProxy(this, "lastIndexOf", args);
  },
  map(fn, thisArg) {
    return apply(this, "map", fn, thisArg, void 0, arguments);
  },
  pop() {
    return noTracking(this, "pop");
  },
  push(...args) {
    return noTracking(this, "push", args);
  },
  reduce(fn, ...args) {
    return reduce(this, "reduce", fn, args);
  },
  reduceRight(fn, ...args) {
    return reduce(this, "reduceRight", fn, args);
  },
  shift() {
    return noTracking(this, "shift");
  },
  // slice could use ARRAY_ITERATE but also seems to beg for range tracking
  some(fn, thisArg) {
    return apply(this, "some", fn, thisArg, void 0, arguments);
  },
  splice(...args) {
    return noTracking(this, "splice", args);
  },
  toReversed() {
    return reactiveReadArray(this).toReversed();
  },
  toSorted(comparer) {
    return reactiveReadArray(this).toSorted(comparer);
  },
  toSpliced(...args) {
    return reactiveReadArray(this).toSpliced(...args);
  },
  unshift(...args) {
    return noTracking(this, "unshift", args);
  },
  values() {
    return iterator(this, "values", toReactive);
  }
};
function iterator(self2, method, wrapValue) {
  const arr = shallowReadArray(self2);
  const iter = arr[method]();
  if (arr !== self2 && !isShallow(self2)) {
    iter._next = iter.next;
    iter.next = () => {
      const result = iter._next();
      if (result.value) {
        result.value = wrapValue(result.value);
      }
      return result;
    };
  }
  return iter;
}
const arrayProto = Array.prototype;
function apply(self2, method, fn, thisArg, wrappedRetFn, args) {
  const arr = shallowReadArray(self2);
  const needsWrap = arr !== self2 && !isShallow(self2);
  const methodFn = arr[method];
  if (methodFn !== arrayProto[method]) {
    const result2 = methodFn.apply(self2, args);
    return needsWrap ? toReactive(result2) : result2;
  }
  let wrappedFn = fn;
  if (arr !== self2) {
    if (needsWrap) {
      wrappedFn = function(item, index) {
        return fn.call(this, toReactive(item), index, self2);
      };
    } else if (fn.length > 2) {
      wrappedFn = function(item, index) {
        return fn.call(this, item, index, self2);
      };
    }
  }
  const result = methodFn.call(arr, wrappedFn, thisArg);
  return needsWrap && wrappedRetFn ? wrappedRetFn(result) : result;
}
function reduce(self2, method, fn, args) {
  const arr = shallowReadArray(self2);
  let wrappedFn = fn;
  if (arr !== self2) {
    if (!isShallow(self2)) {
      wrappedFn = function(acc, item, index) {
        return fn.call(this, acc, toReactive(item), index, self2);
      };
    } else if (fn.length > 3) {
      wrappedFn = function(acc, item, index) {
        return fn.call(this, acc, item, index, self2);
      };
    }
  }
  return arr[method](wrappedFn, ...args);
}
function searchProxy(self2, method, args) {
  const arr = toRaw(self2);
  track(arr, "iterate", ARRAY_ITERATE_KEY);
  const res = arr[method](...args);
  if ((res === -1 || res === false) && isProxy(args[0])) {
    args[0] = toRaw(args[0]);
    return arr[method](...args);
  }
  return res;
}
function noTracking(self2, method, args = []) {
  pauseTracking();
  startBatch();
  const res = toRaw(self2)[method].apply(self2, args);
  endBatch();
  resetTracking();
  return res;
}
const isNonTrackableKeys = /* @__PURE__ */ makeMap(`__proto__,__v_isRef,__isVue`);
const builtInSymbols = new Set(
  /* @__PURE__ */ Object.getOwnPropertyNames(Symbol).filter((key) => key !== "arguments" && key !== "caller").map((key) => Symbol[key]).filter(isSymbol)
);
function hasOwnProperty(key) {
  if (!isSymbol(key)) key = String(key);
  const obj = toRaw(this);
  track(obj, "has", key);
  return obj.hasOwnProperty(key);
}
class BaseReactiveHandler {
  constructor(_isReadonly = false, _isShallow = false) {
    this._isReadonly = _isReadonly;
    this._isShallow = _isShallow;
  }
  get(target, key, receiver) {
    if (key === "__v_skip") return target["__v_skip"];
    const isReadonly2 = this._isReadonly, isShallow2 = this._isShallow;
    if (key === "__v_isReactive") {
      return !isReadonly2;
    } else if (key === "__v_isReadonly") {
      return isReadonly2;
    } else if (key === "__v_isShallow") {
      return isShallow2;
    } else if (key === "__v_raw") {
      if (receiver === (isReadonly2 ? isShallow2 ? shallowReadonlyMap : readonlyMap : isShallow2 ? shallowReactiveMap : reactiveMap).get(target) || // receiver is not the reactive proxy, but has the same prototype
      // this means the receiver is a user proxy of the reactive proxy
      Object.getPrototypeOf(target) === Object.getPrototypeOf(receiver)) {
        return target;
      }
      return;
    }
    const targetIsArray = isArray(target);
    if (!isReadonly2) {
      let fn;
      if (targetIsArray && (fn = arrayInstrumentations[key])) {
        return fn;
      }
      if (key === "hasOwnProperty") {
        return hasOwnProperty;
      }
    }
    const res = Reflect.get(
      target,
      key,
      // if this is a proxy wrapping a ref, return methods using the raw ref
      // as receiver so that we don't have to call `toRaw` on the ref in all
      // its class methods
      isRef(target) ? target : receiver
    );
    if (isSymbol(key) ? builtInSymbols.has(key) : isNonTrackableKeys(key)) {
      return res;
    }
    if (!isReadonly2) {
      track(target, "get", key);
    }
    if (isShallow2) {
      return res;
    }
    if (isRef(res)) {
      return targetIsArray && isIntegerKey(key) ? res : res.value;
    }
    if (isObject(res)) {
      return isReadonly2 ? readonly(res) : reactive(res);
    }
    return res;
  }
}
class MutableReactiveHandler extends BaseReactiveHandler {
  constructor(isShallow2 = false) {
    super(false, isShallow2);
  }
  set(target, key, value, receiver) {
    let oldValue = target[key];
    if (!this._isShallow) {
      const isOldValueReadonly = isReadonly(oldValue);
      if (!isShallow(value) && !isReadonly(value)) {
        oldValue = toRaw(oldValue);
        value = toRaw(value);
      }
      if (!isArray(target) && isRef(oldValue) && !isRef(value)) {
        if (isOldValueReadonly) {
          return false;
        } else {
          oldValue.value = value;
          return true;
        }
      }
    }
    const hadKey = isArray(target) && isIntegerKey(key) ? Number(key) < target.length : hasOwn(target, key);
    const result = Reflect.set(
      target,
      key,
      value,
      isRef(target) ? target : receiver
    );
    if (target === toRaw(receiver)) {
      if (!hadKey) {
        trigger(target, "add", key, value);
      } else if (hasChanged(value, oldValue)) {
        trigger(target, "set", key, value, oldValue);
      }
    }
    return result;
  }
  deleteProperty(target, key) {
    const hadKey = hasOwn(target, key);
    const oldValue = target[key];
    const result = Reflect.deleteProperty(target, key);
    if (result && hadKey) {
      trigger(target, "delete", key, void 0, oldValue);
    }
    return result;
  }
  has(target, key) {
    const result = Reflect.has(target, key);
    if (!isSymbol(key) || !builtInSymbols.has(key)) {
      track(target, "has", key);
    }
    return result;
  }
  ownKeys(target) {
    track(
      target,
      "iterate",
      isArray(target) ? "length" : ITERATE_KEY
    );
    return Reflect.ownKeys(target);
  }
}
class ReadonlyReactiveHandler extends BaseReactiveHandler {
  constructor(isShallow2 = false) {
    super(true, isShallow2);
  }
  set(target, key) {
    if (!!(process.env.NODE_ENV !== "production")) {
      warn(
        `Set operation on key "${String(key)}" failed: target is readonly.`,
        target
      );
    }
    return true;
  }
  deleteProperty(target, key) {
    if (!!(process.env.NODE_ENV !== "production")) {
      warn(
        `Delete operation on key "${String(key)}" failed: target is readonly.`,
        target
      );
    }
    return true;
  }
}
const mutableHandlers = /* @__PURE__ */ new MutableReactiveHandler();
const readonlyHandlers = /* @__PURE__ */ new ReadonlyReactiveHandler();
const shallowReactiveHandlers = /* @__PURE__ */ new MutableReactiveHandler(true);
const shallowReadonlyHandlers = /* @__PURE__ */ new ReadonlyReactiveHandler(true);
const toShallow = (value) => value;
const getProto = (v) => Reflect.getPrototypeOf(v);
function createIterableMethod(method, isReadonly2, isShallow2) {
  return function(...args) {
    const target = this["__v_raw"];
    const rawTarget = toRaw(target);
    const targetIsMap = isMap(rawTarget);
    const isPair = method === "entries" || method === Symbol.iterator && targetIsMap;
    const isKeyOnly = method === "keys" && targetIsMap;
    const innerIterator = target[method](...args);
    const wrap = isShallow2 ? toShallow : isReadonly2 ? toReadonly : toReactive;
    !isReadonly2 && track(
      rawTarget,
      "iterate",
      isKeyOnly ? MAP_KEY_ITERATE_KEY : ITERATE_KEY
    );
    return {
      // iterator protocol
      next() {
        const { value, done } = innerIterator.next();
        return done ? { value, done } : {
          value: isPair ? [wrap(value[0]), wrap(value[1])] : wrap(value),
          done
        };
      },
      // iterable protocol
      [Symbol.iterator]() {
        return this;
      }
    };
  };
}
function createReadonlyMethod(type) {
  return function(...args) {
    if (!!(process.env.NODE_ENV !== "production")) {
      const key = args[0] ? `on key "${args[0]}" ` : ``;
      warn(
        `${capitalize(type)} operation ${key}failed: target is readonly.`,
        toRaw(this)
      );
    }
    return type === "delete" ? false : type === "clear" ? void 0 : this;
  };
}
function createInstrumentations(readonly2, shallow) {
  const instrumentations = {
    get(key) {
      const target = this["__v_raw"];
      const rawTarget = toRaw(target);
      const rawKey = toRaw(key);
      if (!readonly2) {
        if (hasChanged(key, rawKey)) {
          track(rawTarget, "get", key);
        }
        track(rawTarget, "get", rawKey);
      }
      const { has } = getProto(rawTarget);
      const wrap = shallow ? toShallow : readonly2 ? toReadonly : toReactive;
      if (has.call(rawTarget, key)) {
        return wrap(target.get(key));
      } else if (has.call(rawTarget, rawKey)) {
        return wrap(target.get(rawKey));
      } else if (target !== rawTarget) {
        target.get(key);
      }
    },
    get size() {
      const target = this["__v_raw"];
      !readonly2 && track(toRaw(target), "iterate", ITERATE_KEY);
      return Reflect.get(target, "size", target);
    },
    has(key) {
      const target = this["__v_raw"];
      const rawTarget = toRaw(target);
      const rawKey = toRaw(key);
      if (!readonly2) {
        if (hasChanged(key, rawKey)) {
          track(rawTarget, "has", key);
        }
        track(rawTarget, "has", rawKey);
      }
      return key === rawKey ? target.has(key) : target.has(key) || target.has(rawKey);
    },
    forEach(callback, thisArg) {
      const observed = this;
      const target = observed["__v_raw"];
      const rawTarget = toRaw(target);
      const wrap = shallow ? toShallow : readonly2 ? toReadonly : toReactive;
      !readonly2 && track(rawTarget, "iterate", ITERATE_KEY);
      return target.forEach((value, key) => {
        return callback.call(thisArg, wrap(value), wrap(key), observed);
      });
    }
  };
  extend(
    instrumentations,
    readonly2 ? {
      add: createReadonlyMethod("add"),
      set: createReadonlyMethod("set"),
      delete: createReadonlyMethod("delete"),
      clear: createReadonlyMethod("clear")
    } : {
      add(value) {
        if (!shallow && !isShallow(value) && !isReadonly(value)) {
          value = toRaw(value);
        }
        const target = toRaw(this);
        const proto = getProto(target);
        const hadKey = proto.has.call(target, value);
        if (!hadKey) {
          target.add(value);
          trigger(target, "add", value, value);
        }
        return this;
      },
      set(key, value) {
        if (!shallow && !isShallow(value) && !isReadonly(value)) {
          value = toRaw(value);
        }
        const target = toRaw(this);
        const { has, get } = getProto(target);
        let hadKey = has.call(target, key);
        if (!hadKey) {
          key = toRaw(key);
          hadKey = has.call(target, key);
        } else if (!!(process.env.NODE_ENV !== "production")) {
          checkIdentityKeys(target, has, key);
        }
        const oldValue = get.call(target, key);
        target.set(key, value);
        if (!hadKey) {
          trigger(target, "add", key, value);
        } else if (hasChanged(value, oldValue)) {
          trigger(target, "set", key, value, oldValue);
        }
        return this;
      },
      delete(key) {
        const target = toRaw(this);
        const { has, get } = getProto(target);
        let hadKey = has.call(target, key);
        if (!hadKey) {
          key = toRaw(key);
          hadKey = has.call(target, key);
        } else if (!!(process.env.NODE_ENV !== "production")) {
          checkIdentityKeys(target, has, key);
        }
        const oldValue = get ? get.call(target, key) : void 0;
        const result = target.delete(key);
        if (hadKey) {
          trigger(target, "delete", key, void 0, oldValue);
        }
        return result;
      },
      clear() {
        const target = toRaw(this);
        const hadItems = target.size !== 0;
        const oldTarget = !!(process.env.NODE_ENV !== "production") ? isMap(target) ? new Map(target) : new Set(target) : void 0;
        const result = target.clear();
        if (hadItems) {
          trigger(
            target,
            "clear",
            void 0,
            void 0,
            oldTarget
          );
        }
        return result;
      }
    }
  );
  const iteratorMethods = [
    "keys",
    "values",
    "entries",
    Symbol.iterator
  ];
  iteratorMethods.forEach((method) => {
    instrumentations[method] = createIterableMethod(method, readonly2, shallow);
  });
  return instrumentations;
}
function createInstrumentationGetter(isReadonly2, shallow) {
  const instrumentations = createInstrumentations(isReadonly2, shallow);
  return (target, key, receiver) => {
    if (key === "__v_isReactive") {
      return !isReadonly2;
    } else if (key === "__v_isReadonly") {
      return isReadonly2;
    } else if (key === "__v_raw") {
      return target;
    }
    return Reflect.get(
      hasOwn(instrumentations, key) && key in target ? instrumentations : target,
      key,
      receiver
    );
  };
}
const mutableCollectionHandlers = {
  get: /* @__PURE__ */ createInstrumentationGetter(false, false)
};
const shallowCollectionHandlers = {
  get: /* @__PURE__ */ createInstrumentationGetter(false, true)
};
const readonlyCollectionHandlers = {
  get: /* @__PURE__ */ createInstrumentationGetter(true, false)
};
const shallowReadonlyCollectionHandlers = {
  get: /* @__PURE__ */ createInstrumentationGetter(true, true)
};
function checkIdentityKeys(target, has, key) {
  const rawKey = toRaw(key);
  if (rawKey !== key && has.call(target, rawKey)) {
    const type = toRawType(target);
    warn(
      `Reactive ${type} contains both the raw and reactive versions of the same object${type === `Map` ? ` as keys` : ``}, which can lead to inconsistencies. Avoid differentiating between the raw and reactive versions of an object and only use the reactive version if possible.`
    );
  }
}
const reactiveMap = /* @__PURE__ */ new WeakMap();
const shallowReactiveMap = /* @__PURE__ */ new WeakMap();
const readonlyMap = /* @__PURE__ */ new WeakMap();
const shallowReadonlyMap = /* @__PURE__ */ new WeakMap();
function targetTypeMap(rawType) {
  switch (rawType) {
    case "Object":
    case "Array":
      return 1;
    case "Map":
    case "Set":
    case "WeakMap":
    case "WeakSet":
      return 2;
    default:
      return 0;
  }
}
function getTargetType(value) {
  return value["__v_skip"] || !Object.isExtensible(value) ? 0 : targetTypeMap(toRawType(value));
}
function reactive(target) {
  if (isReadonly(target)) {
    return target;
  }
  return createReactiveObject(
    target,
    false,
    mutableHandlers,
    mutableCollectionHandlers,
    reactiveMap
  );
}
function shallowReactive(target) {
  return createReactiveObject(
    target,
    false,
    shallowReactiveHandlers,
    shallowCollectionHandlers,
    shallowReactiveMap
  );
}
function readonly(target) {
  return createReactiveObject(
    target,
    true,
    readonlyHandlers,
    readonlyCollectionHandlers,
    readonlyMap
  );
}
function shallowReadonly(target) {
  return createReactiveObject(
    target,
    true,
    shallowReadonlyHandlers,
    shallowReadonlyCollectionHandlers,
    shallowReadonlyMap
  );
}
function createReactiveObject(target, isReadonly2, baseHandlers, collectionHandlers, proxyMap) {
  if (!isObject(target)) {
    if (!!(process.env.NODE_ENV !== "production")) {
      warn(
        `value cannot be made ${isReadonly2 ? "readonly" : "reactive"}: ${String(
          target
        )}`
      );
    }
    return target;
  }
  if (target["__v_raw"] && !(isReadonly2 && target["__v_isReactive"])) {
    return target;
  }
  const targetType = getTargetType(target);
  if (targetType === 0) {
    return target;
  }
  const existingProxy = proxyMap.get(target);
  if (existingProxy) {
    return existingProxy;
  }
  const proxy = new Proxy(
    target,
    targetType === 2 ? collectionHandlers : baseHandlers
  );
  proxyMap.set(target, proxy);
  return proxy;
}
function isReactive(value) {
  if (isReadonly(value)) {
    return isReactive(value["__v_raw"]);
  }
  return !!(value && value["__v_isReactive"]);
}
function isReadonly(value) {
  return !!(value && value["__v_isReadonly"]);
}
function isShallow(value) {
  return !!(value && value["__v_isShallow"]);
}
function isProxy(value) {
  return value ? !!value["__v_raw"] : false;
}
function toRaw(observed) {
  const raw = observed && observed["__v_raw"];
  return raw ? toRaw(raw) : observed;
}
function markRaw(value) {
  if (!hasOwn(value, "__v_skip") && Object.isExtensible(value)) {
    def(value, "__v_skip", true);
  }
  return value;
}
const toReactive = (value) => isObject(value) ? reactive(value) : value;
const toReadonly = (value) => isObject(value) ? readonly(value) : value;
function isRef(r2) {
  return r2 ? r2["__v_isRef"] === true : false;
}
function unref(ref2) {
  return isRef(ref2) ? ref2.value : ref2;
}
const shallowUnwrapHandlers = {
  get: (target, key, receiver) => key === "__v_raw" ? target : unref(Reflect.get(target, key, receiver)),
  set: (target, key, value, receiver) => {
    const oldValue = target[key];
    if (isRef(oldValue) && !isRef(value)) {
      oldValue.value = value;
      return true;
    } else {
      return Reflect.set(target, key, value, receiver);
    }
  }
};
function proxyRefs(objectWithRefs) {
  return isReactive(objectWithRefs) ? objectWithRefs : new Proxy(objectWithRefs, shallowUnwrapHandlers);
}
class ComputedRefImpl {
  constructor(fn, setter, isSSR) {
    this.fn = fn;
    this.setter = setter;
    this._value = void 0;
    this.dep = new Dep(this);
    this.__v_isRef = true;
    this.deps = void 0;
    this.depsTail = void 0;
    this.flags = 16;
    this.globalVersion = globalVersion - 1;
    this.next = void 0;
    this.effect = this;
    this["__v_isReadonly"] = !setter;
    this.isSSR = isSSR;
  }
  /**
   * @internal
   */
  notify() {
    this.flags |= 16;
    if (!(this.flags & 8) && // avoid infinite self recursion
    activeSub !== this) {
      batch(this, true);
      return true;
    } else if (!!(process.env.NODE_ENV !== "production")) ;
  }
  get value() {
    const link = !!(process.env.NODE_ENV !== "production") ? this.dep.track({
      target: this,
      type: "get",
      key: "value"
    }) : this.dep.track();
    refreshComputed(this);
    if (link) {
      link.version = this.dep.version;
    }
    return this._value;
  }
  set value(newValue) {
    if (this.setter) {
      this.setter(newValue);
    } else if (!!(process.env.NODE_ENV !== "production")) {
      warn("Write operation failed: computed value is readonly");
    }
  }
}
function computed$1(getterOrOptions, debugOptions, isSSR = false) {
  let getter;
  let setter;
  if (isFunction(getterOrOptions)) {
    getter = getterOrOptions;
  } else {
    getter = getterOrOptions.get;
    setter = getterOrOptions.set;
  }
  const cRef = new ComputedRefImpl(getter, setter, isSSR);
  if (!!(process.env.NODE_ENV !== "production") && debugOptions) ;
  return cRef;
}
const INITIAL_WATCHER_VALUE = {};
const cleanupMap = /* @__PURE__ */ new WeakMap();
let activeWatcher = void 0;
function onWatcherCleanup(cleanupFn, failSilently = false, owner = activeWatcher) {
  if (owner) {
    let cleanups = cleanupMap.get(owner);
    if (!cleanups) cleanupMap.set(owner, cleanups = []);
    cleanups.push(cleanupFn);
  } else if (!!(process.env.NODE_ENV !== "production") && !failSilently) {
    warn(
      `onWatcherCleanup() was called when there was no active watcher to associate with.`
    );
  }
}
function watch$1(source, cb, options = EMPTY_OBJ) {
  const { immediate, deep, once, scheduler, augmentJob, call } = options;
  const warnInvalidSource = (s2) => {
    (options.onWarn || warn)(
      `Invalid watch source: `,
      s2,
      `A watch source can only be a getter/effect function, a ref, a reactive object, or an array of these types.`
    );
  };
  const reactiveGetter = (source2) => {
    if (deep) return source2;
    if (isShallow(source2) || deep === false || deep === 0)
      return traverse(source2, 1);
    return traverse(source2);
  };
  let effect;
  let getter;
  let cleanup;
  let boundCleanup;
  let forceTrigger = false;
  let isMultiSource = false;
  if (isRef(source)) {
    getter = () => source.value;
    forceTrigger = isShallow(source);
  } else if (isReactive(source)) {
    getter = () => reactiveGetter(source);
    forceTrigger = true;
  } else if (isArray(source)) {
    isMultiSource = true;
    forceTrigger = source.some((s2) => isReactive(s2) || isShallow(s2));
    getter = () => source.map((s2) => {
      if (isRef(s2)) {
        return s2.value;
      } else if (isReactive(s2)) {
        return reactiveGetter(s2);
      } else if (isFunction(s2)) {
        return call ? call(s2, 2) : s2();
      } else {
        !!(process.env.NODE_ENV !== "production") && warnInvalidSource(s2);
      }
    });
  } else if (isFunction(source)) {
    if (cb) {
      getter = call ? () => call(source, 2) : source;
    } else {
      getter = () => {
        if (cleanup) {
          pauseTracking();
          try {
            cleanup();
          } finally {
            resetTracking();
          }
        }
        const currentEffect = activeWatcher;
        activeWatcher = effect;
        try {
          return call ? call(source, 3, [boundCleanup]) : source(boundCleanup);
        } finally {
          activeWatcher = currentEffect;
        }
      };
    }
  } else {
    getter = NOOP;
    !!(process.env.NODE_ENV !== "production") && warnInvalidSource(source);
  }
  if (cb && deep) {
    const baseGetter = getter;
    const depth = deep === true ? Infinity : deep;
    getter = () => traverse(baseGetter(), depth);
  }
  const scope = getCurrentScope();
  const watchHandle = () => {
    effect.stop();
    if (scope && scope.active) {
      remove(scope.effects, effect);
    }
  };
  if (once && cb) {
    const _cb = cb;
    cb = (...args) => {
      _cb(...args);
      watchHandle();
    };
  }
  let oldValue = isMultiSource ? new Array(source.length).fill(INITIAL_WATCHER_VALUE) : INITIAL_WATCHER_VALUE;
  const job = (immediateFirstRun) => {
    if (!(effect.flags & 1) || !effect.dirty && !immediateFirstRun) {
      return;
    }
    if (cb) {
      const newValue = effect.run();
      if (deep || forceTrigger || (isMultiSource ? newValue.some((v, i2) => hasChanged(v, oldValue[i2])) : hasChanged(newValue, oldValue))) {
        if (cleanup) {
          cleanup();
        }
        const currentWatcher = activeWatcher;
        activeWatcher = effect;
        try {
          const args = [
            newValue,
            // pass undefined as the old value when it's changed for the first time
            oldValue === INITIAL_WATCHER_VALUE ? void 0 : isMultiSource && oldValue[0] === INITIAL_WATCHER_VALUE ? [] : oldValue,
            boundCleanup
          ];
          oldValue = newValue;
          call ? call(cb, 3, args) : (
            // @ts-expect-error
            cb(...args)
          );
        } finally {
          activeWatcher = currentWatcher;
        }
      }
    } else {
      effect.run();
    }
  };
  if (augmentJob) {
    augmentJob(job);
  }
  effect = new ReactiveEffect(getter);
  effect.scheduler = scheduler ? () => scheduler(job, false) : job;
  boundCleanup = (fn) => onWatcherCleanup(fn, false, effect);
  cleanup = effect.onStop = () => {
    const cleanups = cleanupMap.get(effect);
    if (cleanups) {
      if (call) {
        call(cleanups, 4);
      } else {
        for (const cleanup2 of cleanups) cleanup2();
      }
      cleanupMap.delete(effect);
    }
  };
  if (!!(process.env.NODE_ENV !== "production")) {
    effect.onTrack = options.onTrack;
    effect.onTrigger = options.onTrigger;
  }
  if (cb) {
    if (immediate) {
      job(true);
    } else {
      oldValue = effect.run();
    }
  } else if (scheduler) {
    scheduler(job.bind(null, true), true);
  } else {
    effect.run();
  }
  watchHandle.pause = effect.pause.bind(effect);
  watchHandle.resume = effect.resume.bind(effect);
  watchHandle.stop = watchHandle;
  return watchHandle;
}
function traverse(value, depth = Infinity, seen) {
  if (depth <= 0 || !isObject(value) || value["__v_skip"]) {
    return value;
  }
  seen = seen || /* @__PURE__ */ new Set();
  if (seen.has(value)) {
    return value;
  }
  seen.add(value);
  depth--;
  if (isRef(value)) {
    traverse(value.value, depth, seen);
  } else if (isArray(value)) {
    for (let i2 = 0; i2 < value.length; i2++) {
      traverse(value[i2], depth, seen);
    }
  } else if (isSet(value) || isMap(value)) {
    value.forEach((v) => {
      traverse(v, depth, seen);
    });
  } else if (isPlainObject(value)) {
    for (const key in value) {
      traverse(value[key], depth, seen);
    }
    for (const key of Object.getOwnPropertySymbols(value)) {
      if (Object.prototype.propertyIsEnumerable.call(value, key)) {
        traverse(value[key], depth, seen);
      }
    }
  }
  return value;
}
/**
* @vue/runtime-core v3.5.17
* (c) 2018-present Yuxi (Evan) You and Vue contributors
* @license MIT
**/
const stack = [];
function pushWarningContext(vnode) {
  stack.push(vnode);
}
function popWarningContext() {
  stack.pop();
}
let isWarning = false;
function warn$1(msg, ...args) {
  if (isWarning) return;
  isWarning = true;
  pauseTracking();
  const instance = stack.length ? stack[stack.length - 1].component : null;
  const appWarnHandler = instance && instance.appContext.config.warnHandler;
  const trace = getComponentTrace();
  if (appWarnHandler) {
    callWithErrorHandling(
      appWarnHandler,
      instance,
      11,
      [
        // eslint-disable-next-line no-restricted-syntax
        msg + args.map((a2) => {
          var _a, _b;
          return (_b = (_a = a2.toString) == null ? void 0 : _a.call(a2)) != null ? _b : JSON.stringify(a2);
        }).join(""),
        instance && instance.proxy,
        trace.map(
          ({ vnode }) => `at <${formatComponentName(instance, vnode.type)}>`
        ).join("\n"),
        trace
      ]
    );
  } else {
    const warnArgs = [`[Vue warn]: ${msg}`, ...args];
    if (trace.length && // avoid spamming console during tests
    true) {
      warnArgs.push(`
`, ...formatTrace(trace));
    }
    console.warn(...warnArgs);
  }
  resetTracking();
  isWarning = false;
}
function getComponentTrace() {
  let currentVNode = stack[stack.length - 1];
  if (!currentVNode) {
    return [];
  }
  const normalizedStack = [];
  while (currentVNode) {
    const last = normalizedStack[0];
    if (last && last.vnode === currentVNode) {
      last.recurseCount++;
    } else {
      normalizedStack.push({
        vnode: currentVNode,
        recurseCount: 0
      });
    }
    const parentInstance = currentVNode.component && currentVNode.component.parent;
    currentVNode = parentInstance && parentInstance.vnode;
  }
  return normalizedStack;
}
function formatTrace(trace) {
  const logs = [];
  trace.forEach((entry, i2) => {
    logs.push(...i2 === 0 ? [] : [`
`], ...formatTraceEntry(entry));
  });
  return logs;
}
function formatTraceEntry({ vnode, recurseCount }) {
  const postfix = recurseCount > 0 ? `... (${recurseCount} recursive calls)` : ``;
  const isRoot = vnode.component ? vnode.component.parent == null : false;
  const open = ` at <${formatComponentName(
    vnode.component,
    vnode.type,
    isRoot
  )}`;
  const close = `>` + postfix;
  return vnode.props ? [open, ...formatProps(vnode.props), close] : [open + close];
}
function formatProps(props) {
  const res = [];
  const keys = Object.keys(props);
  keys.slice(0, 3).forEach((key) => {
    res.push(...formatProp(key, props[key]));
  });
  if (keys.length > 3) {
    res.push(` ...`);
  }
  return res;
}
function formatProp(key, value, raw) {
  if (isString(value)) {
    value = JSON.stringify(value);
    return raw ? value : [`${key}=${value}`];
  } else if (typeof value === "number" || typeof value === "boolean" || value == null) {
    return raw ? value : [`${key}=${value}`];
  } else if (isRef(value)) {
    value = formatProp(key, toRaw(value.value), true);
    return raw ? value : [`${key}=Ref<`, value, `>`];
  } else if (isFunction(value)) {
    return [`${key}=fn${value.name ? `<${value.name}>` : ``}`];
  } else {
    value = toRaw(value);
    return raw ? value : [`${key}=`, value];
  }
}
const ErrorTypeStrings$1 = {
  ["sp"]: "serverPrefetch hook",
  ["bc"]: "beforeCreate hook",
  ["c"]: "created hook",
  ["bm"]: "beforeMount hook",
  ["m"]: "mounted hook",
  ["bu"]: "beforeUpdate hook",
  ["u"]: "updated",
  ["bum"]: "beforeUnmount hook",
  ["um"]: "unmounted hook",
  ["a"]: "activated hook",
  ["da"]: "deactivated hook",
  ["ec"]: "errorCaptured hook",
  ["rtc"]: "renderTracked hook",
  ["rtg"]: "renderTriggered hook",
  [0]: "setup function",
  [1]: "render function",
  [2]: "watcher getter",
  [3]: "watcher callback",
  [4]: "watcher cleanup function",
  [5]: "native event handler",
  [6]: "component event handler",
  [7]: "vnode hook",
  [8]: "directive hook",
  [9]: "transition hook",
  [10]: "app errorHandler",
  [11]: "app warnHandler",
  [12]: "ref function",
  [13]: "async component loader",
  [14]: "scheduler flush",
  [15]: "component update",
  [16]: "app unmount cleanup function"
};
function callWithErrorHandling(fn, instance, type, args) {
  try {
    return args ? fn(...args) : fn();
  } catch (err) {
    handleError(err, instance, type);
  }
}
function callWithAsyncErrorHandling(fn, instance, type, args) {
  if (isFunction(fn)) {
    const res = callWithErrorHandling(fn, instance, type, args);
    if (res && isPromise(res)) {
      res.catch((err) => {
        handleError(err, instance, type);
      });
    }
    return res;
  }
  if (isArray(fn)) {
    const values = [];
    for (let i2 = 0; i2 < fn.length; i2++) {
      values.push(callWithAsyncErrorHandling(fn[i2], instance, type, args));
    }
    return values;
  } else if (!!(process.env.NODE_ENV !== "production")) {
    warn$1(
      `Invalid value type passed to callWithAsyncErrorHandling(): ${typeof fn}`
    );
  }
}
function handleError(err, instance, type, throwInDev = true) {
  const contextVNode = instance ? instance.vnode : null;
  const { errorHandler, throwUnhandledErrorInProduction } = instance && instance.appContext.config || EMPTY_OBJ;
  if (instance) {
    let cur = instance.parent;
    const exposedInstance = instance.proxy;
    const errorInfo = !!(process.env.NODE_ENV !== "production") ? ErrorTypeStrings$1[type] : `https://vuejs.org/error-reference/#runtime-${type}`;
    while (cur) {
      const errorCapturedHooks = cur.ec;
      if (errorCapturedHooks) {
        for (let i2 = 0; i2 < errorCapturedHooks.length; i2++) {
          if (errorCapturedHooks[i2](err, exposedInstance, errorInfo) === false) {
            return;
          }
        }
      }
      cur = cur.parent;
    }
    if (errorHandler) {
      pauseTracking();
      callWithErrorHandling(errorHandler, null, 10, [
        err,
        exposedInstance,
        errorInfo
      ]);
      resetTracking();
      return;
    }
  }
  logError(err, type, contextVNode, throwInDev, throwUnhandledErrorInProduction);
}
function logError(err, type, contextVNode, throwInDev = true, throwInProd = false) {
  if (!!(process.env.NODE_ENV !== "production")) {
    const info = ErrorTypeStrings$1[type];
    if (contextVNode) {
      pushWarningContext(contextVNode);
    }
    warn$1(`Unhandled error${info ? ` during execution of ${info}` : ``}`);
    if (contextVNode) {
      popWarningContext();
    }
    if (throwInDev) {
      throw err;
    } else {
      console.error(err);
    }
  } else if (throwInProd) {
    throw err;
  } else {
    console.error(err);
  }
}
const queue = [];
let flushIndex = -1;
const pendingPostFlushCbs = [];
let activePostFlushCbs = null;
let postFlushIndex = 0;
const resolvedPromise = /* @__PURE__ */ Promise.resolve();
let currentFlushPromise = null;
const RECURSION_LIMIT = 100;
function nextTick(fn) {
  const p = currentFlushPromise || resolvedPromise;
  return fn ? p.then(this ? fn.bind(this) : fn) : p;
}
function findInsertionIndex(id) {
  let start = flushIndex + 1;
  let end = queue.length;
  while (start < end) {
    const middle = start + end >>> 1;
    const middleJob = queue[middle];
    const middleJobId = getId(middleJob);
    if (middleJobId < id || middleJobId === id && middleJob.flags & 2) {
      start = middle + 1;
    } else {
      end = middle;
    }
  }
  return start;
}
function queueJob(job) {
  if (!(job.flags & 1)) {
    const jobId = getId(job);
    const lastJob = queue[queue.length - 1];
    if (!lastJob || // fast path when the job id is larger than the tail
    !(job.flags & 2) && jobId >= getId(lastJob)) {
      queue.push(job);
    } else {
      queue.splice(findInsertionIndex(jobId), 0, job);
    }
    job.flags |= 1;
    queueFlush();
  }
}
function queueFlush() {
  if (!currentFlushPromise) {
    currentFlushPromise = resolvedPromise.then(flushJobs);
  }
}
function queuePostFlushCb(cb) {
  if (!isArray(cb)) {
    if (activePostFlushCbs && cb.id === -1) {
      activePostFlushCbs.splice(postFlushIndex + 1, 0, cb);
    } else if (!(cb.flags & 1)) {
      pendingPostFlushCbs.push(cb);
      cb.flags |= 1;
    }
  } else {
    pendingPostFlushCbs.push(...cb);
  }
  queueFlush();
}
function flushPreFlushCbs(instance, seen, i2 = flushIndex + 1) {
  if (!!(process.env.NODE_ENV !== "production")) {
    seen = seen || /* @__PURE__ */ new Map();
  }
  for (; i2 < queue.length; i2++) {
    const cb = queue[i2];
    if (cb && cb.flags & 2) {
      if (instance && cb.id !== instance.uid) {
        continue;
      }
      if (!!(process.env.NODE_ENV !== "production") && checkRecursiveUpdates(seen, cb)) {
        continue;
      }
      queue.splice(i2, 1);
      i2--;
      if (cb.flags & 4) {
        cb.flags &= -2;
      }
      cb();
      if (!(cb.flags & 4)) {
        cb.flags &= -2;
      }
    }
  }
}
function flushPostFlushCbs(seen) {
  if (pendingPostFlushCbs.length) {
    const deduped = [...new Set(pendingPostFlushCbs)].sort(
      (a2, b) => getId(a2) - getId(b)
    );
    pendingPostFlushCbs.length = 0;
    if (activePostFlushCbs) {
      activePostFlushCbs.push(...deduped);
      return;
    }
    activePostFlushCbs = deduped;
    if (!!(process.env.NODE_ENV !== "production")) {
      seen = seen || /* @__PURE__ */ new Map();
    }
    for (postFlushIndex = 0; postFlushIndex < activePostFlushCbs.length; postFlushIndex++) {
      const cb = activePostFlushCbs[postFlushIndex];
      if (!!(process.env.NODE_ENV !== "production") && checkRecursiveUpdates(seen, cb)) {
        continue;
      }
      if (cb.flags & 4) {
        cb.flags &= -2;
      }
      if (!(cb.flags & 8)) cb();
      cb.flags &= -2;
    }
    activePostFlushCbs = null;
    postFlushIndex = 0;
  }
}
const getId = (job) => job.id == null ? job.flags & 2 ? -1 : Infinity : job.id;
function flushJobs(seen) {
  if (!!(process.env.NODE_ENV !== "production")) {
    seen = seen || /* @__PURE__ */ new Map();
  }
  const check = !!(process.env.NODE_ENV !== "production") ? (job) => checkRecursiveUpdates(seen, job) : NOOP;
  try {
    for (flushIndex = 0; flushIndex < queue.length; flushIndex++) {
      const job = queue[flushIndex];
      if (job && !(job.flags & 8)) {
        if (!!(process.env.NODE_ENV !== "production") && check(job)) {
          continue;
        }
        if (job.flags & 4) {
          job.flags &= ~1;
        }
        callWithErrorHandling(
          job,
          job.i,
          job.i ? 15 : 14
        );
        if (!(job.flags & 4)) {
          job.flags &= ~1;
        }
      }
    }
  } finally {
    for (; flushIndex < queue.length; flushIndex++) {
      const job = queue[flushIndex];
      if (job) {
        job.flags &= -2;
      }
    }
    flushIndex = -1;
    queue.length = 0;
    flushPostFlushCbs(seen);
    currentFlushPromise = null;
    if (queue.length || pendingPostFlushCbs.length) {
      flushJobs(seen);
    }
  }
}
function checkRecursiveUpdates(seen, fn) {
  const count = seen.get(fn) || 0;
  if (count > RECURSION_LIMIT) {
    const instance = fn.i;
    const componentName = instance && getComponentName(instance.type);
    handleError(
      `Maximum recursive updates exceeded${componentName ? ` in component <${componentName}>` : ``}. This means you have a reactive effect that is mutating its own dependencies and thus recursively triggering itself. Possible sources include component template, render function, updated hook or watcher source function.`,
      null,
      10
    );
    return true;
  }
  seen.set(fn, count + 1);
  return false;
}
let isHmrUpdating = false;
const hmrDirtyComponents = /* @__PURE__ */ new Map();
if (!!(process.env.NODE_ENV !== "production")) {
  getGlobalThis().__VUE_HMR_RUNTIME__ = {
    createRecord: tryWrap(createRecord),
    rerender: tryWrap(rerender),
    reload: tryWrap(reload)
  };
}
const map = /* @__PURE__ */ new Map();
function registerHMR(instance) {
  const id = instance.type.__hmrId;
  let record = map.get(id);
  if (!record) {
    createRecord(id, instance.type);
    record = map.get(id);
  }
  record.instances.add(instance);
}
function unregisterHMR(instance) {
  map.get(instance.type.__hmrId).instances.delete(instance);
}
function createRecord(id, initialDef) {
  if (map.has(id)) {
    return false;
  }
  map.set(id, {
    initialDef: normalizeClassComponent(initialDef),
    instances: /* @__PURE__ */ new Set()
  });
  return true;
}
function normalizeClassComponent(component) {
  return isClassComponent(component) ? component.__vccOpts : component;
}
function rerender(id, newRender) {
  const record = map.get(id);
  if (!record) {
    return;
  }
  record.initialDef.render = newRender;
  [...record.instances].forEach((instance) => {
    if (newRender) {
      instance.render = newRender;
      normalizeClassComponent(instance.type).render = newRender;
    }
    instance.renderCache = [];
    isHmrUpdating = true;
    instance.update();
    isHmrUpdating = false;
  });
}
function reload(id, newComp) {
  const record = map.get(id);
  if (!record) return;
  newComp = normalizeClassComponent(newComp);
  updateComponentDef(record.initialDef, newComp);
  const instances = [...record.instances];
  for (let i2 = 0; i2 < instances.length; i2++) {
    const instance = instances[i2];
    const oldComp = normalizeClassComponent(instance.type);
    let dirtyInstances = hmrDirtyComponents.get(oldComp);
    if (!dirtyInstances) {
      if (oldComp !== record.initialDef) {
        updateComponentDef(oldComp, newComp);
      }
      hmrDirtyComponents.set(oldComp, dirtyInstances = /* @__PURE__ */ new Set());
    }
    dirtyInstances.add(instance);
    instance.appContext.propsCache.delete(instance.type);
    instance.appContext.emitsCache.delete(instance.type);
    instance.appContext.optionsCache.delete(instance.type);
    if (instance.ceReload) {
      dirtyInstances.add(instance);
      instance.ceReload(newComp.styles);
      dirtyInstances.delete(instance);
    } else if (instance.parent) {
      queueJob(() => {
        isHmrUpdating = true;
        instance.parent.update();
        isHmrUpdating = false;
        dirtyInstances.delete(instance);
      });
    } else if (instance.appContext.reload) {
      instance.appContext.reload();
    } else if (typeof window !== "undefined") {
      window.location.reload();
    } else {
      console.warn(
        "[HMR] Root or manually mounted instance modified. Full reload required."
      );
    }
    if (instance.root.ce && instance !== instance.root) {
      instance.root.ce._removeChildStyle(oldComp);
    }
  }
  queuePostFlushCb(() => {
    hmrDirtyComponents.clear();
  });
}
function updateComponentDef(oldComp, newComp) {
  extend(oldComp, newComp);
  for (const key in oldComp) {
    if (key !== "__file" && !(key in newComp)) {
      delete oldComp[key];
    }
  }
}
function tryWrap(fn) {
  return (id, arg) => {
    try {
      return fn(id, arg);
    } catch (e2) {
      console.error(e2);
      console.warn(
        `[HMR] Something went wrong during Vue component hot-reload. Full reload required.`
      );
    }
  };
}
let devtools$1;
let buffer = [];
let devtoolsNotInstalled = false;
function emit$1(event, ...args) {
  if (devtools$1) {
    devtools$1.emit(event, ...args);
  } else if (!devtoolsNotInstalled) {
    buffer.push({ event, args });
  }
}
function setDevtoolsHook$1(hook, target) {
  var _a, _b;
  devtools$1 = hook;
  if (devtools$1) {
    devtools$1.enabled = true;
    buffer.forEach(({ event, args }) => devtools$1.emit(event, ...args));
    buffer = [];
  } else if (
    // handle late devtools injection - only do this if we are in an actual
    // browser environment to avoid the timer handle stalling test runner exit
    // (#4815)
    typeof window !== "undefined" && // some envs mock window but not fully
    window.HTMLElement && // also exclude jsdom
    // eslint-disable-next-line no-restricted-syntax
    !((_b = (_a = window.navigator) == null ? void 0 : _a.userAgent) == null ? void 0 : _b.includes("jsdom"))
  ) {
    const replay = target.__VUE_DEVTOOLS_HOOK_REPLAY__ = target.__VUE_DEVTOOLS_HOOK_REPLAY__ || [];
    replay.push((newHook) => {
      setDevtoolsHook$1(newHook, target);
    });
    setTimeout(() => {
      if (!devtools$1) {
        target.__VUE_DEVTOOLS_HOOK_REPLAY__ = null;
        devtoolsNotInstalled = true;
        buffer = [];
      }
    }, 3e3);
  } else {
    devtoolsNotInstalled = true;
    buffer = [];
  }
}
function devtoolsInitApp(app, version2) {
  emit$1("app:init", app, version2, {
    Fragment,
    Text,
    Comment,
    Static
  });
}
function devtoolsUnmountApp(app) {
  emit$1("app:unmount", app);
}
const devtoolsComponentAdded = /* @__PURE__ */ createDevtoolsComponentHook(
  "component:added"
  /* COMPONENT_ADDED */
);
const devtoolsComponentUpdated = /* @__PURE__ */ createDevtoolsComponentHook(
  "component:updated"
  /* COMPONENT_UPDATED */
);
const _devtoolsComponentRemoved = /* @__PURE__ */ createDevtoolsComponentHook(
  "component:removed"
  /* COMPONENT_REMOVED */
);
const devtoolsComponentRemoved = (component) => {
  if (devtools$1 && typeof devtools$1.cleanupBuffer === "function" && // remove the component if it wasn't buffered
  !devtools$1.cleanupBuffer(component)) {
    _devtoolsComponentRemoved(component);
  }
};
/*! #__NO_SIDE_EFFECTS__ */
// @__NO_SIDE_EFFECTS__
function createDevtoolsComponentHook(hook) {
  return (component) => {
    emit$1(
      hook,
      component.appContext.app,
      component.uid,
      component.parent ? component.parent.uid : void 0,
      component
    );
  };
}
const devtoolsPerfStart = /* @__PURE__ */ createDevtoolsPerformanceHook(
  "perf:start"
  /* PERFORMANCE_START */
);
const devtoolsPerfEnd = /* @__PURE__ */ createDevtoolsPerformanceHook(
  "perf:end"
  /* PERFORMANCE_END */
);
function createDevtoolsPerformanceHook(hook) {
  return (component, type, time) => {
    emit$1(hook, component.appContext.app, component.uid, component, type, time);
  };
}
function devtoolsComponentEmit(component, event, params) {
  emit$1(
    "component:emit",
    component.appContext.app,
    component,
    event,
    params
  );
}
let currentRenderingInstance = null;
let currentScopeId = null;
function setCurrentRenderingInstance(instance) {
  const prev = currentRenderingInstance;
  currentRenderingInstance = instance;
  currentScopeId = instance && instance.type.__scopeId || null;
  return prev;
}
function withCtx(fn, ctx = currentRenderingInstance, isNonScopedSlot) {
  if (!ctx) return fn;
  if (fn._n) {
    return fn;
  }
  const renderFnWithContext = (...args) => {
    if (renderFnWithContext._d) {
      setBlockTracking(-1);
    }
    const prevInstance = setCurrentRenderingInstance(ctx);
    let res;
    try {
      res = fn(...args);
    } finally {
      setCurrentRenderingInstance(prevInstance);
      if (renderFnWithContext._d) {
        setBlockTracking(1);
      }
    }
    if (!!(process.env.NODE_ENV !== "production") || false) {
      devtoolsComponentUpdated(ctx);
    }
    return res;
  };
  renderFnWithContext._n = true;
  renderFnWithContext._c = true;
  renderFnWithContext._d = true;
  return renderFnWithContext;
}
function validateDirectiveName(name) {
  if (isBuiltInDirective(name)) {
    warn$1("Do not use built-in directive ids as custom directive id: " + name);
  }
}
function invokeDirectiveHook(vnode, prevVNode, instance, name) {
  const bindings = vnode.dirs;
  const oldBindings = prevVNode && prevVNode.dirs;
  for (let i2 = 0; i2 < bindings.length; i2++) {
    const binding = bindings[i2];
    if (oldBindings) {
      binding.oldValue = oldBindings[i2].value;
    }
    let hook = binding.dir[name];
    if (hook) {
      pauseTracking();
      callWithAsyncErrorHandling(hook, instance, 8, [
        vnode.el,
        binding,
        vnode,
        prevVNode
      ]);
      resetTracking();
    }
  }
}
const TeleportEndKey = Symbol("_vte");
const isTeleport = (type) => type.__isTeleport;
function setTransitionHooks(vnode, hooks) {
  if (vnode.shapeFlag & 6 && vnode.component) {
    vnode.transition = hooks;
    setTransitionHooks(vnode.component.subTree, hooks);
  } else if (vnode.shapeFlag & 128) {
    vnode.ssContent.transition = hooks.clone(vnode.ssContent);
    vnode.ssFallback.transition = hooks.clone(vnode.ssFallback);
  } else {
    vnode.transition = hooks;
  }
}
function markAsyncBoundary(instance) {
  instance.ids = [instance.ids[0] + instance.ids[2]++ + "-", 0, 0];
}
const knownTemplateRefs = /* @__PURE__ */ new WeakSet();
function setRef(rawRef, oldRawRef, parentSuspense, vnode, isUnmount = false) {
  if (isArray(rawRef)) {
    rawRef.forEach(
      (r2, i2) => setRef(
        r2,
        oldRawRef && (isArray(oldRawRef) ? oldRawRef[i2] : oldRawRef),
        parentSuspense,
        vnode,
        isUnmount
      )
    );
    return;
  }
  if (isAsyncWrapper(vnode) && !isUnmount) {
    if (vnode.shapeFlag & 512 && vnode.type.__asyncResolved && vnode.component.subTree.component) {
      setRef(rawRef, oldRawRef, parentSuspense, vnode.component.subTree);
    }
    return;
  }
  const refValue = vnode.shapeFlag & 4 ? getComponentPublicInstance(vnode.component) : vnode.el;
  const value = isUnmount ? null : refValue;
  const { i: owner, r: ref3 } = rawRef;
  if (!!(process.env.NODE_ENV !== "production") && !owner) {
    warn$1(
      `Missing ref owner context. ref cannot be used on hoisted vnodes. A vnode with ref must be created inside the render function.`
    );
    return;
  }
  const oldRef = oldRawRef && oldRawRef.r;
  const refs = owner.refs === EMPTY_OBJ ? owner.refs = {} : owner.refs;
  const setupState = owner.setupState;
  const rawSetupState = toRaw(setupState);
  const canSetSetupRef = setupState === EMPTY_OBJ ? () => false : (key) => {
    if (!!(process.env.NODE_ENV !== "production")) {
      if (hasOwn(rawSetupState, key) && !isRef(rawSetupState[key])) {
        warn$1(
          `Template ref "${key}" used on a non-ref value. It will not work in the production build.`
        );
      }
      if (knownTemplateRefs.has(rawSetupState[key])) {
        return false;
      }
    }
    return hasOwn(rawSetupState, key);
  };
  if (oldRef != null && oldRef !== ref3) {
    if (isString(oldRef)) {
      refs[oldRef] = null;
      if (canSetSetupRef(oldRef)) {
        setupState[oldRef] = null;
      }
    } else if (isRef(oldRef)) {
      oldRef.value = null;
    }
  }
  if (isFunction(ref3)) {
    callWithErrorHandling(ref3, owner, 12, [value, refs]);
  } else {
    const _isString = isString(ref3);
    const _isRef = isRef(ref3);
    if (_isString || _isRef) {
      const doSet = () => {
        if (rawRef.f) {
          const existing = _isString ? canSetSetupRef(ref3) ? setupState[ref3] : refs[ref3] : ref3.value;
          if (isUnmount) {
            isArray(existing) && remove(existing, refValue);
          } else {
            if (!isArray(existing)) {
              if (_isString) {
                refs[ref3] = [refValue];
                if (canSetSetupRef(ref3)) {
                  setupState[ref3] = refs[ref3];
                }
              } else {
                ref3.value = [refValue];
                if (rawRef.k) refs[rawRef.k] = ref3.value;
              }
            } else if (!existing.includes(refValue)) {
              existing.push(refValue);
            }
          }
        } else if (_isString) {
          refs[ref3] = value;
          if (canSetSetupRef(ref3)) {
            setupState[ref3] = value;
          }
        } else if (_isRef) {
          ref3.value = value;
          if (rawRef.k) refs[rawRef.k] = value;
        } else if (!!(process.env.NODE_ENV !== "production")) {
          warn$1("Invalid template ref type:", ref3, `(${typeof ref3})`);
        }
      };
      if (value) {
        doSet.id = -1;
        queuePostRenderEffect(doSet, parentSuspense);
      } else {
        doSet();
      }
    } else if (!!(process.env.NODE_ENV !== "production")) {
      warn$1("Invalid template ref type:", ref3, `(${typeof ref3})`);
    }
  }
}
getGlobalThis().requestIdleCallback || ((cb) => setTimeout(cb, 1));
getGlobalThis().cancelIdleCallback || ((id) => clearTimeout(id));
const isAsyncWrapper = (i2) => !!i2.type.__asyncLoader;
const isKeepAlive = (vnode) => vnode.type.__isKeepAlive;
function onActivated(hook, target) {
  registerKeepAliveHook(hook, "a", target);
}
function onDeactivated(hook, target) {
  registerKeepAliveHook(hook, "da", target);
}
function registerKeepAliveHook(hook, type, target = currentInstance) {
  const wrappedHook = hook.__wdc || (hook.__wdc = () => {
    let current = target;
    while (current) {
      if (current.isDeactivated) {
        return;
      }
      current = current.parent;
    }
    return hook();
  });
  injectHook(type, wrappedHook, target);
  if (target) {
    let current = target.parent;
    while (current && current.parent) {
      if (isKeepAlive(current.parent.vnode)) {
        injectToKeepAliveRoot(wrappedHook, type, target, current);
      }
      current = current.parent;
    }
  }
}
function injectToKeepAliveRoot(hook, type, target, keepAliveRoot) {
  const injected = injectHook(
    type,
    hook,
    keepAliveRoot,
    true
    /* prepend */
  );
  onUnmounted(() => {
    remove(keepAliveRoot[type], injected);
  }, target);
}
function injectHook(type, hook, target = currentInstance, prepend = false) {
  if (target) {
    const hooks = target[type] || (target[type] = []);
    const wrappedHook = hook.__weh || (hook.__weh = (...args) => {
      pauseTracking();
      const reset = setCurrentInstance(target);
      const res = callWithAsyncErrorHandling(hook, target, type, args);
      reset();
      resetTracking();
      return res;
    });
    if (prepend) {
      hooks.unshift(wrappedHook);
    } else {
      hooks.push(wrappedHook);
    }
    return wrappedHook;
  } else if (!!(process.env.NODE_ENV !== "production")) {
    const apiName = toHandlerKey(ErrorTypeStrings$1[type].replace(/ hook$/, ""));
    warn$1(
      `${apiName} is called when there is no active component instance to be associated with. Lifecycle injection APIs can only be used during execution of setup(). If you are using async setup(), make sure to register lifecycle hooks before the first await statement.`
    );
  }
}
const createHook = (lifecycle) => (hook, target = currentInstance) => {
  if (!isInSSRComponentSetup || lifecycle === "sp") {
    injectHook(lifecycle, (...args) => hook(...args), target);
  }
};
const onBeforeMount = createHook("bm");
const onMounted = createHook("m");
const onBeforeUpdate = createHook(
  "bu"
);
const onUpdated = createHook("u");
const onBeforeUnmount = createHook(
  "bum"
);
const onUnmounted = createHook("um");
const onServerPrefetch = createHook(
  "sp"
);
const onRenderTriggered = createHook("rtg");
const onRenderTracked = createHook("rtc");
function onErrorCaptured(hook, target = currentInstance) {
  injectHook("ec", hook, target);
}
const COMPONENTS = "components";
function resolveComponent(name, maybeSelfReference) {
  return resolveAsset(COMPONENTS, name, true, maybeSelfReference) || name;
}
const NULL_DYNAMIC_COMPONENT = Symbol.for("v-ndc");
function resolveDynamicComponent(component) {
  if (isString(component)) {
    return resolveAsset(COMPONENTS, component, false) || component;
  } else {
    return component || NULL_DYNAMIC_COMPONENT;
  }
}
function resolveAsset(type, name, warnMissing = true, maybeSelfReference = false) {
  const instance = currentRenderingInstance || currentInstance;
  if (instance) {
    const Component = instance.type;
    {
      const selfName = getComponentName(
        Component,
        false
      );
      if (selfName && (selfName === name || selfName === camelize(name) || selfName === capitalize(camelize(name)))) {
        return Component;
      }
    }
    const res = (
      // local registration
      // check instance[type] first which is resolved for options API
      resolve(instance[type] || Component[type], name) || // global registration
      resolve(instance.appContext[type], name)
    );
    if (!res && maybeSelfReference) {
      return Component;
    }
    if (!!(process.env.NODE_ENV !== "production") && warnMissing && !res) {
      const extra = `
If this is a native custom element, make sure to exclude it from component resolution via compilerOptions.isCustomElement.`;
      warn$1(`Failed to resolve ${type.slice(0, -1)}: ${name}${extra}`);
    }
    return res;
  } else if (!!(process.env.NODE_ENV !== "production")) {
    warn$1(
      `resolve${capitalize(type.slice(0, -1))} can only be used in render() or setup().`
    );
  }
}
function resolve(registry, name) {
  return registry && (registry[name] || registry[camelize(name)] || registry[capitalize(camelize(name))]);
}
function renderList(source, renderItem, cache, index) {
  let ret;
  const cached = cache;
  const sourceIsArray = isArray(source);
  if (sourceIsArray || isString(source)) {
    const sourceIsReactiveArray = sourceIsArray && isReactive(source);
    let needsWrap = false;
    let isReadonlySource = false;
    if (sourceIsReactiveArray) {
      needsWrap = !isShallow(source);
      isReadonlySource = isReadonly(source);
      source = shallowReadArray(source);
    }
    ret = new Array(source.length);
    for (let i2 = 0, l = source.length; i2 < l; i2++) {
      ret[i2] = renderItem(
        needsWrap ? isReadonlySource ? toReadonly(toReactive(source[i2])) : toReactive(source[i2]) : source[i2],
        i2,
        void 0,
        cached
      );
    }
  } else if (typeof source === "number") {
    if (!!(process.env.NODE_ENV !== "production") && !Number.isInteger(source)) {
      warn$1(`The v-for range expect an integer value but got ${source}.`);
    }
    ret = new Array(source);
    for (let i2 = 0; i2 < source; i2++) {
      ret[i2] = renderItem(i2 + 1, i2, void 0, cached);
    }
  } else if (isObject(source)) {
    if (source[Symbol.iterator]) {
      ret = Array.from(
        source,
        (item, i2) => renderItem(item, i2, void 0, cached)
      );
    } else {
      const keys = Object.keys(source);
      ret = new Array(keys.length);
      for (let i2 = 0, l = keys.length; i2 < l; i2++) {
        const key = keys[i2];
        ret[i2] = renderItem(source[key], key, i2, cached);
      }
    }
  } else {
    ret = [];
  }
  return ret;
}
const getPublicInstance = (i2) => {
  if (!i2) return null;
  if (isStatefulComponent(i2)) return getComponentPublicInstance(i2);
  return getPublicInstance(i2.parent);
};
const publicPropertiesMap = (
  // Move PURE marker to new line to workaround compiler discarding it
  // due to type annotation
  /* @__PURE__ */ extend(/* @__PURE__ */ Object.create(null), {
    $: (i2) => i2,
    $el: (i2) => i2.vnode.el,
    $data: (i2) => i2.data,
    $props: (i2) => !!(process.env.NODE_ENV !== "production") ? shallowReadonly(i2.props) : i2.props,
    $attrs: (i2) => !!(process.env.NODE_ENV !== "production") ? shallowReadonly(i2.attrs) : i2.attrs,
    $slots: (i2) => !!(process.env.NODE_ENV !== "production") ? shallowReadonly(i2.slots) : i2.slots,
    $refs: (i2) => !!(process.env.NODE_ENV !== "production") ? shallowReadonly(i2.refs) : i2.refs,
    $parent: (i2) => getPublicInstance(i2.parent),
    $root: (i2) => getPublicInstance(i2.root),
    $host: (i2) => i2.ce,
    $emit: (i2) => i2.emit,
    $options: (i2) => resolveMergedOptions(i2),
    $forceUpdate: (i2) => i2.f || (i2.f = () => {
      queueJob(i2.update);
    }),
    $nextTick: (i2) => i2.n || (i2.n = nextTick.bind(i2.proxy)),
    $watch: (i2) => instanceWatch.bind(i2)
  })
);
const isReservedPrefix = (key) => key === "_" || key === "$";
const hasSetupBinding = (state, key) => state !== EMPTY_OBJ && !state.__isScriptSetup && hasOwn(state, key);
const PublicInstanceProxyHandlers = {
  get({ _: instance }, key) {
    if (key === "__v_skip") {
      return true;
    }
    const { ctx, setupState, data, props, accessCache, type, appContext } = instance;
    if (!!(process.env.NODE_ENV !== "production") && key === "__isVue") {
      return true;
    }
    let normalizedProps;
    if (key[0] !== "$") {
      const n2 = accessCache[key];
      if (n2 !== void 0) {
        switch (n2) {
          case 1:
            return setupState[key];
          case 2:
            return data[key];
          case 4:
            return ctx[key];
          case 3:
            return props[key];
        }
      } else if (hasSetupBinding(setupState, key)) {
        accessCache[key] = 1;
        return setupState[key];
      } else if (data !== EMPTY_OBJ && hasOwn(data, key)) {
        accessCache[key] = 2;
        return data[key];
      } else if (
        // only cache other properties when instance has declared (thus stable)
        // props
        (normalizedProps = instance.propsOptions[0]) && hasOwn(normalizedProps, key)
      ) {
        accessCache[key] = 3;
        return props[key];
      } else if (ctx !== EMPTY_OBJ && hasOwn(ctx, key)) {
        accessCache[key] = 4;
        return ctx[key];
      } else if (shouldCacheAccess) {
        accessCache[key] = 0;
      }
    }
    const publicGetter = publicPropertiesMap[key];
    let cssModule, globalProperties;
    if (publicGetter) {
      if (key === "$attrs") {
        track(instance.attrs, "get", "");
        !!(process.env.NODE_ENV !== "production") && markAttrsAccessed();
      } else if (!!(process.env.NODE_ENV !== "production") && key === "$slots") {
        track(instance, "get", key);
      }
      return publicGetter(instance);
    } else if (
      // css module (injected by vue-loader)
      (cssModule = type.__cssModules) && (cssModule = cssModule[key])
    ) {
      return cssModule;
    } else if (ctx !== EMPTY_OBJ && hasOwn(ctx, key)) {
      accessCache[key] = 4;
      return ctx[key];
    } else if (
      // global properties
      globalProperties = appContext.config.globalProperties, hasOwn(globalProperties, key)
    ) {
      {
        return globalProperties[key];
      }
    } else if (!!(process.env.NODE_ENV !== "production") && currentRenderingInstance && (!isString(key) || // #1091 avoid internal isRef/isVNode checks on component instance leading
    // to infinite warning loop
    key.indexOf("__v") !== 0)) {
      if (data !== EMPTY_OBJ && isReservedPrefix(key[0]) && hasOwn(data, key)) {
        warn$1(
          `Property ${JSON.stringify(
            key
          )} must be accessed via $data because it starts with a reserved character ("$" or "_") and is not proxied on the render context.`
        );
      } else if (instance === currentRenderingInstance) {
        warn$1(
          `Property ${JSON.stringify(key)} was accessed during render but is not defined on instance.`
        );
      }
    }
  },
  set({ _: instance }, key, value) {
    const { data, setupState, ctx } = instance;
    if (hasSetupBinding(setupState, key)) {
      setupState[key] = value;
      return true;
    } else if (!!(process.env.NODE_ENV !== "production") && setupState.__isScriptSetup && hasOwn(setupState, key)) {
      warn$1(`Cannot mutate <script setup> binding "${key}" from Options API.`);
      return false;
    } else if (data !== EMPTY_OBJ && hasOwn(data, key)) {
      data[key] = value;
      return true;
    } else if (hasOwn(instance.props, key)) {
      !!(process.env.NODE_ENV !== "production") && warn$1(`Attempting to mutate prop "${key}". Props are readonly.`);
      return false;
    }
    if (key[0] === "$" && key.slice(1) in instance) {
      !!(process.env.NODE_ENV !== "production") && warn$1(
        `Attempting to mutate public property "${key}". Properties starting with $ are reserved and readonly.`
      );
      return false;
    } else {
      if (!!(process.env.NODE_ENV !== "production") && key in instance.appContext.config.globalProperties) {
        Object.defineProperty(ctx, key, {
          enumerable: true,
          configurable: true,
          value
        });
      } else {
        ctx[key] = value;
      }
    }
    return true;
  },
  has({
    _: { data, setupState, accessCache, ctx, appContext, propsOptions }
  }, key) {
    let normalizedProps;
    return !!accessCache[key] || data !== EMPTY_OBJ && hasOwn(data, key) || hasSetupBinding(setupState, key) || (normalizedProps = propsOptions[0]) && hasOwn(normalizedProps, key) || hasOwn(ctx, key) || hasOwn(publicPropertiesMap, key) || hasOwn(appContext.config.globalProperties, key);
  },
  defineProperty(target, key, descriptor) {
    if (descriptor.get != null) {
      target._.accessCache[key] = 0;
    } else if (hasOwn(descriptor, "value")) {
      this.set(target, key, descriptor.value, null);
    }
    return Reflect.defineProperty(target, key, descriptor);
  }
};
if (!!(process.env.NODE_ENV !== "production") && true) {
  PublicInstanceProxyHandlers.ownKeys = (target) => {
    warn$1(
      `Avoid app logic that relies on enumerating keys on a component instance. The keys will be empty in production mode to avoid performance overhead.`
    );
    return Reflect.ownKeys(target);
  };
}
function createDevRenderContext(instance) {
  const target = {};
  Object.defineProperty(target, `_`, {
    configurable: true,
    enumerable: false,
    get: () => instance
  });
  Object.keys(publicPropertiesMap).forEach((key) => {
    Object.defineProperty(target, key, {
      configurable: true,
      enumerable: false,
      get: () => publicPropertiesMap[key](instance),
      // intercepted by the proxy so no need for implementation,
      // but needed to prevent set errors
      set: NOOP
    });
  });
  return target;
}
function exposePropsOnRenderContext(instance) {
  const {
    ctx,
    propsOptions: [propsOptions]
  } = instance;
  if (propsOptions) {
    Object.keys(propsOptions).forEach((key) => {
      Object.defineProperty(ctx, key, {
        enumerable: true,
        configurable: true,
        get: () => instance.props[key],
        set: NOOP
      });
    });
  }
}
function exposeSetupStateOnRenderContext(instance) {
  const { ctx, setupState } = instance;
  Object.keys(toRaw(setupState)).forEach((key) => {
    if (!setupState.__isScriptSetup) {
      if (isReservedPrefix(key[0])) {
        warn$1(
          `setup() return property ${JSON.stringify(
            key
          )} should not start with "$" or "_" which are reserved prefixes for Vue internals.`
        );
        return;
      }
      Object.defineProperty(ctx, key, {
        enumerable: true,
        configurable: true,
        get: () => setupState[key],
        set: NOOP
      });
    }
  });
}
function normalizePropsOrEmits(props) {
  return isArray(props) ? props.reduce(
    (normalized, p) => (normalized[p] = null, normalized),
    {}
  ) : props;
}
function createDuplicateChecker() {
  const cache = /* @__PURE__ */ Object.create(null);
  return (type, key) => {
    if (cache[key]) {
      warn$1(`${type} property "${key}" is already defined in ${cache[key]}.`);
    } else {
      cache[key] = type;
    }
  };
}
let shouldCacheAccess = true;
function applyOptions(instance) {
  const options = resolveMergedOptions(instance);
  const publicThis = instance.proxy;
  const ctx = instance.ctx;
  shouldCacheAccess = false;
  if (options.beforeCreate) {
    callHook(options.beforeCreate, instance, "bc");
  }
  const {
    // state
    data: dataOptions,
    computed: computedOptions,
    methods,
    watch: watchOptions,
    provide: provideOptions,
    inject: injectOptions,
    // lifecycle
    created,
    beforeMount,
    mounted,
    beforeUpdate,
    updated,
    activated,
    deactivated,
    beforeDestroy,
    beforeUnmount,
    destroyed,
    unmounted,
    render: render2,
    renderTracked,
    renderTriggered,
    errorCaptured,
    serverPrefetch,
    // public API
    expose,
    inheritAttrs,
    // assets
    components,
    directives,
    filters
  } = options;
  const checkDuplicateProperties = !!(process.env.NODE_ENV !== "production") ? createDuplicateChecker() : null;
  if (!!(process.env.NODE_ENV !== "production")) {
    const [propsOptions] = instance.propsOptions;
    if (propsOptions) {
      for (const key in propsOptions) {
        checkDuplicateProperties("Props", key);
      }
    }
  }
  if (injectOptions) {
    resolveInjections(injectOptions, ctx, checkDuplicateProperties);
  }
  if (methods) {
    for (const key in methods) {
      const methodHandler = methods[key];
      if (isFunction(methodHandler)) {
        if (!!(process.env.NODE_ENV !== "production")) {
          Object.defineProperty(ctx, key, {
            value: methodHandler.bind(publicThis),
            configurable: true,
            enumerable: true,
            writable: true
          });
        } else {
          ctx[key] = methodHandler.bind(publicThis);
        }
        if (!!(process.env.NODE_ENV !== "production")) {
          checkDuplicateProperties("Methods", key);
        }
      } else if (!!(process.env.NODE_ENV !== "production")) {
        warn$1(
          `Method "${key}" has type "${typeof methodHandler}" in the component definition. Did you reference the function correctly?`
        );
      }
    }
  }
  if (dataOptions) {
    if (!!(process.env.NODE_ENV !== "production") && !isFunction(dataOptions)) {
      warn$1(
        `The data option must be a function. Plain object usage is no longer supported.`
      );
    }
    const data = dataOptions.call(publicThis, publicThis);
    if (!!(process.env.NODE_ENV !== "production") && isPromise(data)) {
      warn$1(
        `data() returned a Promise - note data() cannot be async; If you intend to perform data fetching before component renders, use async setup() + <Suspense>.`
      );
    }
    if (!isObject(data)) {
      !!(process.env.NODE_ENV !== "production") && warn$1(`data() should return an object.`);
    } else {
      instance.data = reactive(data);
      if (!!(process.env.NODE_ENV !== "production")) {
        for (const key in data) {
          checkDuplicateProperties("Data", key);
          if (!isReservedPrefix(key[0])) {
            Object.defineProperty(ctx, key, {
              configurable: true,
              enumerable: true,
              get: () => data[key],
              set: NOOP
            });
          }
        }
      }
    }
  }
  shouldCacheAccess = true;
  if (computedOptions) {
    for (const key in computedOptions) {
      const opt = computedOptions[key];
      const get = isFunction(opt) ? opt.bind(publicThis, publicThis) : isFunction(opt.get) ? opt.get.bind(publicThis, publicThis) : NOOP;
      if (!!(process.env.NODE_ENV !== "production") && get === NOOP) {
        warn$1(`Computed property "${key}" has no getter.`);
      }
      const set = !isFunction(opt) && isFunction(opt.set) ? opt.set.bind(publicThis) : !!(process.env.NODE_ENV !== "production") ? () => {
        warn$1(
          `Write operation failed: computed property "${key}" is readonly.`
        );
      } : NOOP;
      const c2 = computed({
        get,
        set
      });
      Object.defineProperty(ctx, key, {
        enumerable: true,
        configurable: true,
        get: () => c2.value,
        set: (v) => c2.value = v
      });
      if (!!(process.env.NODE_ENV !== "production")) {
        checkDuplicateProperties("Computed", key);
      }
    }
  }
  if (watchOptions) {
    for (const key in watchOptions) {
      createWatcher(watchOptions[key], ctx, publicThis, key);
    }
  }
  if (provideOptions) {
    const provides = isFunction(provideOptions) ? provideOptions.call(publicThis) : provideOptions;
    Reflect.ownKeys(provides).forEach((key) => {
      provide(key, provides[key]);
    });
  }
  if (created) {
    callHook(created, instance, "c");
  }
  function registerLifecycleHook(register, hook) {
    if (isArray(hook)) {
      hook.forEach((_hook) => register(_hook.bind(publicThis)));
    } else if (hook) {
      register(hook.bind(publicThis));
    }
  }
  registerLifecycleHook(onBeforeMount, beforeMount);
  registerLifecycleHook(onMounted, mounted);
  registerLifecycleHook(onBeforeUpdate, beforeUpdate);
  registerLifecycleHook(onUpdated, updated);
  registerLifecycleHook(onActivated, activated);
  registerLifecycleHook(onDeactivated, deactivated);
  registerLifecycleHook(onErrorCaptured, errorCaptured);
  registerLifecycleHook(onRenderTracked, renderTracked);
  registerLifecycleHook(onRenderTriggered, renderTriggered);
  registerLifecycleHook(onBeforeUnmount, beforeUnmount);
  registerLifecycleHook(onUnmounted, unmounted);
  registerLifecycleHook(onServerPrefetch, serverPrefetch);
  if (isArray(expose)) {
    if (expose.length) {
      const exposed = instance.exposed || (instance.exposed = {});
      expose.forEach((key) => {
        Object.defineProperty(exposed, key, {
          get: () => publicThis[key],
          set: (val) => publicThis[key] = val
        });
      });
    } else if (!instance.exposed) {
      instance.exposed = {};
    }
  }
  if (render2 && instance.render === NOOP) {
    instance.render = render2;
  }
  if (inheritAttrs != null) {
    instance.inheritAttrs = inheritAttrs;
  }
  if (components) instance.components = components;
  if (directives) instance.directives = directives;
  if (serverPrefetch) {
    markAsyncBoundary(instance);
  }
}
function resolveInjections(injectOptions, ctx, checkDuplicateProperties = NOOP) {
  if (isArray(injectOptions)) {
    injectOptions = normalizeInject(injectOptions);
  }
  for (const key in injectOptions) {
    const opt = injectOptions[key];
    let injected;
    if (isObject(opt)) {
      if ("default" in opt) {
        injected = inject(
          opt.from || key,
          opt.default,
          true
        );
      } else {
        injected = inject(opt.from || key);
      }
    } else {
      injected = inject(opt);
    }
    if (isRef(injected)) {
      Object.defineProperty(ctx, key, {
        enumerable: true,
        configurable: true,
        get: () => injected.value,
        set: (v) => injected.value = v
      });
    } else {
      ctx[key] = injected;
    }
    if (!!(process.env.NODE_ENV !== "production")) {
      checkDuplicateProperties("Inject", key);
    }
  }
}
function callHook(hook, instance, type) {
  callWithAsyncErrorHandling(
    isArray(hook) ? hook.map((h2) => h2.bind(instance.proxy)) : hook.bind(instance.proxy),
    instance,
    type
  );
}
function createWatcher(raw, ctx, publicThis, key) {
  let getter = key.includes(".") ? createPathGetter(publicThis, key) : () => publicThis[key];
  if (isString(raw)) {
    const handler = ctx[raw];
    if (isFunction(handler)) {
      {
        watch(getter, handler);
      }
    } else if (!!(process.env.NODE_ENV !== "production")) {
      warn$1(`Invalid watch handler specified by key "${raw}"`, handler);
    }
  } else if (isFunction(raw)) {
    {
      watch(getter, raw.bind(publicThis));
    }
  } else if (isObject(raw)) {
    if (isArray(raw)) {
      raw.forEach((r2) => createWatcher(r2, ctx, publicThis, key));
    } else {
      const handler = isFunction(raw.handler) ? raw.handler.bind(publicThis) : ctx[raw.handler];
      if (isFunction(handler)) {
        watch(getter, handler, raw);
      } else if (!!(process.env.NODE_ENV !== "production")) {
        warn$1(`Invalid watch handler specified by key "${raw.handler}"`, handler);
      }
    }
  } else if (!!(process.env.NODE_ENV !== "production")) {
    warn$1(`Invalid watch option: "${key}"`, raw);
  }
}
function resolveMergedOptions(instance) {
  const base = instance.type;
  const { mixins, extends: extendsOptions } = base;
  const {
    mixins: globalMixins,
    optionsCache: cache,
    config: { optionMergeStrategies }
  } = instance.appContext;
  const cached = cache.get(base);
  let resolved;
  if (cached) {
    resolved = cached;
  } else if (!globalMixins.length && !mixins && !extendsOptions) {
    {
      resolved = base;
    }
  } else {
    resolved = {};
    if (globalMixins.length) {
      globalMixins.forEach(
        (m) => mergeOptions(resolved, m, optionMergeStrategies, true)
      );
    }
    mergeOptions(resolved, base, optionMergeStrategies);
  }
  if (isObject(base)) {
    cache.set(base, resolved);
  }
  return resolved;
}
function mergeOptions(to, from, strats, asMixin = false) {
  const { mixins, extends: extendsOptions } = from;
  if (extendsOptions) {
    mergeOptions(to, extendsOptions, strats, true);
  }
  if (mixins) {
    mixins.forEach(
      (m) => mergeOptions(to, m, strats, true)
    );
  }
  for (const key in from) {
    if (asMixin && key === "expose") {
      !!(process.env.NODE_ENV !== "production") && warn$1(
        `"expose" option is ignored when declared in mixins or extends. It should only be declared in the base component itself.`
      );
    } else {
      const strat = internalOptionMergeStrats[key] || strats && strats[key];
      to[key] = strat ? strat(to[key], from[key]) : from[key];
    }
  }
  return to;
}
const internalOptionMergeStrats = {
  data: mergeDataFn,
  props: mergeEmitsOrPropsOptions,
  emits: mergeEmitsOrPropsOptions,
  // objects
  methods: mergeObjectOptions,
  computed: mergeObjectOptions,
  // lifecycle
  beforeCreate: mergeAsArray,
  created: mergeAsArray,
  beforeMount: mergeAsArray,
  mounted: mergeAsArray,
  beforeUpdate: mergeAsArray,
  updated: mergeAsArray,
  beforeDestroy: mergeAsArray,
  beforeUnmount: mergeAsArray,
  destroyed: mergeAsArray,
  unmounted: mergeAsArray,
  activated: mergeAsArray,
  deactivated: mergeAsArray,
  errorCaptured: mergeAsArray,
  serverPrefetch: mergeAsArray,
  // assets
  components: mergeObjectOptions,
  directives: mergeObjectOptions,
  // watch
  watch: mergeWatchOptions,
  // provide / inject
  provide: mergeDataFn,
  inject: mergeInject
};
function mergeDataFn(to, from) {
  if (!from) {
    return to;
  }
  if (!to) {
    return from;
  }
  return function mergedDataFn() {
    return extend(
      isFunction(to) ? to.call(this, this) : to,
      isFunction(from) ? from.call(this, this) : from
    );
  };
}
function mergeInject(to, from) {
  return mergeObjectOptions(normalizeInject(to), normalizeInject(from));
}
function normalizeInject(raw) {
  if (isArray(raw)) {
    const res = {};
    for (let i2 = 0; i2 < raw.length; i2++) {
      res[raw[i2]] = raw[i2];
    }
    return res;
  }
  return raw;
}
function mergeAsArray(to, from) {
  return to ? [...new Set([].concat(to, from))] : from;
}
function mergeObjectOptions(to, from) {
  return to ? extend(/* @__PURE__ */ Object.create(null), to, from) : from;
}
function mergeEmitsOrPropsOptions(to, from) {
  if (to) {
    if (isArray(to) && isArray(from)) {
      return [.../* @__PURE__ */ new Set([...to, ...from])];
    }
    return extend(
      /* @__PURE__ */ Object.create(null),
      normalizePropsOrEmits(to),
      normalizePropsOrEmits(from != null ? from : {})
    );
  } else {
    return from;
  }
}
function mergeWatchOptions(to, from) {
  if (!to) return from;
  if (!from) return to;
  const merged = extend(/* @__PURE__ */ Object.create(null), to);
  for (const key in from) {
    merged[key] = mergeAsArray(to[key], from[key]);
  }
  return merged;
}
function createAppContext() {
  return {
    app: null,
    config: {
      isNativeTag: NO,
      performance: false,
      globalProperties: {},
      optionMergeStrategies: {},
      errorHandler: void 0,
      warnHandler: void 0,
      compilerOptions: {}
    },
    mixins: [],
    components: {},
    directives: {},
    provides: /* @__PURE__ */ Object.create(null),
    optionsCache: /* @__PURE__ */ new WeakMap(),
    propsCache: /* @__PURE__ */ new WeakMap(),
    emitsCache: /* @__PURE__ */ new WeakMap()
  };
}
let uid$1 = 0;
function createAppAPI(render2, hydrate) {
  return function createApp(rootComponent, rootProps = null) {
    if (!isFunction(rootComponent)) {
      rootComponent = extend({}, rootComponent);
    }
    if (rootProps != null && !isObject(rootProps)) {
      !!(process.env.NODE_ENV !== "production") && warn$1(`root props passed to app.mount() must be an object.`);
      rootProps = null;
    }
    const context = createAppContext();
    const installedPlugins = /* @__PURE__ */ new WeakSet();
    const pluginCleanupFns = [];
    let isMounted = false;
    const app = context.app = {
      _uid: uid$1++,
      _component: rootComponent,
      _props: rootProps,
      _container: null,
      _context: context,
      _instance: null,
      version,
      get config() {
        return context.config;
      },
      set config(v) {
        if (!!(process.env.NODE_ENV !== "production")) {
          warn$1(
            `app.config cannot be replaced. Modify individual options instead.`
          );
        }
      },
      use(plugin, ...options) {
        if (installedPlugins.has(plugin)) {
          !!(process.env.NODE_ENV !== "production") && warn$1(`Plugin has already been applied to target app.`);
        } else if (plugin && isFunction(plugin.install)) {
          installedPlugins.add(plugin);
          plugin.install(app, ...options);
        } else if (isFunction(plugin)) {
          installedPlugins.add(plugin);
          plugin(app, ...options);
        } else if (!!(process.env.NODE_ENV !== "production")) {
          warn$1(
            `A plugin must either be a function or an object with an "install" function.`
          );
        }
        return app;
      },
      mixin(mixin) {
        {
          if (!context.mixins.includes(mixin)) {
            context.mixins.push(mixin);
          } else if (!!(process.env.NODE_ENV !== "production")) {
            warn$1(
              "Mixin has already been applied to target app" + (mixin.name ? `: ${mixin.name}` : "")
            );
          }
        }
        return app;
      },
      component(name, component) {
        if (!!(process.env.NODE_ENV !== "production")) {
          validateComponentName(name, context.config);
        }
        if (!component) {
          return context.components[name];
        }
        if (!!(process.env.NODE_ENV !== "production") && context.components[name]) {
          warn$1(`Component "${name}" has already been registered in target app.`);
        }
        context.components[name] = component;
        return app;
      },
      directive(name, directive) {
        if (!!(process.env.NODE_ENV !== "production")) {
          validateDirectiveName(name);
        }
        if (!directive) {
          return context.directives[name];
        }
        if (!!(process.env.NODE_ENV !== "production") && context.directives[name]) {
          warn$1(`Directive "${name}" has already been registered in target app.`);
        }
        context.directives[name] = directive;
        return app;
      },
      mount(rootContainer, isHydrate, namespace) {
        if (!isMounted) {
          if (!!(process.env.NODE_ENV !== "production") && rootContainer.__vue_app__) {
            warn$1(
              `There is already an app instance mounted on the host container.
 If you want to mount another app on the same host container, you need to unmount the previous app by calling \`app.unmount()\` first.`
            );
          }
          const vnode = app._ceVNode || createVNode(rootComponent, rootProps);
          vnode.appContext = context;
          if (namespace === true) {
            namespace = "svg";
          } else if (namespace === false) {
            namespace = void 0;
          }
          if (!!(process.env.NODE_ENV !== "production")) {
            context.reload = () => {
              const cloned = cloneVNode(vnode);
              cloned.el = null;
              render2(cloned, rootContainer, namespace);
            };
          }
          {
            render2(vnode, rootContainer, namespace);
          }
          isMounted = true;
          app._container = rootContainer;
          rootContainer.__vue_app__ = app;
          if (!!(process.env.NODE_ENV !== "production") || false) {
            app._instance = vnode.component;
            devtoolsInitApp(app, version);
          }
          return getComponentPublicInstance(vnode.component);
        } else if (!!(process.env.NODE_ENV !== "production")) {
          warn$1(
            `App has already been mounted.
If you want to remount the same app, move your app creation logic into a factory function and create fresh app instances for each mount - e.g. \`const createMyApp = () => createApp(App)\``
          );
        }
      },
      onUnmount(cleanupFn) {
        if (!!(process.env.NODE_ENV !== "production") && typeof cleanupFn !== "function") {
          warn$1(
            `Expected function as first argument to app.onUnmount(), but got ${typeof cleanupFn}`
          );
        }
        pluginCleanupFns.push(cleanupFn);
      },
      unmount() {
        if (isMounted) {
          callWithAsyncErrorHandling(
            pluginCleanupFns,
            app._instance,
            16
          );
          render2(null, app._container);
          if (!!(process.env.NODE_ENV !== "production") || false) {
            app._instance = null;
            devtoolsUnmountApp(app);
          }
          delete app._container.__vue_app__;
        } else if (!!(process.env.NODE_ENV !== "production")) {
          warn$1(`Cannot unmount an app that is not mounted.`);
        }
      },
      provide(key, value) {
        if (!!(process.env.NODE_ENV !== "production") && key in context.provides) {
          if (hasOwn(context.provides, key)) {
            warn$1(
              `App already provides property with key "${String(key)}". It will be overwritten with the new value.`
            );
          } else {
            warn$1(
              `App already provides property with key "${String(key)}" inherited from its parent element. It will be overwritten with the new value.`
            );
          }
        }
        context.provides[key] = value;
        return app;
      },
      runWithContext(fn) {
        const lastApp = currentApp;
        currentApp = app;
        try {
          return fn();
        } finally {
          currentApp = lastApp;
        }
      }
    };
    return app;
  };
}
let currentApp = null;
function provide(key, value) {
  if (!currentInstance) {
    if (!!(process.env.NODE_ENV !== "production")) {
      warn$1(`provide() can only be used inside setup().`);
    }
  } else {
    let provides = currentInstance.provides;
    const parentProvides = currentInstance.parent && currentInstance.parent.provides;
    if (parentProvides === provides) {
      provides = currentInstance.provides = Object.create(parentProvides);
    }
    provides[key] = value;
  }
}
function inject(key, defaultValue, treatDefaultAsFactory = false) {
  const instance = currentInstance || currentRenderingInstance;
  if (instance || currentApp) {
    let provides = currentApp ? currentApp._context.provides : instance ? instance.parent == null || instance.ce ? instance.vnode.appContext && instance.vnode.appContext.provides : instance.parent.provides : void 0;
    if (provides && key in provides) {
      return provides[key];
    } else if (arguments.length > 1) {
      return treatDefaultAsFactory && isFunction(defaultValue) ? defaultValue.call(instance && instance.proxy) : defaultValue;
    } else if (!!(process.env.NODE_ENV !== "production")) {
      warn$1(`injection "${String(key)}" not found.`);
    }
  } else if (!!(process.env.NODE_ENV !== "production")) {
    warn$1(`inject() can only be used inside setup() or functional components.`);
  }
}
const internalObjectProto = {};
const createInternalObject = () => Object.create(internalObjectProto);
const isInternalObject = (obj) => Object.getPrototypeOf(obj) === internalObjectProto;
function initProps(instance, rawProps, isStateful, isSSR = false) {
  const props = {};
  const attrs = createInternalObject();
  instance.propsDefaults = /* @__PURE__ */ Object.create(null);
  setFullProps(instance, rawProps, props, attrs);
  for (const key in instance.propsOptions[0]) {
    if (!(key in props)) {
      props[key] = void 0;
    }
  }
  if (!!(process.env.NODE_ENV !== "production")) {
    validateProps(rawProps || {}, props, instance);
  }
  if (isStateful) {
    instance.props = isSSR ? props : shallowReactive(props);
  } else {
    if (!instance.type.props) {
      instance.props = attrs;
    } else {
      instance.props = props;
    }
  }
  instance.attrs = attrs;
}
function isInHmrContext(instance) {
  while (instance) {
    if (instance.type.__hmrId) return true;
    instance = instance.parent;
  }
}
function updateProps(instance, rawProps, rawPrevProps, optimized) {
  const {
    props,
    attrs,
    vnode: { patchFlag }
  } = instance;
  const rawCurrentProps = toRaw(props);
  const [options] = instance.propsOptions;
  let hasAttrsChanged = false;
  if (
    // always force full diff in dev
    // - #1942 if hmr is enabled with sfc component
    // - vite#872 non-sfc component used by sfc component
    !(!!(process.env.NODE_ENV !== "production") && isInHmrContext(instance)) && (optimized || patchFlag > 0) && !(patchFlag & 16)
  ) {
    if (patchFlag & 8) {
      const propsToUpdate = instance.vnode.dynamicProps;
      for (let i2 = 0; i2 < propsToUpdate.length; i2++) {
        let key = propsToUpdate[i2];
        if (isEmitListener(instance.emitsOptions, key)) {
          continue;
        }
        const value = rawProps[key];
        if (options) {
          if (hasOwn(attrs, key)) {
            if (value !== attrs[key]) {
              attrs[key] = value;
              hasAttrsChanged = true;
            }
          } else {
            const camelizedKey = camelize(key);
            props[camelizedKey] = resolvePropValue(
              options,
              rawCurrentProps,
              camelizedKey,
              value,
              instance,
              false
            );
          }
        } else {
          if (value !== attrs[key]) {
            attrs[key] = value;
            hasAttrsChanged = true;
          }
        }
      }
    }
  } else {
    if (setFullProps(instance, rawProps, props, attrs)) {
      hasAttrsChanged = true;
    }
    let kebabKey;
    for (const key in rawCurrentProps) {
      if (!rawProps || // for camelCase
      !hasOwn(rawProps, key) && // it's possible the original props was passed in as kebab-case
      // and converted to camelCase (#955)
      ((kebabKey = hyphenate(key)) === key || !hasOwn(rawProps, kebabKey))) {
        if (options) {
          if (rawPrevProps && // for camelCase
          (rawPrevProps[key] !== void 0 || // for kebab-case
          rawPrevProps[kebabKey] !== void 0)) {
            props[key] = resolvePropValue(
              options,
              rawCurrentProps,
              key,
              void 0,
              instance,
              true
            );
          }
        } else {
          delete props[key];
        }
      }
    }
    if (attrs !== rawCurrentProps) {
      for (const key in attrs) {
        if (!rawProps || !hasOwn(rawProps, key) && true) {
          delete attrs[key];
          hasAttrsChanged = true;
        }
      }
    }
  }
  if (hasAttrsChanged) {
    trigger(instance.attrs, "set", "");
  }
  if (!!(process.env.NODE_ENV !== "production")) {
    validateProps(rawProps || {}, props, instance);
  }
}
function setFullProps(instance, rawProps, props, attrs) {
  const [options, needCastKeys] = instance.propsOptions;
  let hasAttrsChanged = false;
  let rawCastValues;
  if (rawProps) {
    for (let key in rawProps) {
      if (isReservedProp(key)) {
        continue;
      }
      const value = rawProps[key];
      let camelKey;
      if (options && hasOwn(options, camelKey = camelize(key))) {
        if (!needCastKeys || !needCastKeys.includes(camelKey)) {
          props[camelKey] = value;
        } else {
          (rawCastValues || (rawCastValues = {}))[camelKey] = value;
        }
      } else if (!isEmitListener(instance.emitsOptions, key)) {
        if (!(key in attrs) || value !== attrs[key]) {
          attrs[key] = value;
          hasAttrsChanged = true;
        }
      }
    }
  }
  if (needCastKeys) {
    const rawCurrentProps = toRaw(props);
    const castValues = rawCastValues || EMPTY_OBJ;
    for (let i2 = 0; i2 < needCastKeys.length; i2++) {
      const key = needCastKeys[i2];
      props[key] = resolvePropValue(
        options,
        rawCurrentProps,
        key,
        castValues[key],
        instance,
        !hasOwn(castValues, key)
      );
    }
  }
  return hasAttrsChanged;
}
function resolvePropValue(options, props, key, value, instance, isAbsent) {
  const opt = options[key];
  if (opt != null) {
    const hasDefault = hasOwn(opt, "default");
    if (hasDefault && value === void 0) {
      const defaultValue = opt.default;
      if (opt.type !== Function && !opt.skipFactory && isFunction(defaultValue)) {
        const { propsDefaults } = instance;
        if (key in propsDefaults) {
          value = propsDefaults[key];
        } else {
          const reset = setCurrentInstance(instance);
          value = propsDefaults[key] = defaultValue.call(
            null,
            props
          );
          reset();
        }
      } else {
        value = defaultValue;
      }
      if (instance.ce) {
        instance.ce._setProp(key, value);
      }
    }
    if (opt[
      0
      /* shouldCast */
    ]) {
      if (isAbsent && !hasDefault) {
        value = false;
      } else if (opt[
        1
        /* shouldCastTrue */
      ] && (value === "" || value === hyphenate(key))) {
        value = true;
      }
    }
  }
  return value;
}
const mixinPropsCache = /* @__PURE__ */ new WeakMap();
function normalizePropsOptions(comp, appContext, asMixin = false) {
  const cache = asMixin ? mixinPropsCache : appContext.propsCache;
  const cached = cache.get(comp);
  if (cached) {
    return cached;
  }
  const raw = comp.props;
  const normalized = {};
  const needCastKeys = [];
  let hasExtends = false;
  if (!isFunction(comp)) {
    const extendProps = (raw2) => {
      hasExtends = true;
      const [props, keys] = normalizePropsOptions(raw2, appContext, true);
      extend(normalized, props);
      if (keys) needCastKeys.push(...keys);
    };
    if (!asMixin && appContext.mixins.length) {
      appContext.mixins.forEach(extendProps);
    }
    if (comp.extends) {
      extendProps(comp.extends);
    }
    if (comp.mixins) {
      comp.mixins.forEach(extendProps);
    }
  }
  if (!raw && !hasExtends) {
    if (isObject(comp)) {
      cache.set(comp, EMPTY_ARR);
    }
    return EMPTY_ARR;
  }
  if (isArray(raw)) {
    for (let i2 = 0; i2 < raw.length; i2++) {
      if (!!(process.env.NODE_ENV !== "production") && !isString(raw[i2])) {
        warn$1(`props must be strings when using array syntax.`, raw[i2]);
      }
      const normalizedKey = camelize(raw[i2]);
      if (validatePropName(normalizedKey)) {
        normalized[normalizedKey] = EMPTY_OBJ;
      }
    }
  } else if (raw) {
    if (!!(process.env.NODE_ENV !== "production") && !isObject(raw)) {
      warn$1(`invalid props options`, raw);
    }
    for (const key in raw) {
      const normalizedKey = camelize(key);
      if (validatePropName(normalizedKey)) {
        const opt = raw[key];
        const prop = normalized[normalizedKey] = isArray(opt) || isFunction(opt) ? { type: opt } : extend({}, opt);
        const propType = prop.type;
        let shouldCast = false;
        let shouldCastTrue = true;
        if (isArray(propType)) {
          for (let index = 0; index < propType.length; ++index) {
            const type = propType[index];
            const typeName = isFunction(type) && type.name;
            if (typeName === "Boolean") {
              shouldCast = true;
              break;
            } else if (typeName === "String") {
              shouldCastTrue = false;
            }
          }
        } else {
          shouldCast = isFunction(propType) && propType.name === "Boolean";
        }
        prop[
          0
          /* shouldCast */
        ] = shouldCast;
        prop[
          1
          /* shouldCastTrue */
        ] = shouldCastTrue;
        if (shouldCast || hasOwn(prop, "default")) {
          needCastKeys.push(normalizedKey);
        }
      }
    }
  }
  const res = [normalized, needCastKeys];
  if (isObject(comp)) {
    cache.set(comp, res);
  }
  return res;
}
function validatePropName(key) {
  if (key[0] !== "$" && !isReservedProp(key)) {
    return true;
  } else if (!!(process.env.NODE_ENV !== "production")) {
    warn$1(`Invalid prop name: "${key}" is a reserved property.`);
  }
  return false;
}
function getType(ctor) {
  if (ctor === null) {
    return "null";
  }
  if (typeof ctor === "function") {
    return ctor.name || "";
  } else if (typeof ctor === "object") {
    const name = ctor.constructor && ctor.constructor.name;
    return name || "";
  }
  return "";
}
function validateProps(rawProps, props, instance) {
  const resolvedValues = toRaw(props);
  const options = instance.propsOptions[0];
  const camelizePropsKey = Object.keys(rawProps).map((key) => camelize(key));
  for (const key in options) {
    let opt = options[key];
    if (opt == null) continue;
    validateProp(
      key,
      resolvedValues[key],
      opt,
      !!(process.env.NODE_ENV !== "production") ? shallowReadonly(resolvedValues) : resolvedValues,
      !camelizePropsKey.includes(key)
    );
  }
}
function validateProp(name, value, prop, props, isAbsent) {
  const { type, required, validator, skipCheck } = prop;
  if (required && isAbsent) {
    warn$1('Missing required prop: "' + name + '"');
    return;
  }
  if (value == null && !required) {
    return;
  }
  if (type != null && type !== true && !skipCheck) {
    let isValid = false;
    const types = isArray(type) ? type : [type];
    const expectedTypes = [];
    for (let i2 = 0; i2 < types.length && !isValid; i2++) {
      const { valid, expectedType } = assertType(value, types[i2]);
      expectedTypes.push(expectedType || "");
      isValid = valid;
    }
    if (!isValid) {
      warn$1(getInvalidTypeMessage(name, value, expectedTypes));
      return;
    }
  }
  if (validator && !validator(value, props)) {
    warn$1('Invalid prop: custom validator check failed for prop "' + name + '".');
  }
}
const isSimpleType = /* @__PURE__ */ makeMap(
  "String,Number,Boolean,Function,Symbol,BigInt"
);
function assertType(value, type) {
  let valid;
  const expectedType = getType(type);
  if (expectedType === "null") {
    valid = value === null;
  } else if (isSimpleType(expectedType)) {
    const t2 = typeof value;
    valid = t2 === expectedType.toLowerCase();
    if (!valid && t2 === "object") {
      valid = value instanceof type;
    }
  } else if (expectedType === "Object") {
    valid = isObject(value);
  } else if (expectedType === "Array") {
    valid = isArray(value);
  } else {
    valid = value instanceof type;
  }
  return {
    valid,
    expectedType
  };
}
function getInvalidTypeMessage(name, value, expectedTypes) {
  if (expectedTypes.length === 0) {
    return `Prop type [] for prop "${name}" won't match anything. Did you mean to use type Array instead?`;
  }
  let message = `Invalid prop: type check failed for prop "${name}". Expected ${expectedTypes.map(capitalize).join(" | ")}`;
  const expectedType = expectedTypes[0];
  const receivedType = toRawType(value);
  const expectedValue = styleValue(value, expectedType);
  const receivedValue = styleValue(value, receivedType);
  if (expectedTypes.length === 1 && isExplicable(expectedType) && !isBoolean(expectedType, receivedType)) {
    message += ` with value ${expectedValue}`;
  }
  message += `, got ${receivedType} `;
  if (isExplicable(receivedType)) {
    message += `with value ${receivedValue}.`;
  }
  return message;
}
function styleValue(value, type) {
  if (type === "String") {
    return `"${value}"`;
  } else if (type === "Number") {
    return `${Number(value)}`;
  } else {
    return `${value}`;
  }
}
function isExplicable(type) {
  const explicitTypes = ["string", "number", "boolean"];
  return explicitTypes.some((elem) => type.toLowerCase() === elem);
}
function isBoolean(...args) {
  return args.some((elem) => elem.toLowerCase() === "boolean");
}
const isInternalKey = (key) => key[0] === "_" || key === "$stable";
const normalizeSlotValue = (value) => isArray(value) ? value.map(normalizeVNode) : [normalizeVNode(value)];
const normalizeSlot = (key, rawSlot, ctx) => {
  if (rawSlot._n) {
    return rawSlot;
  }
  const normalized = withCtx((...args) => {
    if (!!(process.env.NODE_ENV !== "production") && currentInstance && !(ctx === null && currentRenderingInstance) && !(ctx && ctx.root !== currentInstance.root)) {
      warn$1(
        `Slot "${key}" invoked outside of the render function: this will not track dependencies used in the slot. Invoke the slot function inside the render function instead.`
      );
    }
    return normalizeSlotValue(rawSlot(...args));
  }, ctx);
  normalized._c = false;
  return normalized;
};
const normalizeObjectSlots = (rawSlots, slots, instance) => {
  const ctx = rawSlots._ctx;
  for (const key in rawSlots) {
    if (isInternalKey(key)) continue;
    const value = rawSlots[key];
    if (isFunction(value)) {
      slots[key] = normalizeSlot(key, value, ctx);
    } else if (value != null) {
      if (!!(process.env.NODE_ENV !== "production") && true) {
        warn$1(
          `Non-function value encountered for slot "${key}". Prefer function slots for better performance.`
        );
      }
      const normalized = normalizeSlotValue(value);
      slots[key] = () => normalized;
    }
  }
};
const normalizeVNodeSlots = (instance, children) => {
  if (!!(process.env.NODE_ENV !== "production") && !isKeepAlive(instance.vnode) && true) {
    warn$1(
      `Non-function value encountered for default slot. Prefer function slots for better performance.`
    );
  }
  const normalized = normalizeSlotValue(children);
  instance.slots.default = () => normalized;
};
const assignSlots = (slots, children, optimized) => {
  for (const key in children) {
    if (optimized || !isInternalKey(key)) {
      slots[key] = children[key];
    }
  }
};
const initSlots = (instance, children, optimized) => {
  const slots = instance.slots = createInternalObject();
  if (instance.vnode.shapeFlag & 32) {
    const cacheIndexes = children.__;
    if (cacheIndexes) def(slots, "__", cacheIndexes, true);
    const type = children._;
    if (type) {
      assignSlots(slots, children, optimized);
      if (optimized) {
        def(slots, "_", type, true);
      }
    } else {
      normalizeObjectSlots(children, slots);
    }
  } else if (children) {
    normalizeVNodeSlots(instance, children);
  }
};
const updateSlots = (instance, children, optimized) => {
  const { vnode, slots } = instance;
  let needDeletionCheck = true;
  let deletionComparisonTarget = EMPTY_OBJ;
  if (vnode.shapeFlag & 32) {
    const type = children._;
    if (type) {
      if (!!(process.env.NODE_ENV !== "production") && isHmrUpdating) {
        assignSlots(slots, children, optimized);
        trigger(instance, "set", "$slots");
      } else if (optimized && type === 1) {
        needDeletionCheck = false;
      } else {
        assignSlots(slots, children, optimized);
      }
    } else {
      needDeletionCheck = !children.$stable;
      normalizeObjectSlots(children, slots);
    }
    deletionComparisonTarget = children;
  } else if (children) {
    normalizeVNodeSlots(instance, children);
    deletionComparisonTarget = { default: 1 };
  }
  if (needDeletionCheck) {
    for (const key in slots) {
      if (!isInternalKey(key) && deletionComparisonTarget[key] == null) {
        delete slots[key];
      }
    }
  }
};
let supported;
let perf;
function startMeasure(instance, type) {
  if (instance.appContext.config.performance && isSupported()) {
    perf.mark(`vue-${type}-${instance.uid}`);
  }
  if (!!(process.env.NODE_ENV !== "production") || false) {
    devtoolsPerfStart(instance, type, isSupported() ? perf.now() : Date.now());
  }
}
function endMeasure(instance, type) {
  if (instance.appContext.config.performance && isSupported()) {
    const startTag = `vue-${type}-${instance.uid}`;
    const endTag = startTag + `:end`;
    perf.mark(endTag);
    perf.measure(
      `<${formatComponentName(instance, instance.type)}> ${type}`,
      startTag,
      endTag
    );
    perf.clearMarks(startTag);
    perf.clearMarks(endTag);
  }
  if (!!(process.env.NODE_ENV !== "production") || false) {
    devtoolsPerfEnd(instance, type, isSupported() ? perf.now() : Date.now());
  }
}
function isSupported() {
  if (supported !== void 0) {
    return supported;
  }
  if (typeof window !== "undefined" && window.performance) {
    supported = true;
    perf = window.performance;
  } else {
    supported = false;
  }
  return supported;
}
function initFeatureFlags() {
  const needWarn = [];
  if (!!(process.env.NODE_ENV !== "production") && needWarn.length) {
    const multi = needWarn.length > 1;
    console.warn(
      `Feature flag${multi ? `s` : ``} ${needWarn.join(", ")} ${multi ? `are` : `is`} not explicitly defined. You are running the esm-bundler build of Vue, which expects these compile-time feature flags to be globally injected via the bundler config in order to get better tree-shaking in the production bundle.

For more details, see https://link.vuejs.org/feature-flags.`
    );
  }
}
const queuePostRenderEffect = queueEffectWithSuspense;
function createRenderer(options) {
  return baseCreateRenderer(options);
}
function baseCreateRenderer(options, createHydrationFns) {
  {
    initFeatureFlags();
  }
  const target = getGlobalThis();
  target.__VUE__ = true;
  if (!!(process.env.NODE_ENV !== "production") || false) {
    setDevtoolsHook$1(target.__VUE_DEVTOOLS_GLOBAL_HOOK__, target);
  }
  const {
    insert: hostInsert,
    remove: hostRemove,
    patchProp: hostPatchProp,
    createElement: hostCreateElement,
    createText: hostCreateText,
    createComment: hostCreateComment,
    setText: hostSetText,
    setElementText: hostSetElementText,
    parentNode: hostParentNode,
    nextSibling: hostNextSibling,
    setScopeId: hostSetScopeId = NOOP,
    insertStaticContent: hostInsertStaticContent
  } = options;
  const patch = (n1, n2, container, anchor = null, parentComponent = null, parentSuspense = null, namespace = void 0, slotScopeIds = null, optimized = !!(process.env.NODE_ENV !== "production") && isHmrUpdating ? false : !!n2.dynamicChildren) => {
    if (n1 === n2) {
      return;
    }
    if (n1 && !isSameVNodeType(n1, n2)) {
      anchor = getNextHostNode(n1);
      unmount(n1, parentComponent, parentSuspense, true);
      n1 = null;
    }
    if (n2.patchFlag === -2) {
      optimized = false;
      n2.dynamicChildren = null;
    }
    const { type, ref: ref3, shapeFlag } = n2;
    switch (type) {
      case Text:
        processText(n1, n2, container, anchor);
        break;
      case Comment:
        processCommentNode(n1, n2, container, anchor);
        break;
      case Static:
        if (n1 == null) {
          mountStaticNode(n2, container, anchor, namespace);
        } else if (!!(process.env.NODE_ENV !== "production")) {
          patchStaticNode(n1, n2, container, namespace);
        }
        break;
      case Fragment:
        processFragment(
          n1,
          n2,
          container,
          anchor,
          parentComponent,
          parentSuspense,
          namespace,
          slotScopeIds,
          optimized
        );
        break;
      default:
        if (shapeFlag & 1) {
          processElement(
            n1,
            n2,
            container,
            anchor,
            parentComponent,
            parentSuspense,
            namespace,
            slotScopeIds,
            optimized
          );
        } else if (shapeFlag & 6) {
          processComponent(
            n1,
            n2,
            container,
            anchor,
            parentComponent,
            parentSuspense,
            namespace,
            slotScopeIds,
            optimized
          );
        } else if (shapeFlag & 64) {
          type.process(
            n1,
            n2,
            container,
            anchor,
            parentComponent,
            parentSuspense,
            namespace,
            slotScopeIds,
            optimized,
            internals
          );
        } else if (shapeFlag & 128) {
          type.process(
            n1,
            n2,
            container,
            anchor,
            parentComponent,
            parentSuspense,
            namespace,
            slotScopeIds,
            optimized,
            internals
          );
        } else if (!!(process.env.NODE_ENV !== "production")) {
          warn$1("Invalid VNode type:", type, `(${typeof type})`);
        }
    }
    if (ref3 != null && parentComponent) {
      setRef(ref3, n1 && n1.ref, parentSuspense, n2 || n1, !n2);
    } else if (ref3 == null && n1 && n1.ref != null) {
      setRef(n1.ref, null, parentSuspense, n1, true);
    }
  };
  const processText = (n1, n2, container, anchor) => {
    if (n1 == null) {
      hostInsert(
        n2.el = hostCreateText(n2.children),
        container,
        anchor
      );
    } else {
      const el = n2.el = n1.el;
      if (n2.children !== n1.children) {
        hostSetText(el, n2.children);
      }
    }
  };
  const processCommentNode = (n1, n2, container, anchor) => {
    if (n1 == null) {
      hostInsert(
        n2.el = hostCreateComment(n2.children || ""),
        container,
        anchor
      );
    } else {
      n2.el = n1.el;
    }
  };
  const mountStaticNode = (n2, container, anchor, namespace) => {
    [n2.el, n2.anchor] = hostInsertStaticContent(
      n2.children,
      container,
      anchor,
      namespace,
      n2.el,
      n2.anchor
    );
  };
  const patchStaticNode = (n1, n2, container, namespace) => {
    if (n2.children !== n1.children) {
      const anchor = hostNextSibling(n1.anchor);
      removeStaticNode(n1);
      [n2.el, n2.anchor] = hostInsertStaticContent(
        n2.children,
        container,
        anchor,
        namespace
      );
    } else {
      n2.el = n1.el;
      n2.anchor = n1.anchor;
    }
  };
  const moveStaticNode = ({ el, anchor }, container, nextSibling2) => {
    let next;
    while (el && el !== anchor) {
      next = hostNextSibling(el);
      hostInsert(el, container, nextSibling2);
      el = next;
    }
    hostInsert(anchor, container, nextSibling2);
  };
  const removeStaticNode = ({ el, anchor }) => {
    let next;
    while (el && el !== anchor) {
      next = hostNextSibling(el);
      hostRemove(el);
      el = next;
    }
    hostRemove(anchor);
  };
  const processElement = (n1, n2, container, anchor, parentComponent, parentSuspense, namespace, slotScopeIds, optimized) => {
    if (n2.type === "svg") {
      namespace = "svg";
    } else if (n2.type === "math") {
      namespace = "mathml";
    }
    if (n1 == null) {
      mountElement(
        n2,
        container,
        anchor,
        parentComponent,
        parentSuspense,
        namespace,
        slotScopeIds,
        optimized
      );
    } else {
      patchElement(
        n1,
        n2,
        parentComponent,
        parentSuspense,
        namespace,
        slotScopeIds,
        optimized
      );
    }
  };
  const mountElement = (vnode, container, anchor, parentComponent, parentSuspense, namespace, slotScopeIds, optimized) => {
    let el;
    let vnodeHook;
    const { props, shapeFlag, transition, dirs } = vnode;
    el = vnode.el = hostCreateElement(
      vnode.type,
      namespace,
      props && props.is,
      props
    );
    if (shapeFlag & 8) {
      hostSetElementText(el, vnode.children);
    } else if (shapeFlag & 16) {
      mountChildren(
        vnode.children,
        el,
        null,
        parentComponent,
        parentSuspense,
        resolveChildrenNamespace(vnode, namespace),
        slotScopeIds,
        optimized
      );
    }
    if (dirs) {
      invokeDirectiveHook(vnode, null, parentComponent, "created");
    }
    setScopeId2(el, vnode, vnode.scopeId, slotScopeIds, parentComponent);
    if (props) {
      for (const key in props) {
        if (key !== "value" && !isReservedProp(key)) {
          hostPatchProp(el, key, null, props[key], namespace, parentComponent);
        }
      }
      if ("value" in props) {
        hostPatchProp(el, "value", null, props.value, namespace);
      }
      if (vnodeHook = props.onVnodeBeforeMount) {
        invokeVNodeHook(vnodeHook, parentComponent, vnode);
      }
    }
    if (!!(process.env.NODE_ENV !== "production") || false) {
      def(el, "__vnode", vnode, true);
      def(el, "__vueParentComponent", parentComponent, true);
    }
    if (dirs) {
      invokeDirectiveHook(vnode, null, parentComponent, "beforeMount");
    }
    const needCallTransitionHooks = needTransition(parentSuspense, transition);
    if (needCallTransitionHooks) {
      transition.beforeEnter(el);
    }
    hostInsert(el, container, anchor);
    if ((vnodeHook = props && props.onVnodeMounted) || needCallTransitionHooks || dirs) {
      queuePostRenderEffect(() => {
        vnodeHook && invokeVNodeHook(vnodeHook, parentComponent, vnode);
        needCallTransitionHooks && transition.enter(el);
        dirs && invokeDirectiveHook(vnode, null, parentComponent, "mounted");
      }, parentSuspense);
    }
  };
  const setScopeId2 = (el, vnode, scopeId, slotScopeIds, parentComponent) => {
    if (scopeId) {
      hostSetScopeId(el, scopeId);
    }
    if (slotScopeIds) {
      for (let i2 = 0; i2 < slotScopeIds.length; i2++) {
        hostSetScopeId(el, slotScopeIds[i2]);
      }
    }
    if (parentComponent) {
      let subTree = parentComponent.subTree;
      if (!!(process.env.NODE_ENV !== "production") && subTree.patchFlag > 0 && subTree.patchFlag & 2048) {
        subTree = filterSingleRoot(subTree.children) || subTree;
      }
      if (vnode === subTree || isSuspense(subTree.type) && (subTree.ssContent === vnode || subTree.ssFallback === vnode)) {
        const parentVNode = parentComponent.vnode;
        setScopeId2(
          el,
          parentVNode,
          parentVNode.scopeId,
          parentVNode.slotScopeIds,
          parentComponent.parent
        );
      }
    }
  };
  const mountChildren = (children, container, anchor, parentComponent, parentSuspense, namespace, slotScopeIds, optimized, start = 0) => {
    for (let i2 = start; i2 < children.length; i2++) {
      const child = children[i2] = optimized ? cloneIfMounted(children[i2]) : normalizeVNode(children[i2]);
      patch(
        null,
        child,
        container,
        anchor,
        parentComponent,
        parentSuspense,
        namespace,
        slotScopeIds,
        optimized
      );
    }
  };
  const patchElement = (n1, n2, parentComponent, parentSuspense, namespace, slotScopeIds, optimized) => {
    const el = n2.el = n1.el;
    if (!!(process.env.NODE_ENV !== "production") || false) {
      el.__vnode = n2;
    }
    let { patchFlag, dynamicChildren, dirs } = n2;
    patchFlag |= n1.patchFlag & 16;
    const oldProps = n1.props || EMPTY_OBJ;
    const newProps = n2.props || EMPTY_OBJ;
    let vnodeHook;
    parentComponent && toggleRecurse(parentComponent, false);
    if (vnodeHook = newProps.onVnodeBeforeUpdate) {
      invokeVNodeHook(vnodeHook, parentComponent, n2, n1);
    }
    if (dirs) {
      invokeDirectiveHook(n2, n1, parentComponent, "beforeUpdate");
    }
    parentComponent && toggleRecurse(parentComponent, true);
    if (!!(process.env.NODE_ENV !== "production") && isHmrUpdating) {
      patchFlag = 0;
      optimized = false;
      dynamicChildren = null;
    }
    if (oldProps.innerHTML && newProps.innerHTML == null || oldProps.textContent && newProps.textContent == null) {
      hostSetElementText(el, "");
    }
    if (dynamicChildren) {
      patchBlockChildren(
        n1.dynamicChildren,
        dynamicChildren,
        el,
        parentComponent,
        parentSuspense,
        resolveChildrenNamespace(n2, namespace),
        slotScopeIds
      );
      if (!!(process.env.NODE_ENV !== "production")) {
        traverseStaticChildren(n1, n2);
      }
    } else if (!optimized) {
      patchChildren(
        n1,
        n2,
        el,
        null,
        parentComponent,
        parentSuspense,
        resolveChildrenNamespace(n2, namespace),
        slotScopeIds,
        false
      );
    }
    if (patchFlag > 0) {
      if (patchFlag & 16) {
        patchProps(el, oldProps, newProps, parentComponent, namespace);
      } else {
        if (patchFlag & 2) {
          if (oldProps.class !== newProps.class) {
            hostPatchProp(el, "class", null, newProps.class, namespace);
          }
        }
        if (patchFlag & 4) {
          hostPatchProp(el, "style", oldProps.style, newProps.style, namespace);
        }
        if (patchFlag & 8) {
          const propsToUpdate = n2.dynamicProps;
          for (let i2 = 0; i2 < propsToUpdate.length; i2++) {
            const key = propsToUpdate[i2];
            const prev = oldProps[key];
            const next = newProps[key];
            if (next !== prev || key === "value") {
              hostPatchProp(el, key, prev, next, namespace, parentComponent);
            }
          }
        }
      }
      if (patchFlag & 1) {
        if (n1.children !== n2.children) {
          hostSetElementText(el, n2.children);
        }
      }
    } else if (!optimized && dynamicChildren == null) {
      patchProps(el, oldProps, newProps, parentComponent, namespace);
    }
    if ((vnodeHook = newProps.onVnodeUpdated) || dirs) {
      queuePostRenderEffect(() => {
        vnodeHook && invokeVNodeHook(vnodeHook, parentComponent, n2, n1);
        dirs && invokeDirectiveHook(n2, n1, parentComponent, "updated");
      }, parentSuspense);
    }
  };
  const patchBlockChildren = (oldChildren, newChildren, fallbackContainer, parentComponent, parentSuspense, namespace, slotScopeIds) => {
    for (let i2 = 0; i2 < newChildren.length; i2++) {
      const oldVNode = oldChildren[i2];
      const newVNode = newChildren[i2];
      const container = (
        // oldVNode may be an errored async setup() component inside Suspense
        // which will not have a mounted element
        oldVNode.el && // - In the case of a Fragment, we need to provide the actual parent
        // of the Fragment itself so it can move its children.
        (oldVNode.type === Fragment || // - In the case of different nodes, there is going to be a replacement
        // which also requires the correct parent container
        !isSameVNodeType(oldVNode, newVNode) || // - In the case of a component, it could contain anything.
        oldVNode.shapeFlag & (6 | 64 | 128)) ? hostParentNode(oldVNode.el) : (
          // In other cases, the parent container is not actually used so we
          // just pass the block element here to avoid a DOM parentNode call.
          fallbackContainer
        )
      );
      patch(
        oldVNode,
        newVNode,
        container,
        null,
        parentComponent,
        parentSuspense,
        namespace,
        slotScopeIds,
        true
      );
    }
  };
  const patchProps = (el, oldProps, newProps, parentComponent, namespace) => {
    if (oldProps !== newProps) {
      if (oldProps !== EMPTY_OBJ) {
        for (const key in oldProps) {
          if (!isReservedProp(key) && !(key in newProps)) {
            hostPatchProp(
              el,
              key,
              oldProps[key],
              null,
              namespace,
              parentComponent
            );
          }
        }
      }
      for (const key in newProps) {
        if (isReservedProp(key)) continue;
        const next = newProps[key];
        const prev = oldProps[key];
        if (next !== prev && key !== "value") {
          hostPatchProp(el, key, prev, next, namespace, parentComponent);
        }
      }
      if ("value" in newProps) {
        hostPatchProp(el, "value", oldProps.value, newProps.value, namespace);
      }
    }
  };
  const processFragment = (n1, n2, container, anchor, parentComponent, parentSuspense, namespace, slotScopeIds, optimized) => {
    const fragmentStartAnchor = n2.el = n1 ? n1.el : hostCreateText("");
    const fragmentEndAnchor = n2.anchor = n1 ? n1.anchor : hostCreateText("");
    let { patchFlag, dynamicChildren, slotScopeIds: fragmentSlotScopeIds } = n2;
    if (!!(process.env.NODE_ENV !== "production") && // #5523 dev root fragment may inherit directives
    (isHmrUpdating || patchFlag & 2048)) {
      patchFlag = 0;
      optimized = false;
      dynamicChildren = null;
    }
    if (fragmentSlotScopeIds) {
      slotScopeIds = slotScopeIds ? slotScopeIds.concat(fragmentSlotScopeIds) : fragmentSlotScopeIds;
    }
    if (n1 == null) {
      hostInsert(fragmentStartAnchor, container, anchor);
      hostInsert(fragmentEndAnchor, container, anchor);
      mountChildren(
        // #10007
        // such fragment like `<></>` will be compiled into
        // a fragment which doesn't have a children.
        // In this case fallback to an empty array
        n2.children || [],
        container,
        fragmentEndAnchor,
        parentComponent,
        parentSuspense,
        namespace,
        slotScopeIds,
        optimized
      );
    } else {
      if (patchFlag > 0 && patchFlag & 64 && dynamicChildren && // #2715 the previous fragment could've been a BAILed one as a result
      // of renderSlot() with no valid children
      n1.dynamicChildren) {
        patchBlockChildren(
          n1.dynamicChildren,
          dynamicChildren,
          container,
          parentComponent,
          parentSuspense,
          namespace,
          slotScopeIds
        );
        if (!!(process.env.NODE_ENV !== "production")) {
          traverseStaticChildren(n1, n2);
        } else if (
          // #2080 if the stable fragment has a key, it's a <template v-for> that may
          //  get moved around. Make sure all root level vnodes inherit el.
          // #2134 or if it's a component root, it may also get moved around
          // as the component is being moved.
          n2.key != null || parentComponent && n2 === parentComponent.subTree
        ) {
          traverseStaticChildren(
            n1,
            n2,
            true
            /* shallow */
          );
        }
      } else {
        patchChildren(
          n1,
          n2,
          container,
          fragmentEndAnchor,
          parentComponent,
          parentSuspense,
          namespace,
          slotScopeIds,
          optimized
        );
      }
    }
  };
  const processComponent = (n1, n2, container, anchor, parentComponent, parentSuspense, namespace, slotScopeIds, optimized) => {
    n2.slotScopeIds = slotScopeIds;
    if (n1 == null) {
      if (n2.shapeFlag & 512) {
        parentComponent.ctx.activate(
          n2,
          container,
          anchor,
          namespace,
          optimized
        );
      } else {
        mountComponent(
          n2,
          container,
          anchor,
          parentComponent,
          parentSuspense,
          namespace,
          optimized
        );
      }
    } else {
      updateComponent(n1, n2, optimized);
    }
  };
  const mountComponent = (initialVNode, container, anchor, parentComponent, parentSuspense, namespace, optimized) => {
    const instance = initialVNode.component = createComponentInstance(
      initialVNode,
      parentComponent,
      parentSuspense
    );
    if (!!(process.env.NODE_ENV !== "production") && instance.type.__hmrId) {
      registerHMR(instance);
    }
    if (!!(process.env.NODE_ENV !== "production")) {
      pushWarningContext(initialVNode);
      startMeasure(instance, `mount`);
    }
    if (isKeepAlive(initialVNode)) {
      instance.ctx.renderer = internals;
    }
    {
      if (!!(process.env.NODE_ENV !== "production")) {
        startMeasure(instance, `init`);
      }
      setupComponent(instance, false, optimized);
      if (!!(process.env.NODE_ENV !== "production")) {
        endMeasure(instance, `init`);
      }
    }
    if (!!(process.env.NODE_ENV !== "production") && isHmrUpdating) initialVNode.el = null;
    if (instance.asyncDep) {
      parentSuspense && parentSuspense.registerDep(instance, setupRenderEffect, optimized);
      if (!initialVNode.el) {
        const placeholder = instance.subTree = createVNode(Comment);
        processCommentNode(null, placeholder, container, anchor);
      }
    } else {
      setupRenderEffect(
        instance,
        initialVNode,
        container,
        anchor,
        parentSuspense,
        namespace,
        optimized
      );
    }
    if (!!(process.env.NODE_ENV !== "production")) {
      popWarningContext();
      endMeasure(instance, `mount`);
    }
  };
  const updateComponent = (n1, n2, optimized) => {
    const instance = n2.component = n1.component;
    if (shouldUpdateComponent(n1, n2, optimized)) {
      if (instance.asyncDep && !instance.asyncResolved) {
        if (!!(process.env.NODE_ENV !== "production")) {
          pushWarningContext(n2);
        }
        updateComponentPreRender(instance, n2, optimized);
        if (!!(process.env.NODE_ENV !== "production")) {
          popWarningContext();
        }
        return;
      } else {
        instance.next = n2;
        instance.update();
      }
    } else {
      n2.el = n1.el;
      instance.vnode = n2;
    }
  };
  const setupRenderEffect = (instance, initialVNode, container, anchor, parentSuspense, namespace, optimized) => {
    const componentUpdateFn = () => {
      if (!instance.isMounted) {
        let vnodeHook;
        const { el, props } = initialVNode;
        const { bm, m, parent, root, type } = instance;
        const isAsyncWrapperVNode = isAsyncWrapper(initialVNode);
        toggleRecurse(instance, false);
        if (bm) {
          invokeArrayFns(bm);
        }
        if (!isAsyncWrapperVNode && (vnodeHook = props && props.onVnodeBeforeMount)) {
          invokeVNodeHook(vnodeHook, parent, initialVNode);
        }
        toggleRecurse(instance, true);
        {
          if (root.ce && // @ts-expect-error _def is private
          root.ce._def.shadowRoot !== false) {
            root.ce._injectChildStyle(type);
          }
          if (!!(process.env.NODE_ENV !== "production")) {
            startMeasure(instance, `render`);
          }
          const subTree = instance.subTree = renderComponentRoot(instance);
          if (!!(process.env.NODE_ENV !== "production")) {
            endMeasure(instance, `render`);
          }
          if (!!(process.env.NODE_ENV !== "production")) {
            startMeasure(instance, `patch`);
          }
          patch(
            null,
            subTree,
            container,
            anchor,
            instance,
            parentSuspense,
            namespace
          );
          if (!!(process.env.NODE_ENV !== "production")) {
            endMeasure(instance, `patch`);
          }
          initialVNode.el = subTree.el;
        }
        if (m) {
          queuePostRenderEffect(m, parentSuspense);
        }
        if (!isAsyncWrapperVNode && (vnodeHook = props && props.onVnodeMounted)) {
          const scopedInitialVNode = initialVNode;
          queuePostRenderEffect(
            () => invokeVNodeHook(vnodeHook, parent, scopedInitialVNode),
            parentSuspense
          );
        }
        if (initialVNode.shapeFlag & 256 || parent && isAsyncWrapper(parent.vnode) && parent.vnode.shapeFlag & 256) {
          instance.a && queuePostRenderEffect(instance.a, parentSuspense);
        }
        instance.isMounted = true;
        if (!!(process.env.NODE_ENV !== "production") || false) {
          devtoolsComponentAdded(instance);
        }
        initialVNode = container = anchor = null;
      } else {
        let { next, bu, u, parent, vnode } = instance;
        {
          const nonHydratedAsyncRoot = locateNonHydratedAsyncRoot(instance);
          if (nonHydratedAsyncRoot) {
            if (next) {
              next.el = vnode.el;
              updateComponentPreRender(instance, next, optimized);
            }
            nonHydratedAsyncRoot.asyncDep.then(() => {
              if (!instance.isUnmounted) {
                componentUpdateFn();
              }
            });
            return;
          }
        }
        let originNext = next;
        let vnodeHook;
        if (!!(process.env.NODE_ENV !== "production")) {
          pushWarningContext(next || instance.vnode);
        }
        toggleRecurse(instance, false);
        if (next) {
          next.el = vnode.el;
          updateComponentPreRender(instance, next, optimized);
        } else {
          next = vnode;
        }
        if (bu) {
          invokeArrayFns(bu);
        }
        if (vnodeHook = next.props && next.props.onVnodeBeforeUpdate) {
          invokeVNodeHook(vnodeHook, parent, next, vnode);
        }
        toggleRecurse(instance, true);
        if (!!(process.env.NODE_ENV !== "production")) {
          startMeasure(instance, `render`);
        }
        const nextTree = renderComponentRoot(instance);
        if (!!(process.env.NODE_ENV !== "production")) {
          endMeasure(instance, `render`);
        }
        const prevTree = instance.subTree;
        instance.subTree = nextTree;
        if (!!(process.env.NODE_ENV !== "production")) {
          startMeasure(instance, `patch`);
        }
        patch(
          prevTree,
          nextTree,
          // parent may have changed if it's in a teleport
          hostParentNode(prevTree.el),
          // anchor may have changed if it's in a fragment
          getNextHostNode(prevTree),
          instance,
          parentSuspense,
          namespace
        );
        if (!!(process.env.NODE_ENV !== "production")) {
          endMeasure(instance, `patch`);
        }
        next.el = nextTree.el;
        if (originNext === null) {
          updateHOCHostEl(instance, nextTree.el);
        }
        if (u) {
          queuePostRenderEffect(u, parentSuspense);
        }
        if (vnodeHook = next.props && next.props.onVnodeUpdated) {
          queuePostRenderEffect(
            () => invokeVNodeHook(vnodeHook, parent, next, vnode),
            parentSuspense
          );
        }
        if (!!(process.env.NODE_ENV !== "production") || false) {
          devtoolsComponentUpdated(instance);
        }
        if (!!(process.env.NODE_ENV !== "production")) {
          popWarningContext();
        }
      }
    };
    instance.scope.on();
    const effect2 = instance.effect = new ReactiveEffect(componentUpdateFn);
    instance.scope.off();
    const update = instance.update = effect2.run.bind(effect2);
    const job = instance.job = effect2.runIfDirty.bind(effect2);
    job.i = instance;
    job.id = instance.uid;
    effect2.scheduler = () => queueJob(job);
    toggleRecurse(instance, true);
    if (!!(process.env.NODE_ENV !== "production")) {
      effect2.onTrack = instance.rtc ? (e2) => invokeArrayFns(instance.rtc, e2) : void 0;
      effect2.onTrigger = instance.rtg ? (e2) => invokeArrayFns(instance.rtg, e2) : void 0;
    }
    update();
  };
  const updateComponentPreRender = (instance, nextVNode, optimized) => {
    nextVNode.component = instance;
    const prevProps = instance.vnode.props;
    instance.vnode = nextVNode;
    instance.next = null;
    updateProps(instance, nextVNode.props, prevProps, optimized);
    updateSlots(instance, nextVNode.children, optimized);
    pauseTracking();
    flushPreFlushCbs(instance);
    resetTracking();
  };
  const patchChildren = (n1, n2, container, anchor, parentComponent, parentSuspense, namespace, slotScopeIds, optimized = false) => {
    const c1 = n1 && n1.children;
    const prevShapeFlag = n1 ? n1.shapeFlag : 0;
    const c2 = n2.children;
    const { patchFlag, shapeFlag } = n2;
    if (patchFlag > 0) {
      if (patchFlag & 128) {
        patchKeyedChildren(
          c1,
          c2,
          container,
          anchor,
          parentComponent,
          parentSuspense,
          namespace,
          slotScopeIds,
          optimized
        );
        return;
      } else if (patchFlag & 256) {
        patchUnkeyedChildren(
          c1,
          c2,
          container,
          anchor,
          parentComponent,
          parentSuspense,
          namespace,
          slotScopeIds,
          optimized
        );
        return;
      }
    }
    if (shapeFlag & 8) {
      if (prevShapeFlag & 16) {
        unmountChildren(c1, parentComponent, parentSuspense);
      }
      if (c2 !== c1) {
        hostSetElementText(container, c2);
      }
    } else {
      if (prevShapeFlag & 16) {
        if (shapeFlag & 16) {
          patchKeyedChildren(
            c1,
            c2,
            container,
            anchor,
            parentComponent,
            parentSuspense,
            namespace,
            slotScopeIds,
            optimized
          );
        } else {
          unmountChildren(c1, parentComponent, parentSuspense, true);
        }
      } else {
        if (prevShapeFlag & 8) {
          hostSetElementText(container, "");
        }
        if (shapeFlag & 16) {
          mountChildren(
            c2,
            container,
            anchor,
            parentComponent,
            parentSuspense,
            namespace,
            slotScopeIds,
            optimized
          );
        }
      }
    }
  };
  const patchUnkeyedChildren = (c1, c2, container, anchor, parentComponent, parentSuspense, namespace, slotScopeIds, optimized) => {
    c1 = c1 || EMPTY_ARR;
    c2 = c2 || EMPTY_ARR;
    const oldLength = c1.length;
    const newLength = c2.length;
    const commonLength = Math.min(oldLength, newLength);
    let i2;
    for (i2 = 0; i2 < commonLength; i2++) {
      const nextChild = c2[i2] = optimized ? cloneIfMounted(c2[i2]) : normalizeVNode(c2[i2]);
      patch(
        c1[i2],
        nextChild,
        container,
        null,
        parentComponent,
        parentSuspense,
        namespace,
        slotScopeIds,
        optimized
      );
    }
    if (oldLength > newLength) {
      unmountChildren(
        c1,
        parentComponent,
        parentSuspense,
        true,
        false,
        commonLength
      );
    } else {
      mountChildren(
        c2,
        container,
        anchor,
        parentComponent,
        parentSuspense,
        namespace,
        slotScopeIds,
        optimized,
        commonLength
      );
    }
  };
  const patchKeyedChildren = (c1, c2, container, parentAnchor, parentComponent, parentSuspense, namespace, slotScopeIds, optimized) => {
    let i2 = 0;
    const l2 = c2.length;
    let e1 = c1.length - 1;
    let e2 = l2 - 1;
    while (i2 <= e1 && i2 <= e2) {
      const n1 = c1[i2];
      const n2 = c2[i2] = optimized ? cloneIfMounted(c2[i2]) : normalizeVNode(c2[i2]);
      if (isSameVNodeType(n1, n2)) {
        patch(
          n1,
          n2,
          container,
          null,
          parentComponent,
          parentSuspense,
          namespace,
          slotScopeIds,
          optimized
        );
      } else {
        break;
      }
      i2++;
    }
    while (i2 <= e1 && i2 <= e2) {
      const n1 = c1[e1];
      const n2 = c2[e2] = optimized ? cloneIfMounted(c2[e2]) : normalizeVNode(c2[e2]);
      if (isSameVNodeType(n1, n2)) {
        patch(
          n1,
          n2,
          container,
          null,
          parentComponent,
          parentSuspense,
          namespace,
          slotScopeIds,
          optimized
        );
      } else {
        break;
      }
      e1--;
      e2--;
    }
    if (i2 > e1) {
      if (i2 <= e2) {
        const nextPos = e2 + 1;
        const anchor = nextPos < l2 ? c2[nextPos].el : parentAnchor;
        while (i2 <= e2) {
          patch(
            null,
            c2[i2] = optimized ? cloneIfMounted(c2[i2]) : normalizeVNode(c2[i2]),
            container,
            anchor,
            parentComponent,
            parentSuspense,
            namespace,
            slotScopeIds,
            optimized
          );
          i2++;
        }
      }
    } else if (i2 > e2) {
      while (i2 <= e1) {
        unmount(c1[i2], parentComponent, parentSuspense, true);
        i2++;
      }
    } else {
      const s1 = i2;
      const s2 = i2;
      const keyToNewIndexMap = /* @__PURE__ */ new Map();
      for (i2 = s2; i2 <= e2; i2++) {
        const nextChild = c2[i2] = optimized ? cloneIfMounted(c2[i2]) : normalizeVNode(c2[i2]);
        if (nextChild.key != null) {
          if (!!(process.env.NODE_ENV !== "production") && keyToNewIndexMap.has(nextChild.key)) {
            warn$1(
              `Duplicate keys found during update:`,
              JSON.stringify(nextChild.key),
              `Make sure keys are unique.`
            );
          }
          keyToNewIndexMap.set(nextChild.key, i2);
        }
      }
      let j;
      let patched = 0;
      const toBePatched = e2 - s2 + 1;
      let moved = false;
      let maxNewIndexSoFar = 0;
      const newIndexToOldIndexMap = new Array(toBePatched);
      for (i2 = 0; i2 < toBePatched; i2++) newIndexToOldIndexMap[i2] = 0;
      for (i2 = s1; i2 <= e1; i2++) {
        const prevChild = c1[i2];
        if (patched >= toBePatched) {
          unmount(prevChild, parentComponent, parentSuspense, true);
          continue;
        }
        let newIndex;
        if (prevChild.key != null) {
          newIndex = keyToNewIndexMap.get(prevChild.key);
        } else {
          for (j = s2; j <= e2; j++) {
            if (newIndexToOldIndexMap[j - s2] === 0 && isSameVNodeType(prevChild, c2[j])) {
              newIndex = j;
              break;
            }
          }
        }
        if (newIndex === void 0) {
          unmount(prevChild, parentComponent, parentSuspense, true);
        } else {
          newIndexToOldIndexMap[newIndex - s2] = i2 + 1;
          if (newIndex >= maxNewIndexSoFar) {
            maxNewIndexSoFar = newIndex;
          } else {
            moved = true;
          }
          patch(
            prevChild,
            c2[newIndex],
            container,
            null,
            parentComponent,
            parentSuspense,
            namespace,
            slotScopeIds,
            optimized
          );
          patched++;
        }
      }
      const increasingNewIndexSequence = moved ? getSequence(newIndexToOldIndexMap) : EMPTY_ARR;
      j = increasingNewIndexSequence.length - 1;
      for (i2 = toBePatched - 1; i2 >= 0; i2--) {
        const nextIndex = s2 + i2;
        const nextChild = c2[nextIndex];
        const anchor = nextIndex + 1 < l2 ? c2[nextIndex + 1].el : parentAnchor;
        if (newIndexToOldIndexMap[i2] === 0) {
          patch(
            null,
            nextChild,
            container,
            anchor,
            parentComponent,
            parentSuspense,
            namespace,
            slotScopeIds,
            optimized
          );
        } else if (moved) {
          if (j < 0 || i2 !== increasingNewIndexSequence[j]) {
            move(nextChild, container, anchor, 2);
          } else {
            j--;
          }
        }
      }
    }
  };
  const move = (vnode, container, anchor, moveType, parentSuspense = null) => {
    const { el, type, transition, children, shapeFlag } = vnode;
    if (shapeFlag & 6) {
      move(vnode.component.subTree, container, anchor, moveType);
      return;
    }
    if (shapeFlag & 128) {
      vnode.suspense.move(container, anchor, moveType);
      return;
    }
    if (shapeFlag & 64) {
      type.move(vnode, container, anchor, internals);
      return;
    }
    if (type === Fragment) {
      hostInsert(el, container, anchor);
      for (let i2 = 0; i2 < children.length; i2++) {
        move(children[i2], container, anchor, moveType);
      }
      hostInsert(vnode.anchor, container, anchor);
      return;
    }
    if (type === Static) {
      moveStaticNode(vnode, container, anchor);
      return;
    }
    const needTransition2 = moveType !== 2 && shapeFlag & 1 && transition;
    if (needTransition2) {
      if (moveType === 0) {
        transition.beforeEnter(el);
        hostInsert(el, container, anchor);
        queuePostRenderEffect(() => transition.enter(el), parentSuspense);
      } else {
        const { leave, delayLeave, afterLeave } = transition;
        const remove22 = () => {
          if (vnode.ctx.isUnmounted) {
            hostRemove(el);
          } else {
            hostInsert(el, container, anchor);
          }
        };
        const performLeave = () => {
          leave(el, () => {
            remove22();
            afterLeave && afterLeave();
          });
        };
        if (delayLeave) {
          delayLeave(el, remove22, performLeave);
        } else {
          performLeave();
        }
      }
    } else {
      hostInsert(el, container, anchor);
    }
  };
  const unmount = (vnode, parentComponent, parentSuspense, doRemove = false, optimized = false) => {
    const {
      type,
      props,
      ref: ref3,
      children,
      dynamicChildren,
      shapeFlag,
      patchFlag,
      dirs,
      cacheIndex
    } = vnode;
    if (patchFlag === -2) {
      optimized = false;
    }
    if (ref3 != null) {
      pauseTracking();
      setRef(ref3, null, parentSuspense, vnode, true);
      resetTracking();
    }
    if (cacheIndex != null) {
      parentComponent.renderCache[cacheIndex] = void 0;
    }
    if (shapeFlag & 256) {
      parentComponent.ctx.deactivate(vnode);
      return;
    }
    const shouldInvokeDirs = shapeFlag & 1 && dirs;
    const shouldInvokeVnodeHook = !isAsyncWrapper(vnode);
    let vnodeHook;
    if (shouldInvokeVnodeHook && (vnodeHook = props && props.onVnodeBeforeUnmount)) {
      invokeVNodeHook(vnodeHook, parentComponent, vnode);
    }
    if (shapeFlag & 6) {
      unmountComponent(vnode.component, parentSuspense, doRemove);
    } else {
      if (shapeFlag & 128) {
        vnode.suspense.unmount(parentSuspense, doRemove);
        return;
      }
      if (shouldInvokeDirs) {
        invokeDirectiveHook(vnode, null, parentComponent, "beforeUnmount");
      }
      if (shapeFlag & 64) {
        vnode.type.remove(
          vnode,
          parentComponent,
          parentSuspense,
          internals,
          doRemove
        );
      } else if (dynamicChildren && // #5154
      // when v-once is used inside a block, setBlockTracking(-1) marks the
      // parent block with hasOnce: true
      // so that it doesn't take the fast path during unmount - otherwise
      // components nested in v-once are never unmounted.
      !dynamicChildren.hasOnce && // #1153: fast path should not be taken for non-stable (v-for) fragments
      (type !== Fragment || patchFlag > 0 && patchFlag & 64)) {
        unmountChildren(
          dynamicChildren,
          parentComponent,
          parentSuspense,
          false,
          true
        );
      } else if (type === Fragment && patchFlag & (128 | 256) || !optimized && shapeFlag & 16) {
        unmountChildren(children, parentComponent, parentSuspense);
      }
      if (doRemove) {
        remove2(vnode);
      }
    }
    if (shouldInvokeVnodeHook && (vnodeHook = props && props.onVnodeUnmounted) || shouldInvokeDirs) {
      queuePostRenderEffect(() => {
        vnodeHook && invokeVNodeHook(vnodeHook, parentComponent, vnode);
        shouldInvokeDirs && invokeDirectiveHook(vnode, null, parentComponent, "unmounted");
      }, parentSuspense);
    }
  };
  const remove2 = (vnode) => {
    const { type, el, anchor, transition } = vnode;
    if (type === Fragment) {
      if (!!(process.env.NODE_ENV !== "production") && vnode.patchFlag > 0 && vnode.patchFlag & 2048 && transition && !transition.persisted) {
        vnode.children.forEach((child) => {
          if (child.type === Comment) {
            hostRemove(child.el);
          } else {
            remove2(child);
          }
        });
      } else {
        removeFragment(el, anchor);
      }
      return;
    }
    if (type === Static) {
      removeStaticNode(vnode);
      return;
    }
    const performRemove = () => {
      hostRemove(el);
      if (transition && !transition.persisted && transition.afterLeave) {
        transition.afterLeave();
      }
    };
    if (vnode.shapeFlag & 1 && transition && !transition.persisted) {
      const { leave, delayLeave } = transition;
      const performLeave = () => leave(el, performRemove);
      if (delayLeave) {
        delayLeave(vnode.el, performRemove, performLeave);
      } else {
        performLeave();
      }
    } else {
      performRemove();
    }
  };
  const removeFragment = (cur, end) => {
    let next;
    while (cur !== end) {
      next = hostNextSibling(cur);
      hostRemove(cur);
      cur = next;
    }
    hostRemove(end);
  };
  const unmountComponent = (instance, parentSuspense, doRemove) => {
    if (!!(process.env.NODE_ENV !== "production") && instance.type.__hmrId) {
      unregisterHMR(instance);
    }
    const {
      bum,
      scope,
      job,
      subTree,
      um,
      m,
      a: a2,
      parent,
      slots: { __: slotCacheKeys }
    } = instance;
    invalidateMount(m);
    invalidateMount(a2);
    if (bum) {
      invokeArrayFns(bum);
    }
    if (parent && isArray(slotCacheKeys)) {
      slotCacheKeys.forEach((v) => {
        parent.renderCache[v] = void 0;
      });
    }
    scope.stop();
    if (job) {
      job.flags |= 8;
      unmount(subTree, instance, parentSuspense, doRemove);
    }
    if (um) {
      queuePostRenderEffect(um, parentSuspense);
    }
    queuePostRenderEffect(() => {
      instance.isUnmounted = true;
    }, parentSuspense);
    if (parentSuspense && parentSuspense.pendingBranch && !parentSuspense.isUnmounted && instance.asyncDep && !instance.asyncResolved && instance.suspenseId === parentSuspense.pendingId) {
      parentSuspense.deps--;
      if (parentSuspense.deps === 0) {
        parentSuspense.resolve();
      }
    }
    if (!!(process.env.NODE_ENV !== "production") || false) {
      devtoolsComponentRemoved(instance);
    }
  };
  const unmountChildren = (children, parentComponent, parentSuspense, doRemove = false, optimized = false, start = 0) => {
    for (let i2 = start; i2 < children.length; i2++) {
      unmount(children[i2], parentComponent, parentSuspense, doRemove, optimized);
    }
  };
  const getNextHostNode = (vnode) => {
    if (vnode.shapeFlag & 6) {
      return getNextHostNode(vnode.component.subTree);
    }
    if (vnode.shapeFlag & 128) {
      return vnode.suspense.next();
    }
    const el = hostNextSibling(vnode.anchor || vnode.el);
    const teleportEnd = el && el[TeleportEndKey];
    return teleportEnd ? hostNextSibling(teleportEnd) : el;
  };
  let isFlushing = false;
  const render2 = (vnode, container, namespace) => {
    if (vnode == null) {
      if (container._vnode) {
        unmount(container._vnode, null, null, true);
      }
    } else {
      patch(
        container._vnode || null,
        vnode,
        container,
        null,
        null,
        null,
        namespace
      );
    }
    container._vnode = vnode;
    if (!isFlushing) {
      isFlushing = true;
      flushPreFlushCbs();
      flushPostFlushCbs();
      isFlushing = false;
    }
  };
  const internals = {
    p: patch,
    um: unmount,
    m: move,
    r: remove2,
    mt: mountComponent,
    mc: mountChildren,
    pc: patchChildren,
    pbc: patchBlockChildren,
    n: getNextHostNode,
    o: options
  };
  let hydrate;
  return {
    render: render2,
    hydrate,
    createApp: createAppAPI(render2)
  };
}
function resolveChildrenNamespace({ type, props }, currentNamespace) {
  return currentNamespace === "svg" && type === "foreignObject" || currentNamespace === "mathml" && type === "annotation-xml" && props && props.encoding && props.encoding.includes("html") ? void 0 : currentNamespace;
}
function toggleRecurse({ effect: effect2, job }, allowed) {
  if (allowed) {
    effect2.flags |= 32;
    job.flags |= 4;
  } else {
    effect2.flags &= -33;
    job.flags &= -5;
  }
}
function needTransition(parentSuspense, transition) {
  return (!parentSuspense || parentSuspense && !parentSuspense.pendingBranch) && transition && !transition.persisted;
}
function traverseStaticChildren(n1, n2, shallow = false) {
  const ch1 = n1.children;
  const ch2 = n2.children;
  if (isArray(ch1) && isArray(ch2)) {
    for (let i2 = 0; i2 < ch1.length; i2++) {
      const c1 = ch1[i2];
      let c2 = ch2[i2];
      if (c2.shapeFlag & 1 && !c2.dynamicChildren) {
        if (c2.patchFlag <= 0 || c2.patchFlag === 32) {
          c2 = ch2[i2] = cloneIfMounted(ch2[i2]);
          c2.el = c1.el;
        }
        if (!shallow && c2.patchFlag !== -2)
          traverseStaticChildren(c1, c2);
      }
      if (c2.type === Text) {
        c2.el = c1.el;
      }
      if (c2.type === Comment && !c2.el) {
        c2.el = c1.el;
      }
      if (!!(process.env.NODE_ENV !== "production")) {
        c2.el && (c2.el.__vnode = c2);
      }
    }
  }
}
function getSequence(arr) {
  const p = arr.slice();
  const result = [0];
  let i2, j, u, v, c2;
  const len = arr.length;
  for (i2 = 0; i2 < len; i2++) {
    const arrI = arr[i2];
    if (arrI !== 0) {
      j = result[result.length - 1];
      if (arr[j] < arrI) {
        p[i2] = j;
        result.push(i2);
        continue;
      }
      u = 0;
      v = result.length - 1;
      while (u < v) {
        c2 = u + v >> 1;
        if (arr[result[c2]] < arrI) {
          u = c2 + 1;
        } else {
          v = c2;
        }
      }
      if (arrI < arr[result[u]]) {
        if (u > 0) {
          p[i2] = result[u - 1];
        }
        result[u] = i2;
      }
    }
  }
  u = result.length;
  v = result[u - 1];
  while (u-- > 0) {
    result[u] = v;
    v = p[v];
  }
  return result;
}
function locateNonHydratedAsyncRoot(instance) {
  const subComponent = instance.subTree.component;
  if (subComponent) {
    if (subComponent.asyncDep && !subComponent.asyncResolved) {
      return subComponent;
    } else {
      return locateNonHydratedAsyncRoot(subComponent);
    }
  }
}
function invalidateMount(hooks) {
  if (hooks) {
    for (let i2 = 0; i2 < hooks.length; i2++)
      hooks[i2].flags |= 8;
  }
}
const ssrContextKey = Symbol.for("v-scx");
const useSSRContext = () => {
  {
    const ctx = inject(ssrContextKey);
    if (!ctx) {
      !!(process.env.NODE_ENV !== "production") && warn$1(
        `Server rendering context not provided. Make sure to only call useSSRContext() conditionally in the server build.`
      );
    }
    return ctx;
  }
};
function watch(source, cb, options) {
  if (!!(process.env.NODE_ENV !== "production") && !isFunction(cb)) {
    warn$1(
      `\`watch(fn, options?)\` signature has been moved to a separate API. Use \`watchEffect(fn, options?)\` instead. \`watch\` now only supports \`watch(source, cb, options?) signature.`
    );
  }
  return doWatch(source, cb, options);
}
function doWatch(source, cb, options = EMPTY_OBJ) {
  const { immediate, deep, flush, once } = options;
  if (!!(process.env.NODE_ENV !== "production") && !cb) {
    if (immediate !== void 0) {
      warn$1(
        `watch() "immediate" option is only respected when using the watch(source, callback, options?) signature.`
      );
    }
    if (deep !== void 0) {
      warn$1(
        `watch() "deep" option is only respected when using the watch(source, callback, options?) signature.`
      );
    }
    if (once !== void 0) {
      warn$1(
        `watch() "once" option is only respected when using the watch(source, callback, options?) signature.`
      );
    }
  }
  const baseWatchOptions = extend({}, options);
  if (!!(process.env.NODE_ENV !== "production")) baseWatchOptions.onWarn = warn$1;
  const runsImmediately = cb && immediate || !cb && flush !== "post";
  let ssrCleanup;
  if (isInSSRComponentSetup) {
    if (flush === "sync") {
      const ctx = useSSRContext();
      ssrCleanup = ctx.__watcherHandles || (ctx.__watcherHandles = []);
    } else if (!runsImmediately) {
      const watchStopHandle = () => {
      };
      watchStopHandle.stop = NOOP;
      watchStopHandle.resume = NOOP;
      watchStopHandle.pause = NOOP;
      return watchStopHandle;
    }
  }
  const instance = currentInstance;
  baseWatchOptions.call = (fn, type, args) => callWithAsyncErrorHandling(fn, instance, type, args);
  let isPre = false;
  if (flush === "post") {
    baseWatchOptions.scheduler = (job) => {
      queuePostRenderEffect(job, instance && instance.suspense);
    };
  } else if (flush !== "sync") {
    isPre = true;
    baseWatchOptions.scheduler = (job, isFirstRun) => {
      if (isFirstRun) {
        job();
      } else {
        queueJob(job);
      }
    };
  }
  baseWatchOptions.augmentJob = (job) => {
    if (cb) {
      job.flags |= 4;
    }
    if (isPre) {
      job.flags |= 2;
      if (instance) {
        job.id = instance.uid;
        job.i = instance;
      }
    }
  };
  const watchHandle = watch$1(source, cb, baseWatchOptions);
  if (isInSSRComponentSetup) {
    if (ssrCleanup) {
      ssrCleanup.push(watchHandle);
    } else if (runsImmediately) {
      watchHandle();
    }
  }
  return watchHandle;
}
function instanceWatch(source, value, options) {
  const publicThis = this.proxy;
  const getter = isString(source) ? source.includes(".") ? createPathGetter(publicThis, source) : () => publicThis[source] : source.bind(publicThis, publicThis);
  let cb;
  if (isFunction(value)) {
    cb = value;
  } else {
    cb = value.handler;
    options = value;
  }
  const reset = setCurrentInstance(this);
  const res = doWatch(getter, cb.bind(publicThis), options);
  reset();
  return res;
}
function createPathGetter(ctx, path) {
  const segments = path.split(".");
  return () => {
    let cur = ctx;
    for (let i2 = 0; i2 < segments.length && cur; i2++) {
      cur = cur[segments[i2]];
    }
    return cur;
  };
}
const getModelModifiers = (props, modelName) => {
  return modelName === "modelValue" || modelName === "model-value" ? props.modelModifiers : props[`${modelName}Modifiers`] || props[`${camelize(modelName)}Modifiers`] || props[`${hyphenate(modelName)}Modifiers`];
};
function emit(instance, event, ...rawArgs) {
  if (instance.isUnmounted) return;
  const props = instance.vnode.props || EMPTY_OBJ;
  if (!!(process.env.NODE_ENV !== "production")) {
    const {
      emitsOptions,
      propsOptions: [propsOptions]
    } = instance;
    if (emitsOptions) {
      if (!(event in emitsOptions) && true) {
        if (!propsOptions || !(toHandlerKey(camelize(event)) in propsOptions)) {
          warn$1(
            `Component emitted event "${event}" but it is neither declared in the emits option nor as an "${toHandlerKey(camelize(event))}" prop.`
          );
        }
      } else {
        const validator = emitsOptions[event];
        if (isFunction(validator)) {
          const isValid = validator(...rawArgs);
          if (!isValid) {
            warn$1(
              `Invalid event arguments: event validation failed for event "${event}".`
            );
          }
        }
      }
    }
  }
  let args = rawArgs;
  const isModelListener2 = event.startsWith("update:");
  const modifiers = isModelListener2 && getModelModifiers(props, event.slice(7));
  if (modifiers) {
    if (modifiers.trim) {
      args = rawArgs.map((a2) => isString(a2) ? a2.trim() : a2);
    }
    if (modifiers.number) {
      args = rawArgs.map(looseToNumber);
    }
  }
  if (!!(process.env.NODE_ENV !== "production") || false) {
    devtoolsComponentEmit(instance, event, args);
  }
  if (!!(process.env.NODE_ENV !== "production")) {
    const lowerCaseEvent = event.toLowerCase();
    if (lowerCaseEvent !== event && props[toHandlerKey(lowerCaseEvent)]) {
      warn$1(
        `Event "${lowerCaseEvent}" is emitted in component ${formatComponentName(
          instance,
          instance.type
        )} but the handler is registered for "${event}". Note that HTML attributes are case-insensitive and you cannot use v-on to listen to camelCase events when using in-DOM templates. You should probably use "${hyphenate(
          event
        )}" instead of "${event}".`
      );
    }
  }
  let handlerName;
  let handler = props[handlerName = toHandlerKey(event)] || // also try camelCase event handler (#2249)
  props[handlerName = toHandlerKey(camelize(event))];
  if (!handler && isModelListener2) {
    handler = props[handlerName = toHandlerKey(hyphenate(event))];
  }
  if (handler) {
    callWithAsyncErrorHandling(
      handler,
      instance,
      6,
      args
    );
  }
  const onceHandler = props[handlerName + `Once`];
  if (onceHandler) {
    if (!instance.emitted) {
      instance.emitted = {};
    } else if (instance.emitted[handlerName]) {
      return;
    }
    instance.emitted[handlerName] = true;
    callWithAsyncErrorHandling(
      onceHandler,
      instance,
      6,
      args
    );
  }
}
function normalizeEmitsOptions(comp, appContext, asMixin = false) {
  const cache = appContext.emitsCache;
  const cached = cache.get(comp);
  if (cached !== void 0) {
    return cached;
  }
  const raw = comp.emits;
  let normalized = {};
  let hasExtends = false;
  if (!isFunction(comp)) {
    const extendEmits = (raw2) => {
      const normalizedFromExtend = normalizeEmitsOptions(raw2, appContext, true);
      if (normalizedFromExtend) {
        hasExtends = true;
        extend(normalized, normalizedFromExtend);
      }
    };
    if (!asMixin && appContext.mixins.length) {
      appContext.mixins.forEach(extendEmits);
    }
    if (comp.extends) {
      extendEmits(comp.extends);
    }
    if (comp.mixins) {
      comp.mixins.forEach(extendEmits);
    }
  }
  if (!raw && !hasExtends) {
    if (isObject(comp)) {
      cache.set(comp, null);
    }
    return null;
  }
  if (isArray(raw)) {
    raw.forEach((key) => normalized[key] = null);
  } else {
    extend(normalized, raw);
  }
  if (isObject(comp)) {
    cache.set(comp, normalized);
  }
  return normalized;
}
function isEmitListener(options, key) {
  if (!options || !isOn(key)) {
    return false;
  }
  key = key.slice(2).replace(/Once$/, "");
  return hasOwn(options, key[0].toLowerCase() + key.slice(1)) || hasOwn(options, hyphenate(key)) || hasOwn(options, key);
}
let accessedAttrs = false;
function markAttrsAccessed() {
  accessedAttrs = true;
}
function renderComponentRoot(instance) {
  const {
    type: Component,
    vnode,
    proxy,
    withProxy,
    propsOptions: [propsOptions],
    slots,
    attrs,
    emit: emit2,
    render: render2,
    renderCache,
    props,
    data,
    setupState,
    ctx,
    inheritAttrs
  } = instance;
  const prev = setCurrentRenderingInstance(instance);
  let result;
  let fallthroughAttrs;
  if (!!(process.env.NODE_ENV !== "production")) {
    accessedAttrs = false;
  }
  try {
    if (vnode.shapeFlag & 4) {
      const proxyToUse = withProxy || proxy;
      const thisProxy = !!(process.env.NODE_ENV !== "production") && setupState.__isScriptSetup ? new Proxy(proxyToUse, {
        get(target, key, receiver) {
          warn$1(
            `Property '${String(
              key
            )}' was accessed via 'this'. Avoid using 'this' in templates.`
          );
          return Reflect.get(target, key, receiver);
        }
      }) : proxyToUse;
      result = normalizeVNode(
        render2.call(
          thisProxy,
          proxyToUse,
          renderCache,
          !!(process.env.NODE_ENV !== "production") ? shallowReadonly(props) : props,
          setupState,
          data,
          ctx
        )
      );
      fallthroughAttrs = attrs;
    } else {
      const render22 = Component;
      if (!!(process.env.NODE_ENV !== "production") && attrs === props) {
        markAttrsAccessed();
      }
      result = normalizeVNode(
        render22.length > 1 ? render22(
          !!(process.env.NODE_ENV !== "production") ? shallowReadonly(props) : props,
          !!(process.env.NODE_ENV !== "production") ? {
            get attrs() {
              markAttrsAccessed();
              return shallowReadonly(attrs);
            },
            slots,
            emit: emit2
          } : { attrs, slots, emit: emit2 }
        ) : render22(
          !!(process.env.NODE_ENV !== "production") ? shallowReadonly(props) : props,
          null
        )
      );
      fallthroughAttrs = Component.props ? attrs : getFunctionalFallthrough(attrs);
    }
  } catch (err) {
    blockStack.length = 0;
    handleError(err, instance, 1);
    result = createVNode(Comment);
  }
  let root = result;
  let setRoot = void 0;
  if (!!(process.env.NODE_ENV !== "production") && result.patchFlag > 0 && result.patchFlag & 2048) {
    [root, setRoot] = getChildRoot(result);
  }
  if (fallthroughAttrs && inheritAttrs !== false) {
    const keys = Object.keys(fallthroughAttrs);
    const { shapeFlag } = root;
    if (keys.length) {
      if (shapeFlag & (1 | 6)) {
        if (propsOptions && keys.some(isModelListener)) {
          fallthroughAttrs = filterModelListeners(
            fallthroughAttrs,
            propsOptions
          );
        }
        root = cloneVNode(root, fallthroughAttrs, false, true);
      } else if (!!(process.env.NODE_ENV !== "production") && !accessedAttrs && root.type !== Comment) {
        const allAttrs = Object.keys(attrs);
        const eventAttrs = [];
        const extraAttrs = [];
        for (let i2 = 0, l = allAttrs.length; i2 < l; i2++) {
          const key = allAttrs[i2];
          if (isOn(key)) {
            if (!isModelListener(key)) {
              eventAttrs.push(key[2].toLowerCase() + key.slice(3));
            }
          } else {
            extraAttrs.push(key);
          }
        }
        if (extraAttrs.length) {
          warn$1(
            `Extraneous non-props attributes (${extraAttrs.join(", ")}) were passed to component but could not be automatically inherited because component renders fragment or text or teleport root nodes.`
          );
        }
        if (eventAttrs.length) {
          warn$1(
            `Extraneous non-emits event listeners (${eventAttrs.join(", ")}) were passed to component but could not be automatically inherited because component renders fragment or text root nodes. If the listener is intended to be a component custom event listener only, declare it using the "emits" option.`
          );
        }
      }
    }
  }
  if (vnode.dirs) {
    if (!!(process.env.NODE_ENV !== "production") && !isElementRoot(root)) {
      warn$1(
        `Runtime directive used on component with non-element root node. The directives will not function as intended.`
      );
    }
    root = cloneVNode(root, null, false, true);
    root.dirs = root.dirs ? root.dirs.concat(vnode.dirs) : vnode.dirs;
  }
  if (vnode.transition) {
    if (!!(process.env.NODE_ENV !== "production") && !isElementRoot(root)) {
      warn$1(
        `Component inside <Transition> renders non-element root node that cannot be animated.`
      );
    }
    setTransitionHooks(root, vnode.transition);
  }
  if (!!(process.env.NODE_ENV !== "production") && setRoot) {
    setRoot(root);
  } else {
    result = root;
  }
  setCurrentRenderingInstance(prev);
  return result;
}
const getChildRoot = (vnode) => {
  const rawChildren = vnode.children;
  const dynamicChildren = vnode.dynamicChildren;
  const childRoot = filterSingleRoot(rawChildren, false);
  if (!childRoot) {
    return [vnode, void 0];
  } else if (!!(process.env.NODE_ENV !== "production") && childRoot.patchFlag > 0 && childRoot.patchFlag & 2048) {
    return getChildRoot(childRoot);
  }
  const index = rawChildren.indexOf(childRoot);
  const dynamicIndex = dynamicChildren ? dynamicChildren.indexOf(childRoot) : -1;
  const setRoot = (updatedRoot) => {
    rawChildren[index] = updatedRoot;
    if (dynamicChildren) {
      if (dynamicIndex > -1) {
        dynamicChildren[dynamicIndex] = updatedRoot;
      } else if (updatedRoot.patchFlag > 0) {
        vnode.dynamicChildren = [...dynamicChildren, updatedRoot];
      }
    }
  };
  return [normalizeVNode(childRoot), setRoot];
};
function filterSingleRoot(children, recurse = true) {
  let singleRoot;
  for (let i2 = 0; i2 < children.length; i2++) {
    const child = children[i2];
    if (isVNode(child)) {
      if (child.type !== Comment || child.children === "v-if") {
        if (singleRoot) {
          return;
        } else {
          singleRoot = child;
          if (!!(process.env.NODE_ENV !== "production") && recurse && singleRoot.patchFlag > 0 && singleRoot.patchFlag & 2048) {
            return filterSingleRoot(singleRoot.children);
          }
        }
      }
    } else {
      return;
    }
  }
  return singleRoot;
}
const getFunctionalFallthrough = (attrs) => {
  let res;
  for (const key in attrs) {
    if (key === "class" || key === "style" || isOn(key)) {
      (res || (res = {}))[key] = attrs[key];
    }
  }
  return res;
};
const filterModelListeners = (attrs, props) => {
  const res = {};
  for (const key in attrs) {
    if (!isModelListener(key) || !(key.slice(9) in props)) {
      res[key] = attrs[key];
    }
  }
  return res;
};
const isElementRoot = (vnode) => {
  return vnode.shapeFlag & (6 | 1) || vnode.type === Comment;
};
function shouldUpdateComponent(prevVNode, nextVNode, optimized) {
  const { props: prevProps, children: prevChildren, component } = prevVNode;
  const { props: nextProps, children: nextChildren, patchFlag } = nextVNode;
  const emits = component.emitsOptions;
  if (!!(process.env.NODE_ENV !== "production") && (prevChildren || nextChildren) && isHmrUpdating) {
    return true;
  }
  if (nextVNode.dirs || nextVNode.transition) {
    return true;
  }
  if (optimized && patchFlag >= 0) {
    if (patchFlag & 1024) {
      return true;
    }
    if (patchFlag & 16) {
      if (!prevProps) {
        return !!nextProps;
      }
      return hasPropsChanged(prevProps, nextProps, emits);
    } else if (patchFlag & 8) {
      const dynamicProps = nextVNode.dynamicProps;
      for (let i2 = 0; i2 < dynamicProps.length; i2++) {
        const key = dynamicProps[i2];
        if (nextProps[key] !== prevProps[key] && !isEmitListener(emits, key)) {
          return true;
        }
      }
    }
  } else {
    if (prevChildren || nextChildren) {
      if (!nextChildren || !nextChildren.$stable) {
        return true;
      }
    }
    if (prevProps === nextProps) {
      return false;
    }
    if (!prevProps) {
      return !!nextProps;
    }
    if (!nextProps) {
      return true;
    }
    return hasPropsChanged(prevProps, nextProps, emits);
  }
  return false;
}
function hasPropsChanged(prevProps, nextProps, emitsOptions) {
  const nextKeys = Object.keys(nextProps);
  if (nextKeys.length !== Object.keys(prevProps).length) {
    return true;
  }
  for (let i2 = 0; i2 < nextKeys.length; i2++) {
    const key = nextKeys[i2];
    if (nextProps[key] !== prevProps[key] && !isEmitListener(emitsOptions, key)) {
      return true;
    }
  }
  return false;
}
function updateHOCHostEl({ vnode, parent }, el) {
  while (parent) {
    const root = parent.subTree;
    if (root.suspense && root.suspense.activeBranch === vnode) {
      root.el = vnode.el;
    }
    if (root === vnode) {
      (vnode = parent.vnode).el = el;
      parent = parent.parent;
    } else {
      break;
    }
  }
}
const isSuspense = (type) => type.__isSuspense;
function queueEffectWithSuspense(fn, suspense) {
  if (suspense && suspense.pendingBranch) {
    if (isArray(fn)) {
      suspense.effects.push(...fn);
    } else {
      suspense.effects.push(fn);
    }
  } else {
    queuePostFlushCb(fn);
  }
}
const Fragment = Symbol.for("v-fgt");
const Text = Symbol.for("v-txt");
const Comment = Symbol.for("v-cmt");
const Static = Symbol.for("v-stc");
const blockStack = [];
let currentBlock = null;
function openBlock(disableTracking = false) {
  blockStack.push(currentBlock = disableTracking ? null : []);
}
function closeBlock() {
  blockStack.pop();
  currentBlock = blockStack[blockStack.length - 1] || null;
}
let isBlockTreeEnabled = 1;
function setBlockTracking(value, inVOnce = false) {
  isBlockTreeEnabled += value;
  if (value < 0 && currentBlock && inVOnce) {
    currentBlock.hasOnce = true;
  }
}
function setupBlock(vnode) {
  vnode.dynamicChildren = isBlockTreeEnabled > 0 ? currentBlock || EMPTY_ARR : null;
  closeBlock();
  if (isBlockTreeEnabled > 0 && currentBlock) {
    currentBlock.push(vnode);
  }
  return vnode;
}
function createElementBlock(type, props, children, patchFlag, dynamicProps, shapeFlag) {
  return setupBlock(
    createBaseVNode(
      type,
      props,
      children,
      patchFlag,
      dynamicProps,
      shapeFlag,
      true
    )
  );
}
function createBlock(type, props, children, patchFlag, dynamicProps) {
  return setupBlock(
    createVNode(
      type,
      props,
      children,
      patchFlag,
      dynamicProps,
      true
    )
  );
}
function isVNode(value) {
  return value ? value.__v_isVNode === true : false;
}
function isSameVNodeType(n1, n2) {
  if (!!(process.env.NODE_ENV !== "production") && n2.shapeFlag & 6 && n1.component) {
    const dirtyInstances = hmrDirtyComponents.get(n2.type);
    if (dirtyInstances && dirtyInstances.has(n1.component)) {
      n1.shapeFlag &= -257;
      n2.shapeFlag &= -513;
      return false;
    }
  }
  return n1.type === n2.type && n1.key === n2.key;
}
const createVNodeWithArgsTransform = (...args) => {
  return _createVNode(
    ...args
  );
};
const normalizeKey = ({ key }) => key != null ? key : null;
const normalizeRef = ({
  ref: ref3,
  ref_key,
  ref_for
}) => {
  if (typeof ref3 === "number") {
    ref3 = "" + ref3;
  }
  return ref3 != null ? isString(ref3) || isRef(ref3) || isFunction(ref3) ? { i: currentRenderingInstance, r: ref3, k: ref_key, f: !!ref_for } : ref3 : null;
};
function createBaseVNode(type, props = null, children = null, patchFlag = 0, dynamicProps = null, shapeFlag = type === Fragment ? 0 : 1, isBlockNode = false, needFullChildrenNormalization = false) {
  const vnode = {
    __v_isVNode: true,
    __v_skip: true,
    type,
    props,
    key: props && normalizeKey(props),
    ref: props && normalizeRef(props),
    scopeId: currentScopeId,
    slotScopeIds: null,
    children,
    component: null,
    suspense: null,
    ssContent: null,
    ssFallback: null,
    dirs: null,
    transition: null,
    el: null,
    anchor: null,
    target: null,
    targetStart: null,
    targetAnchor: null,
    staticCount: 0,
    shapeFlag,
    patchFlag,
    dynamicProps,
    dynamicChildren: null,
    appContext: null,
    ctx: currentRenderingInstance
  };
  if (needFullChildrenNormalization) {
    normalizeChildren(vnode, children);
    if (shapeFlag & 128) {
      type.normalize(vnode);
    }
  } else if (children) {
    vnode.shapeFlag |= isString(children) ? 8 : 16;
  }
  if (!!(process.env.NODE_ENV !== "production") && vnode.key !== vnode.key) {
    warn$1(`VNode created with invalid key (NaN). VNode type:`, vnode.type);
  }
  if (isBlockTreeEnabled > 0 && // avoid a block node from tracking itself
  !isBlockNode && // has current parent block
  currentBlock && // presence of a patch flag indicates this node needs patching on updates.
  // component nodes also should always be patched, because even if the
  // component doesn't need to update, it needs to persist the instance on to
  // the next vnode so that it can be properly unmounted later.
  (vnode.patchFlag > 0 || shapeFlag & 6) && // the EVENTS flag is only for hydration and if it is the only flag, the
  // vnode should not be considered dynamic due to handler caching.
  vnode.patchFlag !== 32) {
    currentBlock.push(vnode);
  }
  return vnode;
}
const createVNode = !!(process.env.NODE_ENV !== "production") ? createVNodeWithArgsTransform : _createVNode;
function _createVNode(type, props = null, children = null, patchFlag = 0, dynamicProps = null, isBlockNode = false) {
  if (!type || type === NULL_DYNAMIC_COMPONENT) {
    if (!!(process.env.NODE_ENV !== "production") && !type) {
      warn$1(`Invalid vnode type when creating vnode: ${type}.`);
    }
    type = Comment;
  }
  if (isVNode(type)) {
    const cloned = cloneVNode(
      type,
      props,
      true
      /* mergeRef: true */
    );
    if (children) {
      normalizeChildren(cloned, children);
    }
    if (isBlockTreeEnabled > 0 && !isBlockNode && currentBlock) {
      if (cloned.shapeFlag & 6) {
        currentBlock[currentBlock.indexOf(type)] = cloned;
      } else {
        currentBlock.push(cloned);
      }
    }
    cloned.patchFlag = -2;
    return cloned;
  }
  if (isClassComponent(type)) {
    type = type.__vccOpts;
  }
  if (props) {
    props = guardReactiveProps(props);
    let { class: klass, style } = props;
    if (klass && !isString(klass)) {
      props.class = normalizeClass(klass);
    }
    if (isObject(style)) {
      if (isProxy(style) && !isArray(style)) {
        style = extend({}, style);
      }
      props.style = normalizeStyle(style);
    }
  }
  const shapeFlag = isString(type) ? 1 : isSuspense(type) ? 128 : isTeleport(type) ? 64 : isObject(type) ? 4 : isFunction(type) ? 2 : 0;
  if (!!(process.env.NODE_ENV !== "production") && shapeFlag & 4 && isProxy(type)) {
    type = toRaw(type);
    warn$1(
      `Vue received a Component that was made a reactive object. This can lead to unnecessary performance overhead and should be avoided by marking the component with \`markRaw\` or using \`shallowRef\` instead of \`ref\`.`,
      `
Component that was made reactive: `,
      type
    );
  }
  return createBaseVNode(
    type,
    props,
    children,
    patchFlag,
    dynamicProps,
    shapeFlag,
    isBlockNode,
    true
  );
}
function guardReactiveProps(props) {
  if (!props) return null;
  return isProxy(props) || isInternalObject(props) ? extend({}, props) : props;
}
function cloneVNode(vnode, extraProps, mergeRef = false, cloneTransition = false) {
  const { props, ref: ref3, patchFlag, children, transition } = vnode;
  const mergedProps = extraProps ? mergeProps(props || {}, extraProps) : props;
  const cloned = {
    __v_isVNode: true,
    __v_skip: true,
    type: vnode.type,
    props: mergedProps,
    key: mergedProps && normalizeKey(mergedProps),
    ref: extraProps && extraProps.ref ? (
      // #2078 in the case of <component :is="vnode" ref="extra"/>
      // if the vnode itself already has a ref, cloneVNode will need to merge
      // the refs so the single vnode can be set on multiple refs
      mergeRef && ref3 ? isArray(ref3) ? ref3.concat(normalizeRef(extraProps)) : [ref3, normalizeRef(extraProps)] : normalizeRef(extraProps)
    ) : ref3,
    scopeId: vnode.scopeId,
    slotScopeIds: vnode.slotScopeIds,
    children: !!(process.env.NODE_ENV !== "production") && patchFlag === -1 && isArray(children) ? children.map(deepCloneVNode) : children,
    target: vnode.target,
    targetStart: vnode.targetStart,
    targetAnchor: vnode.targetAnchor,
    staticCount: vnode.staticCount,
    shapeFlag: vnode.shapeFlag,
    // if the vnode is cloned with extra props, we can no longer assume its
    // existing patch flag to be reliable and need to add the FULL_PROPS flag.
    // note: preserve flag for fragments since they use the flag for children
    // fast paths only.
    patchFlag: extraProps && vnode.type !== Fragment ? patchFlag === -1 ? 16 : patchFlag | 16 : patchFlag,
    dynamicProps: vnode.dynamicProps,
    dynamicChildren: vnode.dynamicChildren,
    appContext: vnode.appContext,
    dirs: vnode.dirs,
    transition,
    // These should technically only be non-null on mounted VNodes. However,
    // they *should* be copied for kept-alive vnodes. So we just always copy
    // them since them being non-null during a mount doesn't affect the logic as
    // they will simply be overwritten.
    component: vnode.component,
    suspense: vnode.suspense,
    ssContent: vnode.ssContent && cloneVNode(vnode.ssContent),
    ssFallback: vnode.ssFallback && cloneVNode(vnode.ssFallback),
    el: vnode.el,
    anchor: vnode.anchor,
    ctx: vnode.ctx,
    ce: vnode.ce
  };
  if (transition && cloneTransition) {
    setTransitionHooks(
      cloned,
      transition.clone(cloned)
    );
  }
  return cloned;
}
function deepCloneVNode(vnode) {
  const cloned = cloneVNode(vnode);
  if (isArray(vnode.children)) {
    cloned.children = vnode.children.map(deepCloneVNode);
  }
  return cloned;
}
function createTextVNode(text = " ", flag = 0) {
  return createVNode(Text, null, text, flag);
}
function createCommentVNode(text = "", asBlock = false) {
  return asBlock ? (openBlock(), createBlock(Comment, null, text)) : createVNode(Comment, null, text);
}
function normalizeVNode(child) {
  if (child == null || typeof child === "boolean") {
    return createVNode(Comment);
  } else if (isArray(child)) {
    return createVNode(
      Fragment,
      null,
      // #3666, avoid reference pollution when reusing vnode
      child.slice()
    );
  } else if (isVNode(child)) {
    return cloneIfMounted(child);
  } else {
    return createVNode(Text, null, String(child));
  }
}
function cloneIfMounted(child) {
  return child.el === null && child.patchFlag !== -1 || child.memo ? child : cloneVNode(child);
}
function normalizeChildren(vnode, children) {
  let type = 0;
  const { shapeFlag } = vnode;
  if (children == null) {
    children = null;
  } else if (isArray(children)) {
    type = 16;
  } else if (typeof children === "object") {
    if (shapeFlag & (1 | 64)) {
      const slot = children.default;
      if (slot) {
        slot._c && (slot._d = false);
        normalizeChildren(vnode, slot());
        slot._c && (slot._d = true);
      }
      return;
    } else {
      type = 32;
      const slotFlag = children._;
      if (!slotFlag && !isInternalObject(children)) {
        children._ctx = currentRenderingInstance;
      } else if (slotFlag === 3 && currentRenderingInstance) {
        if (currentRenderingInstance.slots._ === 1) {
          children._ = 1;
        } else {
          children._ = 2;
          vnode.patchFlag |= 1024;
        }
      }
    }
  } else if (isFunction(children)) {
    children = { default: children, _ctx: currentRenderingInstance };
    type = 32;
  } else {
    children = String(children);
    if (shapeFlag & 64) {
      type = 16;
      children = [createTextVNode(children)];
    } else {
      type = 8;
    }
  }
  vnode.children = children;
  vnode.shapeFlag |= type;
}
function mergeProps(...args) {
  const ret = {};
  for (let i2 = 0; i2 < args.length; i2++) {
    const toMerge = args[i2];
    for (const key in toMerge) {
      if (key === "class") {
        if (ret.class !== toMerge.class) {
          ret.class = normalizeClass([ret.class, toMerge.class]);
        }
      } else if (key === "style") {
        ret.style = normalizeStyle([ret.style, toMerge.style]);
      } else if (isOn(key)) {
        const existing = ret[key];
        const incoming = toMerge[key];
        if (incoming && existing !== incoming && !(isArray(existing) && existing.includes(incoming))) {
          ret[key] = existing ? [].concat(existing, incoming) : incoming;
        }
      } else if (key !== "") {
        ret[key] = toMerge[key];
      }
    }
  }
  return ret;
}
function invokeVNodeHook(hook, instance, vnode, prevVNode = null) {
  callWithAsyncErrorHandling(hook, instance, 7, [
    vnode,
    prevVNode
  ]);
}
const emptyAppContext = createAppContext();
let uid = 0;
function createComponentInstance(vnode, parent, suspense) {
  const type = vnode.type;
  const appContext = (parent ? parent.appContext : vnode.appContext) || emptyAppContext;
  const instance = {
    uid: uid++,
    vnode,
    type,
    parent,
    appContext,
    root: null,
    // to be immediately set
    next: null,
    subTree: null,
    // will be set synchronously right after creation
    effect: null,
    update: null,
    // will be set synchronously right after creation
    job: null,
    scope: new EffectScope(
      true
      /* detached */
    ),
    render: null,
    proxy: null,
    exposed: null,
    exposeProxy: null,
    withProxy: null,
    provides: parent ? parent.provides : Object.create(appContext.provides),
    ids: parent ? parent.ids : ["", 0, 0],
    accessCache: null,
    renderCache: [],
    // local resolved assets
    components: null,
    directives: null,
    // resolved props and emits options
    propsOptions: normalizePropsOptions(type, appContext),
    emitsOptions: normalizeEmitsOptions(type, appContext),
    // emit
    emit: null,
    // to be set immediately
    emitted: null,
    // props default value
    propsDefaults: EMPTY_OBJ,
    // inheritAttrs
    inheritAttrs: type.inheritAttrs,
    // state
    ctx: EMPTY_OBJ,
    data: EMPTY_OBJ,
    props: EMPTY_OBJ,
    attrs: EMPTY_OBJ,
    slots: EMPTY_OBJ,
    refs: EMPTY_OBJ,
    setupState: EMPTY_OBJ,
    setupContext: null,
    // suspense related
    suspense,
    suspenseId: suspense ? suspense.pendingId : 0,
    asyncDep: null,
    asyncResolved: false,
    // lifecycle hooks
    // not using enums here because it results in computed properties
    isMounted: false,
    isUnmounted: false,
    isDeactivated: false,
    bc: null,
    c: null,
    bm: null,
    m: null,
    bu: null,
    u: null,
    um: null,
    bum: null,
    da: null,
    a: null,
    rtg: null,
    rtc: null,
    ec: null,
    sp: null
  };
  if (!!(process.env.NODE_ENV !== "production")) {
    instance.ctx = createDevRenderContext(instance);
  } else {
    instance.ctx = { _: instance };
  }
  instance.root = parent ? parent.root : instance;
  instance.emit = emit.bind(null, instance);
  if (vnode.ce) {
    vnode.ce(instance);
  }
  return instance;
}
let currentInstance = null;
const getCurrentInstance = () => currentInstance || currentRenderingInstance;
let internalSetCurrentInstance;
let setInSSRSetupState;
{
  const g = getGlobalThis();
  const registerGlobalSetter = (key, setter) => {
    let setters;
    if (!(setters = g[key])) setters = g[key] = [];
    setters.push(setter);
    return (v) => {
      if (setters.length > 1) setters.forEach((set) => set(v));
      else setters[0](v);
    };
  };
  internalSetCurrentInstance = registerGlobalSetter(
    `__VUE_INSTANCE_SETTERS__`,
    (v) => currentInstance = v
  );
  setInSSRSetupState = registerGlobalSetter(
    `__VUE_SSR_SETTERS__`,
    (v) => isInSSRComponentSetup = v
  );
}
const setCurrentInstance = (instance) => {
  const prev = currentInstance;
  internalSetCurrentInstance(instance);
  instance.scope.on();
  return () => {
    instance.scope.off();
    internalSetCurrentInstance(prev);
  };
};
const unsetCurrentInstance = () => {
  currentInstance && currentInstance.scope.off();
  internalSetCurrentInstance(null);
};
const isBuiltInTag = /* @__PURE__ */ makeMap("slot,component");
function validateComponentName(name, { isNativeTag }) {
  if (isBuiltInTag(name) || isNativeTag(name)) {
    warn$1(
      "Do not use built-in or reserved HTML elements as component id: " + name
    );
  }
}
function isStatefulComponent(instance) {
  return instance.vnode.shapeFlag & 4;
}
let isInSSRComponentSetup = false;
function setupComponent(instance, isSSR = false, optimized = false) {
  isSSR && setInSSRSetupState(isSSR);
  const { props, children } = instance.vnode;
  const isStateful = isStatefulComponent(instance);
  initProps(instance, props, isStateful, isSSR);
  initSlots(instance, children, optimized || isSSR);
  const setupResult = isStateful ? setupStatefulComponent(instance, isSSR) : void 0;
  isSSR && setInSSRSetupState(false);
  return setupResult;
}
function setupStatefulComponent(instance, isSSR) {
  var _a;
  const Component = instance.type;
  if (!!(process.env.NODE_ENV !== "production")) {
    if (Component.name) {
      validateComponentName(Component.name, instance.appContext.config);
    }
    if (Component.components) {
      const names = Object.keys(Component.components);
      for (let i2 = 0; i2 < names.length; i2++) {
        validateComponentName(names[i2], instance.appContext.config);
      }
    }
    if (Component.directives) {
      const names = Object.keys(Component.directives);
      for (let i2 = 0; i2 < names.length; i2++) {
        validateDirectiveName(names[i2]);
      }
    }
    if (Component.compilerOptions && isRuntimeOnly()) {
      warn$1(
        `"compilerOptions" is only supported when using a build of Vue that includes the runtime compiler. Since you are using a runtime-only build, the options should be passed via your build tool config instead.`
      );
    }
  }
  instance.accessCache = /* @__PURE__ */ Object.create(null);
  instance.proxy = new Proxy(instance.ctx, PublicInstanceProxyHandlers);
  if (!!(process.env.NODE_ENV !== "production")) {
    exposePropsOnRenderContext(instance);
  }
  const { setup } = Component;
  if (setup) {
    pauseTracking();
    const setupContext = instance.setupContext = setup.length > 1 ? createSetupContext(instance) : null;
    const reset = setCurrentInstance(instance);
    const setupResult = callWithErrorHandling(
      setup,
      instance,
      0,
      [
        !!(process.env.NODE_ENV !== "production") ? shallowReadonly(instance.props) : instance.props,
        setupContext
      ]
    );
    const isAsyncSetup = isPromise(setupResult);
    resetTracking();
    reset();
    if ((isAsyncSetup || instance.sp) && !isAsyncWrapper(instance)) {
      markAsyncBoundary(instance);
    }
    if (isAsyncSetup) {
      setupResult.then(unsetCurrentInstance, unsetCurrentInstance);
      if (isSSR) {
        return setupResult.then((resolvedResult) => {
          handleSetupResult(instance, resolvedResult, isSSR);
        }).catch((e2) => {
          handleError(e2, instance, 0);
        });
      } else {
        instance.asyncDep = setupResult;
        if (!!(process.env.NODE_ENV !== "production") && !instance.suspense) {
          const name = (_a = Component.name) != null ? _a : "Anonymous";
          warn$1(
            `Component <${name}>: setup function returned a promise, but no <Suspense> boundary was found in the parent component tree. A component with async setup() must be nested in a <Suspense> in order to be rendered.`
          );
        }
      }
    } else {
      handleSetupResult(instance, setupResult, isSSR);
    }
  } else {
    finishComponentSetup(instance, isSSR);
  }
}
function handleSetupResult(instance, setupResult, isSSR) {
  if (isFunction(setupResult)) {
    if (instance.type.__ssrInlineRender) {
      instance.ssrRender = setupResult;
    } else {
      instance.render = setupResult;
    }
  } else if (isObject(setupResult)) {
    if (!!(process.env.NODE_ENV !== "production") && isVNode(setupResult)) {
      warn$1(
        `setup() should not return VNodes directly - return a render function instead.`
      );
    }
    if (!!(process.env.NODE_ENV !== "production") || false) {
      instance.devtoolsRawSetupState = setupResult;
    }
    instance.setupState = proxyRefs(setupResult);
    if (!!(process.env.NODE_ENV !== "production")) {
      exposeSetupStateOnRenderContext(instance);
    }
  } else if (!!(process.env.NODE_ENV !== "production") && setupResult !== void 0) {
    warn$1(
      `setup() should return an object. Received: ${setupResult === null ? "null" : typeof setupResult}`
    );
  }
  finishComponentSetup(instance, isSSR);
}
const isRuntimeOnly = () => true;
function finishComponentSetup(instance, isSSR, skipOptions) {
  const Component = instance.type;
  if (!instance.render) {
    instance.render = Component.render || NOOP;
  }
  {
    const reset = setCurrentInstance(instance);
    pauseTracking();
    try {
      applyOptions(instance);
    } finally {
      resetTracking();
      reset();
    }
  }
  if (!!(process.env.NODE_ENV !== "production") && !Component.render && instance.render === NOOP && !isSSR) {
    if (Component.template) {
      warn$1(
        `Component provided template option but runtime compilation is not supported in this build of Vue. Configure your bundler to alias "vue" to "vue/dist/vue.esm-bundler.js".`
      );
    } else {
      warn$1(`Component is missing template or render function: `, Component);
    }
  }
}
const attrsProxyHandlers = !!(process.env.NODE_ENV !== "production") ? {
  get(target, key) {
    markAttrsAccessed();
    track(target, "get", "");
    return target[key];
  },
  set() {
    warn$1(`setupContext.attrs is readonly.`);
    return false;
  },
  deleteProperty() {
    warn$1(`setupContext.attrs is readonly.`);
    return false;
  }
} : {
  get(target, key) {
    track(target, "get", "");
    return target[key];
  }
};
function getSlotsProxy(instance) {
  return new Proxy(instance.slots, {
    get(target, key) {
      track(instance, "get", "$slots");
      return target[key];
    }
  });
}
function createSetupContext(instance) {
  const expose = (exposed) => {
    if (!!(process.env.NODE_ENV !== "production")) {
      if (instance.exposed) {
        warn$1(`expose() should be called only once per setup().`);
      }
      if (exposed != null) {
        let exposedType = typeof exposed;
        if (exposedType === "object") {
          if (isArray(exposed)) {
            exposedType = "array";
          } else if (isRef(exposed)) {
            exposedType = "ref";
          }
        }
        if (exposedType !== "object") {
          warn$1(
            `expose() should be passed a plain object, received ${exposedType}.`
          );
        }
      }
    }
    instance.exposed = exposed || {};
  };
  if (!!(process.env.NODE_ENV !== "production")) {
    let attrsProxy;
    let slotsProxy;
    return Object.freeze({
      get attrs() {
        return attrsProxy || (attrsProxy = new Proxy(instance.attrs, attrsProxyHandlers));
      },
      get slots() {
        return slotsProxy || (slotsProxy = getSlotsProxy(instance));
      },
      get emit() {
        return (event, ...args) => instance.emit(event, ...args);
      },
      expose
    });
  } else {
    return {
      attrs: new Proxy(instance.attrs, attrsProxyHandlers),
      slots: instance.slots,
      emit: instance.emit,
      expose
    };
  }
}
function getComponentPublicInstance(instance) {
  if (instance.exposed) {
    return instance.exposeProxy || (instance.exposeProxy = new Proxy(proxyRefs(markRaw(instance.exposed)), {
      get(target, key) {
        if (key in target) {
          return target[key];
        } else if (key in publicPropertiesMap) {
          return publicPropertiesMap[key](instance);
        }
      },
      has(target, key) {
        return key in target || key in publicPropertiesMap;
      }
    }));
  } else {
    return instance.proxy;
  }
}
const classifyRE = /(?:^|[-_])(\w)/g;
const classify = (str) => str.replace(classifyRE, (c2) => c2.toUpperCase()).replace(/[-_]/g, "");
function getComponentName(Component, includeInferred = true) {
  return isFunction(Component) ? Component.displayName || Component.name : Component.name || includeInferred && Component.__name;
}
function formatComponentName(instance, Component, isRoot = false) {
  let name = getComponentName(Component);
  if (!name && Component.__file) {
    const match = Component.__file.match(/([^/\\]+)\.\w+$/);
    if (match) {
      name = match[1];
    }
  }
  if (!name && instance && instance.parent) {
    const inferFromRegistry = (registry) => {
      for (const key in registry) {
        if (registry[key] === Component) {
          return key;
        }
      }
    };
    name = inferFromRegistry(
      instance.components || instance.parent.type.components
    ) || inferFromRegistry(instance.appContext.components);
  }
  return name ? classify(name) : isRoot ? `App` : `Anonymous`;
}
function isClassComponent(value) {
  return isFunction(value) && "__vccOpts" in value;
}
const computed = (getterOrOptions, debugOptions) => {
  const c2 = computed$1(getterOrOptions, debugOptions, isInSSRComponentSetup);
  if (!!(process.env.NODE_ENV !== "production")) {
    const i2 = getCurrentInstance();
    if (i2 && i2.appContext.config.warnRecursiveComputed) {
      c2._warnRecursive = true;
    }
  }
  return c2;
};
function h(type, propsOrChildren, children) {
  const l = arguments.length;
  if (l === 2) {
    if (isObject(propsOrChildren) && !isArray(propsOrChildren)) {
      if (isVNode(propsOrChildren)) {
        return createVNode(type, null, [propsOrChildren]);
      }
      return createVNode(type, propsOrChildren);
    } else {
      return createVNode(type, null, propsOrChildren);
    }
  } else {
    if (l > 3) {
      children = Array.prototype.slice.call(arguments, 2);
    } else if (l === 3 && isVNode(children)) {
      children = [children];
    }
    return createVNode(type, propsOrChildren, children);
  }
}
function initCustomFormatter() {
  if (!!!(process.env.NODE_ENV !== "production") || typeof window === "undefined") {
    return;
  }
  const vueStyle = { style: "color:#3ba776" };
  const numberStyle = { style: "color:#1677ff" };
  const stringStyle = { style: "color:#f5222d" };
  const keywordStyle = { style: "color:#eb2f96" };
  const formatter = {
    __vue_custom_formatter: true,
    header(obj) {
      if (!isObject(obj)) {
        return null;
      }
      if (obj.__isVue) {
        return ["div", vueStyle, `VueInstance`];
      } else if (isRef(obj)) {
        pauseTracking();
        const value = obj.value;
        resetTracking();
        return [
          "div",
          {},
          ["span", vueStyle, genRefFlag(obj)],
          "<",
          formatValue(value),
          `>`
        ];
      } else if (isReactive(obj)) {
        return [
          "div",
          {},
          ["span", vueStyle, isShallow(obj) ? "ShallowReactive" : "Reactive"],
          "<",
          formatValue(obj),
          `>${isReadonly(obj) ? ` (readonly)` : ``}`
        ];
      } else if (isReadonly(obj)) {
        return [
          "div",
          {},
          ["span", vueStyle, isShallow(obj) ? "ShallowReadonly" : "Readonly"],
          "<",
          formatValue(obj),
          ">"
        ];
      }
      return null;
    },
    hasBody(obj) {
      return obj && obj.__isVue;
    },
    body(obj) {
      if (obj && obj.__isVue) {
        return [
          "div",
          {},
          ...formatInstance(obj.$)
        ];
      }
    }
  };
  function formatInstance(instance) {
    const blocks = [];
    if (instance.type.props && instance.props) {
      blocks.push(createInstanceBlock("props", toRaw(instance.props)));
    }
    if (instance.setupState !== EMPTY_OBJ) {
      blocks.push(createInstanceBlock("setup", instance.setupState));
    }
    if (instance.data !== EMPTY_OBJ) {
      blocks.push(createInstanceBlock("data", toRaw(instance.data)));
    }
    const computed2 = extractKeys(instance, "computed");
    if (computed2) {
      blocks.push(createInstanceBlock("computed", computed2));
    }
    const injected = extractKeys(instance, "inject");
    if (injected) {
      blocks.push(createInstanceBlock("injected", injected));
    }
    blocks.push([
      "div",
      {},
      [
        "span",
        {
          style: keywordStyle.style + ";opacity:0.66"
        },
        "$ (internal): "
      ],
      ["object", { object: instance }]
    ]);
    return blocks;
  }
  function createInstanceBlock(type, target) {
    target = extend({}, target);
    if (!Object.keys(target).length) {
      return ["span", {}];
    }
    return [
      "div",
      { style: "line-height:1.25em;margin-bottom:0.6em" },
      [
        "div",
        {
          style: "color:#476582"
        },
        type
      ],
      [
        "div",
        {
          style: "padding-left:1.25em"
        },
        ...Object.keys(target).map((key) => {
          return [
            "div",
            {},
            ["span", keywordStyle, key + ": "],
            formatValue(target[key], false)
          ];
        })
      ]
    ];
  }
  function formatValue(v, asRaw = true) {
    if (typeof v === "number") {
      return ["span", numberStyle, v];
    } else if (typeof v === "string") {
      return ["span", stringStyle, JSON.stringify(v)];
    } else if (typeof v === "boolean") {
      return ["span", keywordStyle, v];
    } else if (isObject(v)) {
      return ["object", { object: asRaw ? toRaw(v) : v }];
    } else {
      return ["span", stringStyle, String(v)];
    }
  }
  function extractKeys(instance, type) {
    const Comp = instance.type;
    if (isFunction(Comp)) {
      return;
    }
    const extracted = {};
    for (const key in instance.ctx) {
      if (isKeyOfType(Comp, key, type)) {
        extracted[key] = instance.ctx[key];
      }
    }
    return extracted;
  }
  function isKeyOfType(Comp, key, type) {
    const opts = Comp[type];
    if (isArray(opts) && opts.includes(key) || isObject(opts) && key in opts) {
      return true;
    }
    if (Comp.extends && isKeyOfType(Comp.extends, key, type)) {
      return true;
    }
    if (Comp.mixins && Comp.mixins.some((m) => isKeyOfType(m, key, type))) {
      return true;
    }
  }
  function genRefFlag(v) {
    if (isShallow(v)) {
      return `ShallowRef`;
    }
    if (v.effect) {
      return `ComputedRef`;
    }
    return `Ref`;
  }
  if (window.devtoolsFormatters) {
    window.devtoolsFormatters.push(formatter);
  } else {
    window.devtoolsFormatters = [formatter];
  }
}
const version = "3.5.17";
!!(process.env.NODE_ENV !== "production") ? warn$1 : NOOP;
!!(process.env.NODE_ENV !== "production") || true ? devtools$1 : void 0;
!!(process.env.NODE_ENV !== "production") || true ? setDevtoolsHook$1 : NOOP;
function renderVueToPdfStructure(root, props = {}) {
  const container = {};
  const { createApp } = createRenderer(nodeOps);
  const app = createApp(root, props);
  app.use(
    {
      install(app2, options) {
        app2.config.globalProperties.api = options.store;
        app2.config.globalProperties.$tc = options.$tc;
        app2.config.globalProperties.$date = dayjs;
      }
    },
    props
  );
  app.mount(container);
  return container.doc;
}
const fontStore = new FontStore();
async function renderPdfStructureToReactPdf(pdfStructure, compress = true) {
  const layout = await layoutDocument(pdfStructure, fontStore);
  const documentProps = pdfStructure.props || {};
  const { pdfVersion, language, pageLayout, pageMode } = documentProps;
  const ctx = new PDFDocument({
    compress,
    pdfVersion,
    lang: language,
    displayTitle: true,
    autoFirstPage: false,
    pageLayout,
    pageMode
  });
  return renderPDF(ctx, layout);
}
const Font = fontStore;
const pdf = (root, props) => {
  const render2 = async (compress = true) => {
    const pdfStructure = renderVueToPdfStructure(root, props);
    return renderPdfStructureToReactPdf(pdfStructure, compress);
  };
  const toBlob = async () => {
    const chunks = [];
    const instance = await render2();
    return new Promise((resolve2, reject) => {
      instance.on("data", (chunk) => {
        chunks.push(chunk instanceof Uint8Array ? chunk : new Uint8Array(chunk));
      });
      instance.on("end", () => {
        try {
          const blob = new Blob(chunks, { type: "application/pdf" });
          resolve2(blob);
        } catch (error) {
          reject(error);
        }
      });
    });
  };
  return {
    toBlob
  };
};
const PdfComponent = {
  props: {
    id: { type: String, default: "" }
  },
  beforeCreate() {
    var _a;
    Object.entries(((_a = this.$options) == null ? void 0 : _a.pdfStyle) || []).forEach(([selector, rules2]) => {
      styleStore[selector] = styleStore[selector] || {};
      Object.assign(styleStore[selector], rules2);
    });
  }
};
/**
* vue v3.5.17
* (c) 2018-present Yuxi (Evan) You and Vue contributors
* @license MIT
**/
function initDev() {
  {
    initCustomFormatter();
  }
}
if (!!(process.env.NODE_ENV !== "production")) {
  initDev();
}
const block0$u = (component) => {
  component.pdfStyle = { "cover-center": { "textAlign": "center" }, "cover-camp-wrapper": { "padding": "72pt 0 0" }, "cover-camp-organizer": { "fontSize": "18pt", "fontWeight": "medium" }, "cover-camp-title": { "fontSize": "38pt", "fontWeight": "semibold", "margin": "40pt 0" }, "cover-camp-motto": { "fontSize": "28pt", "fontWeight": "medium" } };
};
const _export_sfc = (sfc, props) => {
  const target = sfc.__vccOpts || sfc;
  for (const [key, val] of props) {
    target[key] = val;
  }
  return target;
};
const _sfc_main$P = {
  name: "Cover",
  extends: PdfComponent,
  props: {
    content: { type: Object, required: true },
    config: { type: Object, required: true }
  }
};
const _hoisted_1$I = ["id", "bookmark"];
const _hoisted_2$p = { class: "cover-camp-wrapper" };
const _hoisted_3$d = {
  key: 0,
  class: "cover-camp-organizer cover-center"
};
const _hoisted_4$8 = { class: "cover-camp-title cover-center" };
const _hoisted_5$6 = { class: "cover-camp-motto cover-center" };
function _sfc_render$N(_ctx, _cache, $props, $setup, $data, $options) {
  return openBlock(), createElementBlock("Page", {
    id: _ctx.id,
    size: "A4",
    bookmark: _ctx.$tc("print.cover.title"),
    class: "page"
  }, [
    createBaseVNode("View", _hoisted_2$p, [
      $props.config.camp.organizer ? (openBlock(), createElementBlock(
        "Text",
        _hoisted_3$d,
        toDisplayString($props.config.camp.organizer),
        1
        /* TEXT */
      )) : createCommentVNode("v-if", true),
      createBaseVNode(
        "Text",
        _hoisted_4$8,
        toDisplayString($props.config.camp.title),
        1
        /* TEXT */
      ),
      createBaseVNode(
        "Text",
        _hoisted_5$6,
        toDisplayString($props.config.camp.motto),
        1
        /* TEXT */
      )
    ])
  ], 8, _hoisted_1$I);
}
if (typeof block0$u === "function") block0$u(_sfc_main$P);
const Cover$1 = /* @__PURE__ */ _export_sfc(_sfc_main$P, [["render", _sfc_render$N], ["__file", "/app/src/campPrint/cover/Cover.vue"]]);
const _sfc_main$O = {
  name: "Cover",
  extends: PdfComponent
};
const _hoisted_1$H = ["href"];
function _sfc_render$M(_ctx, _cache, $props, $setup, $data, $options) {
  return openBlock(), createElementBlock("Link", {
    class: "toc-entry",
    href: `#${_ctx.id}`
  }, [
    createBaseVNode(
      "Text",
      null,
      toDisplayString(_ctx.$t("print.cover.title")),
      1
      /* TEXT */
    )
  ], 8, _hoisted_1$H);
}
const Cover = /* @__PURE__ */ _export_sfc(_sfc_main$O, [["render", _sfc_render$M], ["__file", "/app/src/campPrint/tableOfContents/entry/Cover.vue"]]);
const _sfc_main$N = {
  name: "Toc",
  extends: PdfComponent
};
const _hoisted_1$G = ["href"];
function _sfc_render$L(_ctx, _cache, $props, $setup, $data, $options) {
  return openBlock(), createElementBlock("Link", {
    class: "toc-entry",
    href: `#${_ctx.id}`
  }, [
    createBaseVNode(
      "Text",
      null,
      toDisplayString(_ctx.$tc("print.toc.title")),
      1
      /* TEXT */
    )
  ], 8, _hoisted_1$G);
}
const Toc = /* @__PURE__ */ _export_sfc(_sfc_main$N, [["render", _sfc_render$L], ["__file", "/app/src/campPrint/tableOfContents/entry/Toc.vue"]]);
const _sfc_main$M = {
  name: "Picasso",
  extends: PdfComponent,
  props: {
    entry: { type: Object, required: true }
  },
  computed: {
    periods() {
      return this.entry.options.periods.map((periodUri) => this.api.get(periodUri));
    }
  }
};
const _hoisted_1$F = ["href"];
function _sfc_render$K(_ctx, _cache, $props, $setup, $data, $options) {
  return openBlock(true), createElementBlock(
    Fragment,
    null,
    renderList($options.periods, (period) => {
      return openBlock(), createElementBlock("Link", {
        class: "toc-entry",
        href: `#${_ctx.id}-${period.id}`
      }, [
        createBaseVNode(
          "Text",
          null,
          toDisplayString(_ctx.$t("print.picasso.title", { period: period.description })),
          1
          /* TEXT */
        )
      ], 8, _hoisted_1$F);
    }),
    256
    /* UNKEYED_FRAGMENT */
  );
}
const Picasso$1 = /* @__PURE__ */ _export_sfc(_sfc_main$M, [["render", _sfc_render$K], ["__file", "/app/src/campPrint/tableOfContents/entry/Picasso.vue"]]);
const filterMatchScheduleEntry = (scheduleEntry, filter) => {
  var _a, _b;
  if (!filter) return true;
  return (
    // filter by period
    (filter.period === null || filter.period === void 0 || scheduleEntry.period()._meta.self === filter.period) && // filter by days: OR filter
    (filter.day === null || filter.day === void 0 || filter.day.length === 0 || filter.day.includes(
      scheduleEntry.day()._meta.self
    )) && // filter by categories: OR filter
    (filter.category === null || filter.category === void 0 || filter.category.length === 0 || filter.category.includes(
      scheduleEntry.activity().category()._meta.self
    )) && // filter by responsibles: AND filter
    (filter.responsible === null || filter.responsible === void 0 || filter.responsible.length === 0 || filter.responsible.every((responsible) => {
      return scheduleEntry.activity().activityResponsibles().items.map((responsible2) => responsible2.campCollaboration()._meta.self).includes(responsible);
    }) || filter.responsible[0] === "none" && scheduleEntry.activity().activityResponsibles().items.length === 0) && (filter.progressLabel === null || filter.progressLabel === void 0 || filter.progressLabel.length === 0 || filter.progressLabel.includes(
      ((_b = (_a = scheduleEntry.activity()).progressLabel) == null ? void 0 : _b.call(_a)._meta.self) ?? "none"
    ))
  );
};
const _sfc_main$L = {
  name: "Program",
  extends: PdfComponent,
  props: {
    period: { type: Object, required: true },
    filter: { type: Object, default: () => ({}) }
  },
  computed: {
    anyScheduleEntries() {
      return this.period.scheduleEntries().items.length;
    },
    scheduleEntries() {
      return this.period.scheduleEntries().items.filter((scheduleEntry) => {
        return filterMatchScheduleEntry(scheduleEntry, this.filter);
      }).map((scheduleEntry) => {
        const activity = scheduleEntry.activity();
        return {
          ...scheduleEntry,
          category: activity.category().short,
          title: activity.title
        };
      });
    }
  }
};
const _hoisted_1$E = ["href"];
const _hoisted_2$o = ["href"];
function _sfc_render$J(_ctx, _cache, $props, $setup, $data, $options) {
  return $options.anyScheduleEntries ? (openBlock(), createElementBlock(
    Fragment,
    { key: 0 },
    [
      createBaseVNode("Link", {
        class: "toc-entry",
        href: `#${_ctx.id}-${$props.period.id}`
      }, [
        createBaseVNode(
          "Text",
          null,
          toDisplayString($props.period.description),
          1
          /* TEXT */
        )
      ], 8, _hoisted_1$E),
      (openBlock(true), createElementBlock(
        Fragment,
        null,
        renderList($options.scheduleEntries, (scheduleEntry) => {
          return openBlock(), createElementBlock("Link", {
            class: "toc-entry toc-sub-entry",
            href: `#${_ctx.id}-${$props.period.id}-${scheduleEntry.id}`
          }, [
            createBaseVNode(
              "Text",
              null,
              toDisplayString(scheduleEntry.category) + " " + toDisplayString(scheduleEntry.number) + " " + toDisplayString(scheduleEntry.title),
              1
              /* TEXT */
            )
          ], 8, _hoisted_2$o);
        }),
        256
        /* UNKEYED_FRAGMENT */
      ))
    ],
    64
    /* STABLE_FRAGMENT */
  )) : createCommentVNode("v-if", true);
}
const ProgramPeriod$1 = /* @__PURE__ */ _export_sfc(_sfc_main$L, [["render", _sfc_render$J], ["__file", "/app/src/campPrint/tableOfContents/entry/ProgramPeriod.vue"]]);
const _sfc_main$K = {
  name: "Program",
  components: { ProgramPeriod: ProgramPeriod$1 },
  extends: PdfComponent,
  props: {
    entry: { type: Object, required: true }
  },
  computed: {
    periods() {
      return this.entry.options.periods.map((periodUri) => this.api.get(periodUri));
    }
  }
};
function _sfc_render$I(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_ProgramPeriod = resolveComponent("ProgramPeriod");
  return openBlock(true), createElementBlock(
    Fragment,
    null,
    renderList($options.periods, (period) => {
      return openBlock(), createBlock(_component_ProgramPeriod, {
        id: _ctx.id,
        period,
        filter: $props.entry.options.filter
      }, null, 8, ["id", "period", "filter"]);
    }),
    256
    /* UNKEYED_FRAGMENT */
  );
}
const Program$1 = /* @__PURE__ */ _export_sfc(_sfc_main$K, [["render", _sfc_render$I], ["__file", "/app/src/campPrint/tableOfContents/entry/Program.vue"]]);
const _sfc_main$J = {
  name: "Activity",
  extends: PdfComponent,
  props: {
    entry: { type: Object, required: true }
  },
  computed: {
    scheduleEntry() {
      if (!this.entry.options.scheduleEntry) return null;
      const scheduleEntry = this.api.get(this.entry.options.scheduleEntry);
      const activity = scheduleEntry.activity();
      return {
        ...scheduleEntry,
        category: activity.category().short,
        title: activity.title
      };
    }
  }
};
const _hoisted_1$D = ["href"];
function _sfc_render$H(_ctx, _cache, $props, $setup, $data, $options) {
  return $options.scheduleEntry ? (openBlock(), createElementBlock("Link", {
    key: 0,
    class: "toc-entry",
    href: `#${_ctx.id}-${$options.scheduleEntry.id}`
  }, [
    createBaseVNode(
      "Text",
      null,
      toDisplayString($options.scheduleEntry.category) + " " + toDisplayString($options.scheduleEntry.number) + " " + toDisplayString($options.scheduleEntry.title),
      1
      /* TEXT */
    )
  ], 8, _hoisted_1$D)) : createCommentVNode("v-if", true);
}
const Activity$1 = /* @__PURE__ */ _export_sfc(_sfc_main$J, [["render", _sfc_render$H], ["__file", "/app/src/campPrint/tableOfContents/entry/Activity.vue"]]);
const _sfc_main$I = {
  name: "Summary",
  extends: PdfComponent,
  props: {
    entry: { type: Object, required: true }
  },
  computed: {
    periods() {
      return this.entry.options.periods.map((periodUri) => this.api.get(periodUri));
    }
  }
};
const Summary = /* @__PURE__ */ _export_sfc(_sfc_main$I, [["__file", "/app/src/campPrint/tableOfContents/entry/Summary.vue"]]);
const _sfc_main$H = {
  name: "SafetyConsiderations",
  extends: Summary
};
const _hoisted_1$C = ["href"];
function _sfc_render$G(_ctx, _cache, $props, $setup, $data, $options) {
  return openBlock(true), createElementBlock(
    Fragment,
    null,
    renderList(_ctx.periods, (period) => {
      return openBlock(), createElementBlock("Link", {
        class: "toc-entry",
        href: `#${_ctx.id}-${period.id}`
      }, [
        createBaseVNode(
          "Text",
          null,
          toDisplayString(_ctx.$t("print.summary.safetyConsiderations.title")) + ": " + toDisplayString(period.description),
          1
          /* TEXT */
        )
      ], 8, _hoisted_1$C);
    }),
    256
    /* UNKEYED_FRAGMENT */
  );
}
const SafetyConsiderations$2 = /* @__PURE__ */ _export_sfc(_sfc_main$H, [["render", _sfc_render$G], ["__file", "/app/src/campPrint/tableOfContents/entry/SafetyConsiderations.vue"]]);
const _sfc_main$G = {
  name: "Story",
  extends: Summary
};
const _hoisted_1$B = ["href"];
function _sfc_render$F(_ctx, _cache, $props, $setup, $data, $options) {
  return openBlock(true), createElementBlock(
    Fragment,
    null,
    renderList(_ctx.periods, (period) => {
      return openBlock(), createElementBlock("Link", {
        class: "toc-entry",
        href: `#${_ctx.id}-${period.id}`
      }, [
        createBaseVNode(
          "Text",
          null,
          toDisplayString(_ctx.$tc("print.summary.storycontext.title")) + ": " + toDisplayString(period.description),
          1
          /* TEXT */
        )
      ], 8, _hoisted_1$B);
    }),
    256
    /* UNKEYED_FRAGMENT */
  );
}
const Story$1 = /* @__PURE__ */ _export_sfc(_sfc_main$G, [["render", _sfc_render$F], ["__file", "/app/src/campPrint/tableOfContents/entry/Story.vue"]]);
const block0$t = (component) => {
  component.pdfStyle = { "toc-title": { "fontWeight": "semibold", "fontSize": "14pt", "borderBottom": "2pt solid #aaaaaa", "paddingBottom": "2pt", "marginBottom": "8pt" }, "toc-entry": { "display": "flex", "flexDirection": "row", "justifyContent": "space-between", "color": "black", "textDecoration": "none" }, "toc-sub-entry": { "marginLeft": "10pt" } };
};
const _sfc_main$F = {
  name: "Cover",
  extends: PdfComponent,
  props: {
    content: { type: Object, required: true },
    config: { type: Object, required: true }
  },
  computed: {
    entryComponents() {
      return {
        Cover,
        Toc,
        Picasso: Picasso$1,
        Program: Program$1,
        Activity: Activity$1,
        SafetyConsiderations: SafetyConsiderations$2,
        Story: Story$1
      };
    }
  }
};
const _hoisted_1$A = {
  size: "A4",
  class: "page"
};
const _hoisted_2$n = ["id", "bookmark"];
const _hoisted_3$c = { style: { "line-height": "1" } };
const _hoisted_4$7 = { key: 1 };
function _sfc_render$E(_ctx, _cache, $props, $setup, $data, $options) {
  return openBlock(), createElementBlock("Page", _hoisted_1$A, [
    createBaseVNode("Text", {
      id: _ctx.id,
      bookmark: _ctx.$t("print.toc.title"),
      class: "toc-title"
    }, toDisplayString(_ctx.$t("print.toc.title")), 9, _hoisted_2$n),
    createBaseVNode("View", _hoisted_3$c, [
      (openBlock(true), createElementBlock(
        Fragment,
        null,
        renderList($props.config.contents, (entry, index) => {
          return openBlock(), createElementBlock(
            Fragment,
            null,
            [
              entry.type in $options.entryComponents ? (openBlock(), createBlock(resolveDynamicComponent($options.entryComponents[entry.type]), {
                key: 0,
                id: `entry-${index}`,
                entry
              }, null, 8, ["id", "entry"])) : (openBlock(), createElementBlock(
                "Text",
                _hoisted_4$7,
                toDisplayString(entry.type),
                1
                /* TEXT */
              ))
            ],
            64
            /* STABLE_FRAGMENT */
          );
        }),
        256
        /* UNKEYED_FRAGMENT */
      ))
    ])
  ]);
}
if (typeof block0$t === "function") block0$t(_sfc_main$F);
const TableOfContents = /* @__PURE__ */ _export_sfc(_sfc_main$F, [["render", _sfc_render$E], ["__file", "/app/src/campPrint/tableOfContents/TableOfContents.vue"]]);
const block0$s = (component) => {
  component.pdfStyle = { "ys-logo-path": { "fill": "#e92d35", "fillRule": "evenodd", "stroke": "none" } };
};
const _sfc_main$E = {
  name: "YSLogo",
  extends: PdfComponent,
  props: {
    size: { type: Number, default: 20 },
    locale: { type: String, default: "en" }
  },
  computed: {
    path() {
      if (this.locale && this.locale.match(/^it/i)) {
        return "M748 323V191 323H639.3193A200 200 0 01747 503V565A90 94 0 01381 565H433.1321V433.6068H563.2424V323.5098H433.1321V191.047H380.2688A185 210 0 01748 191M322.4024 191V323.5098H190.4173V433.6068H322V565H374V755H175.8372A185 210 0 018 543V191A184 210 0 01373 191Z";
      }
      return "M747 324H640A140 150 0 01745.7661 466.6977V581.2591A1 1 0 01383 565H431.9795V435.5404H563.7701V324.1804H431.9795V193.2711H381.9101A152 160 0 01747 190V324M373.6341 8.7111V193.2844H321.8635V324.1804H186.8021V8.4524H373.6341M321.5128 564.8657H372.7061A1 1 0 018 581V435.5484H321.5128V564.8657";
    }
  }
};
const _hoisted_1$z = ["width", "height"];
const _hoisted_2$m = ["d"];
function _sfc_render$D(_ctx, _cache, $props, $setup, $data, $options) {
  return openBlock(), createElementBlock("Svg", {
    width: $props.size,
    height: $props.size,
    viewBox: "0 0 755.907 755.907"
  }, [
    createBaseVNode("Path", {
      d: $options.path,
      class: "ys-logo-path"
    }, null, 8, _hoisted_2$m)
  ], 8, _hoisted_1$z);
}
if (typeof block0$s === "function") block0$s(_sfc_main$E);
const YSLogo = /* @__PURE__ */ _export_sfc(_sfc_main$E, [["render", _sfc_render$D], ["__file", "/app/src/campPrint/YSLogo.vue"]]);
function longestTime(times2, dayjs2) {
  return dayjs2().hour(0).minute(findLongestText(times2)[0] * 60).second(0).format("LT");
}
function findLongestText(times2) {
  return maxBy(times2, (time) => (time[0] - 1) % 24 + 1);
}
const block0$r = (component) => {
  component.pdfStyle = { "picasso-time-column-spacer": { "marginTop": "0", "marginBottom": "0" }, "picasso-time-column-spacer-text": { "opacity": "0" } };
};
const _sfc_main$D = {
  name: "TimeColumnSpacer",
  extends: PdfComponent,
  props: {
    times: { type: Array, required: true }
  },
  computed: {
    longestTime() {
      return longestTime(this.times, this.$date);
    }
  }
};
const _hoisted_1$y = { class: "picasso-time-column picasso-time-column-spacer" };
const _hoisted_2$l = { class: "picasso-time-column-text picasso-time-column-spacer-text" };
function _sfc_render$C(_ctx, _cache, $props, $setup, $data, $options) {
  return openBlock(), createElementBlock("View", _hoisted_1$y, [
    createBaseVNode(
      "Text",
      _hoisted_2$l,
      toDisplayString($options.longestTime),
      1
      /* TEXT */
    )
  ]);
}
if (typeof block0$r === "function") block0$r(_sfc_main$D);
const TimeColumnSpacer = /* @__PURE__ */ _export_sfc(_sfc_main$D, [["render", _sfc_render$C], ["__file", "/app/src/campPrint/picasso/TimeColumnSpacer.vue"]]);
function userDisplayName$1(user) {
  return user.displayName || "";
}
function campCollaborationDisplayName(campCollaboration, tc, indicateInactive = true) {
  if (!campCollaboration) {
    return "";
  }
  let text = typeof campCollaboration.user === "function" ? userDisplayName$1(campCollaboration.user()) : campCollaboration.inviteEmail || "";
  if (campCollaboration.status === "inactive" && indicateInactive) {
    text += " (" + tc("entity.campCollaboration.status.inactive") + ")";
  }
  return text;
}
const filterDayResponsiblesByDay = (day) => {
  if (!day) return [];
  return day.period().dayResponsibles().items.filter((dayResponsible) => dayResponsible.day()._meta.self === day._meta.self);
};
const dayResponsiblesCommaSeparated = (day, tc) => {
  if (!day) return "";
  return filterDayResponsiblesByDay(day).map(
    (dayResponsible) => campCollaborationDisplayName(dayResponsible.campCollaboration(), tc)
  ).join(", ");
};
const block0$q = (component) => {
  component.pdfStyle = { "picasso-day-header-text": { "fontSize": "8pt", "fontWeight": "bold", "margin": "0 auto 2pt" }, "picasso-day-responsibles": { "fontSize": "8pt", "margin": "3pt auto 0", "lineHeight": "1.3" }, "picasso-day-responsibles-text": { "paddingBottom": "5pt" } };
};
const _sfc_main$C = {
  name: "DayHeader",
  extends: PdfComponent,
  props: {
    day: { type: Object, required: true },
    showDayResponsibles: { type: Boolean, default: false }
  },
  computed: {
    date() {
      return this.$date.utc(this.day.start).hour(0).minute(0).second(0).format(this.$tc("global.datetime.dateLong"));
    },
    dayResponsibles() {
      if (filterDayResponsiblesByDay(this.day).length === 0) return "";
      const label = this.$tc("entity.day.fields.dayResponsibles");
      const displayNames = dayResponsiblesCommaSeparated(this.day, this.$tc);
      return `${label}: ${displayNames}`;
    }
  }
};
const _hoisted_1$x = { class: "picasso-day-header-text" };
const _hoisted_2$k = {
  key: 0,
  class: "picasso-day-responsibles"
};
const _hoisted_3$b = { class: "picasso-day-responsibles-text" };
function _sfc_render$B(_ctx, _cache, $props, $setup, $data, $options) {
  return openBlock(), createElementBlock("View", null, [
    createBaseVNode(
      "Text",
      _hoisted_1$x,
      toDisplayString($options.date),
      1
      /* TEXT */
    ),
    $props.showDayResponsibles ? (openBlock(), createElementBlock("View", _hoisted_2$k, [
      createBaseVNode(
        "Text",
        _hoisted_3$b,
        toDisplayString($options.dayResponsibles),
        1
        /* TEXT */
      )
    ])) : createCommentVNode("v-if", true)
  ]);
}
if (typeof block0$q === "function") block0$q(_sfc_main$C);
const DayHeader = /* @__PURE__ */ _export_sfc(_sfc_main$C, [["render", _sfc_render$B], ["__file", "/app/src/campPrint/picasso/DayHeader.vue"]]);
const block0$p = (component) => {
  component.pdfStyle = { "picasso-time-column-container": { "position": "absolute", "top": "-6", "bottom": "6", "left": "0", "right": "0" }, "picasso-time-column-row": { "paddingHorizontal": "2pt", "flexBasis": "0" } };
};
const _sfc_main$B = {
  name: "TimeColumn",
  extends: PdfComponent,
  props: {
    times: { type: Array, required: true },
    align: { type: String, default: "left" }
  },
  computed: {
    width() {
      return 5 * longestTime(this.times, this.$date).length;
    },
    displayedTimes() {
      return this.times.map(([time, weight], index) => {
        if (index === 0) return { time: " ", weight };
        return {
          weight,
          time: this.$date().hour(0).minute(time * 60).second(0).format("LT")
        };
      });
    }
  }
};
const _hoisted_1$w = { class: "picasso-time-column-container" };
const _hoisted_2$j = { class: "picasso-time-column-text" };
function _sfc_render$A(_ctx, _cache, $props, $setup, $data, $options) {
  return openBlock(), createElementBlock(
    "View",
    {
      class: "picasso-time-column",
      style: normalizeStyle({ width: $options.width })
    },
    [
      createBaseVNode("View", _hoisted_1$w, [
        (openBlock(true), createElementBlock(
          Fragment,
          null,
          renderList($options.displayedTimes, ({ time, weight }) => {
            return openBlock(), createElementBlock(
              "View",
              {
                class: "picasso-time-column-row",
                style: normalizeStyle({
                  flexGrow: weight,
                  alignItems: $props.align === "left" ? "flex-start" : "flex-end"
                })
              },
              [
                createBaseVNode(
                  "Text",
                  _hoisted_2$j,
                  toDisplayString(time),
                  1
                  /* TEXT */
                )
              ],
              4
              /* STYLE */
            );
          }),
          256
          /* UNKEYED_FRAGMENT */
        ))
      ])
    ],
    4
    /* STYLE */
  );
}
if (typeof block0$p === "function") block0$p(_sfc_main$B);
const TimeColumn = /* @__PURE__ */ _export_sfc(_sfc_main$B, [["render", _sfc_render$A], ["__file", "/app/src/campPrint/picasso/TimeColumn.vue"]]);
function contrastColor$1(color) {
  const input = new Color(color);
  const black = new Color("#000");
  const white = new Color("#fff");
  const blackContrast = Math.abs(input.contrast(black, "APCA"));
  const whiteContrast = Math.abs(input.contrast(white, "APCA"));
  return blackContrast > whiteContrast ? black.toString({ format: "hex" }) : white.toString({ format: "hex" });
}
function idToColor(id, inactive = false) {
  if (!id) {
    return new Color("HSL", [0, 0, 30]).to("srgb").toString({ format: "hex" });
  }
  return new Color("HSL", [parseInt(id, 16) % 360 || 0, inactive ? 0 : 100, 30]).to("srgb").toString({ format: "hex" });
}
function userColor(user, inactive = ((_a) => (_a = user._meta) == null ? void 0 : _a.loading)()) {
  if (user.color && !inactive) {
    return user.color;
  }
  return idToColor(user.id, inactive);
}
function campCollaborationColor(campCollaboration) {
  var _a, _b;
  if (!campCollaboration) {
    return idToColor("", true);
  }
  const inactive = ((_a = campCollaboration._meta) == null ? void 0 : _a.loading) || campCollaboration.status === "inactive";
  if ((campCollaboration == null ? void 0 : campCollaboration.color) && !inactive) {
    return campCollaboration.color;
  }
  if (typeof campCollaboration.user === "function") {
    return userColor(
      campCollaboration.user(),
      inactive || ((_b = campCollaboration.user()._meta) == null ? void 0 : _b.loading)
    );
  } else {
    return idToColor(campCollaboration.id, inactive);
  }
}
function userDisplayName(user) {
  return user.displayName || "";
}
function initials(displayName) {
  if (!displayName) return "";
  let items = displayName.split(" ", 2);
  if (items.length === 1) {
    items = items.shift().split(/[,._-]/, 2);
  }
  if (items.length === 1) {
    return runes.substr(displayName, 0, 2).toUpperCase();
  } else {
    return runes.substr(items[0], 0, 1).toUpperCase() + runes.substr(items[1], 0, 1).toUpperCase();
  }
}
function campCollaborationInitials(campCollaboration) {
  if (!campCollaboration) {
    return "";
  }
  if (campCollaboration == null ? void 0 : campCollaboration.abbreviation) {
    return campCollaboration.abbreviation;
  }
  if (typeof campCollaboration.user === "function") {
    if (campCollaboration.user().abbreviation) {
      return campCollaboration.user().abbreviation;
    }
    return initials(userDisplayName(campCollaboration.user()));
  }
  return initials(campCollaboration.inviteEmail || "");
}
const block0$o = (component) => {
  component.pdfStyle = { "responsibles-avatars": { "display": "flex", "flexDirection": "row", "alignItems": "flex-end" }, "responsibles-avatar": { "borderRadius": "6pt", "width": "12pt", "height": "12pt", "display": "flex", "flexDirection": "column", "justifyContent": "center" }, "responsibles-avatar-overlap": { "marginRight": "-2pt" }, "responsibles-initials": { "fontSize": "6pt", "textAlign": "center", "lineHeight": "1.2" } };
};
const _sfc_main$A = {
  name: "Responsibles",
  extends: PdfComponent,
  props: {
    activity: { type: Object, required: true },
    avatars: { type: Boolean, default: false }
  },
  computed: {
    last() {
      return this.activity.activityResponsibles().items.length - 1;
    },
    responsibles() {
      return this.activity.activityResponsibles().items.map(this.displayNameFor);
    }
  },
  methods: {
    colorFor(activityResponsible) {
      return campCollaborationColor(activityResponsible.campCollaboration());
    },
    fontColorFor(activityResponsible) {
      return contrastColor$1(this.colorFor(activityResponsible));
    },
    initialsFor(activityResponsible) {
      return campCollaborationInitials(activityResponsible.campCollaboration());
    },
    displayNameFor(activityResponsible) {
      return campCollaborationDisplayName(
        activityResponsible.campCollaboration(),
        this.$tc.bind(this)
      );
    }
  }
};
const _hoisted_1$v = {
  key: 0,
  class: "responsibles-avatars"
};
const _hoisted_2$i = {
  key: 1,
  style: { "display": "flex", "flex-direction": "row", "flex-wrap": "wrap" }
};
function _sfc_render$z(_ctx, _cache, $props, $setup, $data, $options) {
  return $props.avatars ? (openBlock(), createElementBlock("View", _hoisted_1$v, [
    (openBlock(true), createElementBlock(
      Fragment,
      null,
      renderList($props.activity.activityResponsibles().items, (activityResponsible, index) => {
        return openBlock(), createElementBlock(
          "View",
          {
            class: normalizeClass(["responsibles-avatar", { "responsibles-avatar-overlap": index !== $options.last }]),
            style: normalizeStyle({ backgroundColor: $options.colorFor(activityResponsible) })
          },
          [
            createBaseVNode(
              "Text",
              {
                class: "responsibles-initials",
                style: normalizeStyle({ color: $options.fontColorFor(activityResponsible) })
              },
              toDisplayString($options.initialsFor(activityResponsible)),
              5
              /* TEXT, STYLE */
            )
          ],
          6
          /* CLASS, STYLE */
        );
      }),
      256
      /* UNKEYED_FRAGMENT */
    ))
  ])) : (openBlock(), createElementBlock("View", _hoisted_2$i, [
    (openBlock(true), createElementBlock(
      Fragment,
      null,
      renderList($options.responsibles, (responsible, index) => {
        return openBlock(), createElementBlock("Text", null, [
          createTextVNode(
            toDisplayString(responsible),
            1
            /* TEXT */
          ),
          index + 1 < $options.responsibles.length ? (openBlock(), createElementBlock(
            Fragment,
            { key: 0 },
            [
              createTextVNode(", ")
            ],
            64
            /* STABLE_FRAGMENT */
          )) : createCommentVNode("v-if", true)
        ]);
      }),
      256
      /* UNKEYED_FRAGMENT */
    ))
  ]));
}
if (typeof block0$o === "function") block0$o(_sfc_main$A);
const Responsibles = /* @__PURE__ */ _export_sfc(_sfc_main$A, [["render", _sfc_render$z], ["__file", "/app/src/campPrint/Responsibles.vue"]]);
function contrastColor(color) {
  const input = new Color(color);
  const black = new Color("#000");
  const white = new Color("#fff");
  const blackContrast = Math.abs(input.contrast(black, "APCA"));
  const whiteContrast = Math.abs(input.contrast(white, "APCA"));
  return blackContrast > whiteContrast ? black.toString({ format: "hex" }) : white.toString({ format: "hex" });
}
const block0$n = (component) => {
  component.pdfStyle = { "picasso-schedule-entry-link": { "textDecoration": "none", "color": "black" }, "picasso-schedule-entry": { "position": "absolute", "padding": "0 4pt", "flexDirection": "column", "justifyContent": "flex-start" }, "picasso-schedule-entry-spacer": { "height": "0", "maxHeight": "4pt", "flexGrow": "1" }, "picasso-schedule-entry-title": { "height": "16pt", "lineHeight": "1", "flexGrow": "1" }, "picasso-schedule-entry-responsibles-container": { "position": "absolute", "top": "0", "bottom": "0", "right": "0", "left": "0", "flexDirection": "column", "alignItems": "flex-end", "justifyContent": "flex-end", "padding": "0 4pt" } };
};
const _sfc_main$z = {
  name: "ScheduleEntry",
  components: { Responsibles },
  extends: PdfComponent,
  props: {
    scheduleEntry: { type: Object, required: true },
    percentageHeight: { type: Number, default: 10 }
  },
  computed: {
    color() {
      return this.scheduleEntry.activity().category().color;
    },
    textColor() {
      return contrastColor(this.color);
    },
    category() {
      return this.scheduleEntry.activity().category().short;
    },
    title() {
      return this.scheduleEntry.activity().title;
    },
    linkTarget() {
      return `#scheduleEntry_${this.scheduleEntry.id}`;
    },
    fontSize() {
      return Math.min(8, 3 * this.percentageHeight) + "pt";
    }
  }
};
const _hoisted_1$u = ["href"];
const _hoisted_2$h = { class: "picasso-schedule-entry-responsibles-container" };
function _sfc_render$y(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_Responsibles = resolveComponent("Responsibles");
  return openBlock(), createElementBlock(
    "View",
    {
      class: "picasso-schedule-entry",
      style: normalizeStyle({ backgroundColor: $options.color })
    },
    [
      _cache[2] || (_cache[2] = createBaseVNode(
        "View",
        { class: "picasso-schedule-entry-spacer" },
        null,
        -1
        /* CACHED */
      )),
      createBaseVNode("Link", {
        class: "picasso-schedule-entry-link",
        href: $options.linkTarget
      }, [
        createBaseVNode(
          "Text",
          {
            class: "picasso-schedule-entry-title",
            style: normalizeStyle({ fontSize: $options.fontSize, color: $options.textColor })
          },
          toDisplayString($options.category) + " " + toDisplayString($props.scheduleEntry.number) + " " + toDisplayString($options.title),
          5
          /* TEXT, STYLE */
        )
      ], 8, _hoisted_1$u),
      _cache[3] || (_cache[3] = createBaseVNode(
        "View",
        { class: "picasso-schedule-entry-spacer" },
        null,
        -1
        /* CACHED */
      )),
      createBaseVNode("View", _hoisted_2$h, [
        _cache[0] || (_cache[0] = createBaseVNode(
          "View",
          { class: "picasso-schedule-entry-spacer" },
          null,
          -1
          /* CACHED */
        )),
        createVNode(_component_Responsibles, {
          class: "picasso-schedule-entry-responsibles",
          activity: $props.scheduleEntry.activity(),
          avatars: ""
        }, null, 8, ["activity"]),
        _cache[1] || (_cache[1] = createBaseVNode(
          "View",
          { class: "picasso-schedule-entry-spacer" },
          null,
          -1
          /* CACHED */
        ))
      ])
    ],
    4
    /* STYLE */
  );
}
if (typeof block0$n === "function") block0$n(_sfc_main$z);
const ScheduleEntry$1 = /* @__PURE__ */ _export_sfc(_sfc_main$z, [["render", _sfc_render$y], ["__file", "/app/src/campPrint/picasso/ScheduleEntry.vue"]]);
const FULL_WIDTH = 100;
function getVisuals(events) {
  const visuals = events.map((event) => ({
    id: event.id,
    columnCount: 0,
    column: 0,
    left: 0,
    width: 100,
    startTimestamp: dayjs.utc(event.start).unix(),
    endTimestamp: dayjs.utc(event.end).unix()
  }));
  visuals.sort((a2, b) => {
    return a2.startTimestamp - b.startTimestamp || b.endTimestamp - a2.endTimestamp;
  });
  return visuals;
}
function hasOverlap(s0, e0, s1, e1) {
  return !(s0 >= e1 || e0 <= s1);
}
function setColumnCount(groups) {
  groups.forEach((group) => {
    group.visuals.forEach((groupVisual) => {
      groupVisual.columnCount = groups.length;
      groupVisual.left = groupVisual.column * FULL_WIDTH / groupVisual.columnCount;
      groupVisual.width = FULL_WIDTH / groupVisual.columnCount;
    });
  });
}
function getOpenGroup(groups, eventStart, eventEnd) {
  for (let i2 = 0; i2 < groups.length; i2++) {
    const group = groups[i2];
    let intersected = false;
    if (hasOverlap(eventStart, eventEnd, group.start, group.end)) {
      for (let k = 0; k < group.visuals.length; k++) {
        const groupVisual = group.visuals[k];
        const groupStart = groupVisual.startTimestamp;
        const groupEnd = groupVisual.endTimestamp;
        if (hasOverlap(eventStart, eventEnd, groupStart, groupEnd)) {
          intersected = true;
          break;
        }
      }
    }
    if (!intersected) {
      return i2;
    }
  }
  return -1;
}
function arrange(scheduleEntries) {
  let groups = [];
  let min = -1;
  let max = -1;
  const visuals = getVisuals(scheduleEntries);
  visuals.forEach((visual) => {
    const eventStart = visual.startTimestamp;
    const eventEnd = visual.endTimestamp;
    if (groups.length > 0 && !hasOverlap(eventStart, eventEnd, min, max)) {
      setColumnCount(groups);
      groups = [];
      min = max = -1;
    }
    let targetGroup = getOpenGroup(groups, eventStart, eventEnd);
    if (targetGroup === -1) {
      targetGroup = groups.length;
      groups.push({ start: eventStart, end: eventEnd, visuals: [] });
    }
    const target = groups[targetGroup];
    target.visuals.push(visual);
    target.start = Math.min(target.start, eventStart);
    target.end = Math.max(target.end, eventEnd);
    visual.column = targetGroup;
    if (min === -1) {
      min = eventStart;
      max = eventEnd;
    } else {
      min = Math.min(min, eventStart);
      max = Math.max(max, eventEnd);
    }
  });
  setColumnCount(groups);
  return visuals;
}
function splitDaysIntoPages(days, maxDaysPerPage) {
  const numberOfDays = days.length;
  const numberOfPages = Math.ceil(numberOfDays / maxDaysPerPage);
  const daysPerPage = Math.floor(numberOfDays / numberOfPages);
  const numLargerPages = numberOfDays % numberOfPages;
  let nextUnassignedDayIndex = 0;
  return [...Array(numberOfPages).keys()].map((i2) => {
    const isLargerPage = i2 < numLargerPages;
    const numDaysOnCurrentPage = daysPerPage + (isLargerPage ? 1 : 0);
    const firstDayIndex = nextUnassignedDayIndex;
    nextUnassignedDayIndex = firstDayIndex + numDaysOnCurrentPage;
    return days.filter((day, index) => {
      return index >= firstDayIndex && index < nextUnassignedDayIndex;
    });
  });
}
function calculateBedtime(scheduleEntries, dayjs2, firstDayStart, lastDayEnd, timeBucketSize = 1) {
  const scheduleEntryBounds = getScheduleEntryBounds(
    scheduleEntries,
    dayjs2,
    firstDayStart.unix(),
    lastDayEnd.unix()
  );
  if (!scheduleEntryBounds.length) return { bedtime: 24, getUpTime: 0 };
  const gaps = scheduleEntryBounds.reduce((gaps2, current, index) => {
    if (index === 0) return gaps2;
    const previous = scheduleEntryBounds[index - 1];
    const duration = current.hours - previous.hours;
    if (duration === 0) return gaps2;
    gaps2.push({
      start: previous.hours,
      end: current.hours,
      duration
    });
    return gaps2;
  }, []);
  const { earliestBedtime, latestGetUpTime } = bedtimeConstraintsFromFirstAndLastDay(
    scheduleEntryBounds,
    firstDayStart,
    lastDayEnd
  );
  const largestBedtimeGap = maxBy(
    gaps.filter((gap) => {
      if (gap.start < earliestBedtime || gap.end > latestGetUpTime) return false;
      if (gap.start > 30 || gap.end < 24) return false;
      return true;
    }),
    (gap) => gap.duration
  );
  return {
    bedtime: optimalBedtime(largestBedtimeGap, scheduleEntryBounds, timeBucketSize),
    getUpTime: optimalGetUpTime(largestBedtimeGap, scheduleEntryBounds, timeBucketSize) - 24
  };
}
function getScheduleEntryBounds(scheduleEntries, dayjs2, firstDayStartTimestamp, lastDayEndTimestamp) {
  const bounds = scheduleEntries.flatMap((scheduleEntry) => {
    const start = dayjs2.utc(scheduleEntry.start);
    const end = dayjs2.utc(scheduleEntry.end);
    const startHours = start.hour() + start.minute() / 60;
    const endHours = end.hour() + end.minute() / 60;
    const duration = end.diff(start, "minute") / 60;
    return [
      { hours: startHours, time: start, type: "start", duration },
      { hours: endHours, time: end, type: "end", duration }
    ];
  }).filter(
    (bound) => bound.time.unix() >= firstDayStartTimestamp && bound.time.unix() <= lastDayEndTimestamp
  );
  const shifted = bounds.map((bound) => ({
    ...bound,
    hours: bound.hours + 24,
    time: bound.time.add(24, "hours")
  }));
  return sortBy([...bounds, ...shifted], (bound) => bound.hours);
}
function bedtimeConstraintsFromFirstAndLastDay(scheduleEntryBounds, firstDayStart, lastDayEnd) {
  const latestGetUpTime = earliestScheduleEntryBoundOnFirstDay(
    scheduleEntryBounds,
    firstDayStart
  );
  const earliestBedtime = latestScheduleEntryBoundOnLastDay(
    scheduleEntryBounds,
    lastDayEnd.subtract(1, "second")
  );
  return {
    earliestBedtime: earliestBedtime === void 0 ? 0 : earliestBedtime,
    latestGetUpTime: latestGetUpTime === void 0 ? 48 : latestGetUpTime + 24
  };
}
function earliestScheduleEntryBoundOnFirstDay(scheduleEntryBounds, firstDayStart) {
  const earliestBound = minBy(scheduleEntryBounds, (bound) => bound.time.unix());
  if (earliestBound.hours < 24 && earliestBound.time.diff(firstDayStart, "minute") / 60 < 24) {
    return earliestBound.hours;
  }
  return void 0;
}
function latestScheduleEntryBoundOnLastDay(scheduleEntryBounds, lastDayEnd) {
  const latestBound = maxBy(scheduleEntryBounds, (bound) => bound.time.unix());
  if (latestBound.hours < 24 && lastDayEnd.diff(latestBound.time, "minute") / 60 < 24) {
    return latestBound.hours;
  }
  return void 0;
}
function optimalBedtime(gap, scheduleEntryBounds, timeBucketSize) {
  const bedtime = Math.ceil(gap.start / timeBucketSize) * timeBucketSize;
  if (scheduleEntryBounds.some(
    (bound) => bound.type === "start" && bound.hours <= bedtime && bound.hours > bedtime - timeBucketSize / 2 && bound.duration > bedtime - bound.hours
  )) {
    return bedtime + timeBucketSize;
  }
  return bedtime;
}
function optimalGetUpTime(gap, scheduleEntryBounds, timeBucketSize) {
  const getUpTime = Math.floor(gap.end / timeBucketSize) * timeBucketSize;
  if (scheduleEntryBounds.some(
    (bound) => bound.type === "end" && bound.hours >= getUpTime && bound.hours < getUpTime + timeBucketSize / 2 && bound.duration > bound.hours - getUpTime
  )) {
    return getUpTime - timeBucketSize;
  }
  return getUpTime;
}
function times(getUpTime, bedTime, timeStep) {
  const times2 = [[getUpTime - timeStep / 2, 0.5]];
  for (let i2 = 0; getUpTime + i2 * timeStep < bedTime; i2++) {
    const weight = 1;
    times2.push([getUpTime + i2 * timeStep, weight]);
  }
  times2.push([bedTime, 0.5]);
  times2.push([bedTime + timeStep / 2, 0]);
  return times2;
}
function timesWeightsSum(times2) {
  return times2.reduce((sum, [_, weight]) => sum + weight, 0);
}
function positionPercentage(milliseconds, times2) {
  const hours = milliseconds / (3600 * 1e3);
  let matchingTimeIndex = times2.findIndex(([time, _]) => time > hours) - 1;
  matchingTimeIndex = Math.min(
    Math.max(matchingTimeIndex === -2 ? times2.length : matchingTimeIndex, 0),
    times2.length - 1
  );
  const remainder = times2[matchingTimeIndex][1] !== 0 ? (hours - times2[matchingTimeIndex][0]) / times2[matchingTimeIndex][1] : 0;
  const positionWeightsSum = timesWeightsSum(times2.slice(0, matchingTimeIndex)) + remainder * times2[Math.min(matchingTimeIndex, times2.length)][1];
  const totalWeightsSum = timesWeightsSum(times2);
  if (totalWeightsSum === 0) {
    return 0;
  }
  const result = positionWeightsSum * 100 / totalWeightsSum;
  return Math.max(0, Math.min(100, result));
}
function filterScheduleEntriesByDay(scheduleEntries, day, times2) {
  return scheduleEntries.filter((scheduleEntry) => {
    return dayjs.utc(scheduleEntry.start).isBefore(dayEnd(day, times2)) && dayjs.utc(scheduleEntry.end).isAfter(dayStart(day, times2));
  });
}
function dayOffset(day, offset) {
  return dayjs.utc(day.start).add(offset, "hour");
}
function dayStart(day, times2) {
  const dayStart2 = times2[0][0];
  return dayOffset(day, dayStart2);
}
function dayEnd(day, times2) {
  const dayEnd2 = times2[times2.length - 1][0];
  return dayOffset(day, dayEnd2);
}
function leftAndWith(scheduleEntries) {
  return keyBy(arrange(scheduleEntries), "id");
}
function positionStyles(scheduleEntries, day, times2) {
  const leftAndWidth = leftAndWith(scheduleEntries);
  return keyBy(
    scheduleEntries.map((scheduleEntry) => {
      var _a, _b;
      const left = ((_a = leftAndWidth[scheduleEntry.id]) == null ? void 0 : _a.left) || 0;
      const width = ((_b = leftAndWidth[scheduleEntry.id]) == null ? void 0 : _b.width) || 0;
      const top = positionPercentage(
        dayjs.utc(scheduleEntry.start).valueOf() - dayjs.utc(day.start).valueOf(),
        times2
      );
      const bottom = 100 - positionPercentage(
        dayjs.utc(scheduleEntry.end).valueOf() - dayjs.utc(day.start).valueOf(),
        times2
      );
      return {
        id: scheduleEntry.id,
        top: top + "%",
        bottom: bottom + "%",
        left: left + "%",
        right: 100 - width - left + "%",
        percentageHeight: 100 - bottom - top
      };
    }),
    "id"
  );
}
const block0$m = (component) => {
  component.pdfStyle = { "picasso-day-column": { "flexBasis": "0", "flexGrow": "1", "display": "flex", "flexDirection": "column", "overflow": "hidden", "position": "relative" }, "picasso-day-column-grid": { "width": "100%", "height": "100%", "display": "flex", "flexDirection": "column", "overflow": "hidden", "position": "relative" }, "picasso-day-column-grid-row": { "display": "flex", "flexBasis": "0" }, "picasso-day-column-grid-row-grey": { "backgroundColor": "lightgrey" }, "picasso-day-column-schedule-entry-container": { "margin": "0 2pt", "position": "absolute", "top": "0", "bottom": "0", "left": "0", "right": "0" } };
};
const _sfc_main$y = {
  name: "DayColumn",
  components: { ScheduleEntry: ScheduleEntry$1 },
  extends: PdfComponent,
  props: {
    times: { type: Array, required: true },
    day: { type: Object, required: true },
    scheduleEntries: { type: Array, default: () => [] }
  },
  computed: {
    relevantScheduleEntries() {
      return filterScheduleEntriesByDay(this.scheduleEntries, this.day, this.times);
    },
    positionStyles() {
      return positionStyles(this.relevantScheduleEntries, this.day, this.times);
    },
    borderRadiusStyles() {
      const radius = "2pt";
      return keyBy(
        this.relevantScheduleEntries.map((scheduleEntry) => {
          const start = this.$date.utc(scheduleEntry.start);
          const startsOnThisDay = start.isSameOrAfter(dayStart(this.day, this.times)) && start.isSameOrBefore(dayEnd(this.day, this.times));
          const topStyles = startsOnThisDay ? { borderTopRightRadius: radius, borderTopLeftRadius: radius } : {};
          const end = this.$date.utc(scheduleEntry.end);
          const endsOnThisDay = end.isSameOrAfter(dayStart(this.day, this.times)) && end.isSameOrBefore(dayEnd(this.day, this.times));
          const bottomStyles = endsOnThisDay ? { borderBottomRightRadius: radius, borderBottomLeftRadius: radius } : {};
          return { id: scheduleEntry.id, ...bottomStyles, ...topStyles };
        }),
        "id"
      );
    }
  }
};
const _hoisted_1$t = { class: "picasso-day-column" };
const _hoisted_2$g = { class: "picasso-day-column-grid" };
const _hoisted_3$a = { class: "picasso-day-column-schedule-entry-container" };
function _sfc_render$x(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_ScheduleEntry = resolveComponent("ScheduleEntry");
  return openBlock(), createElementBlock("View", _hoisted_1$t, [
    createBaseVNode("View", _hoisted_2$g, [
      (openBlock(true), createElementBlock(
        Fragment,
        null,
        renderList($props.times, ([_, weight], index) => {
          return openBlock(), createElementBlock(
            "View",
            {
              class: normalizeClass(["picasso-day-column-grid-row", { "picasso-day-column-grid-row-grey": index % 2 === 1 }]),
              style: normalizeStyle({ flexGrow: weight })
            },
            null,
            6
            /* CLASS, STYLE */
          );
        }),
        256
        /* UNKEYED_FRAGMENT */
      ))
    ]),
    createBaseVNode("View", _hoisted_3$a, [
      (openBlock(true), createElementBlock(
        Fragment,
        null,
        renderList($options.relevantScheduleEntries, (scheduleEntry) => {
          return openBlock(), createBlock(_component_ScheduleEntry, {
            "schedule-entry": scheduleEntry,
            style: normalizeStyle({
              ...$options.positionStyles[scheduleEntry.id],
              ...$options.borderRadiusStyles[scheduleEntry.id]
            }),
            "percentage-height": $options.positionStyles[scheduleEntry.id].percentageHeight
          }, null, 8, ["schedule-entry", "style", "percentage-height"]);
        }),
        256
        /* UNKEYED_FRAGMENT */
      ))
    ])
  ]);
}
if (typeof block0$m === "function") block0$m(_sfc_main$y);
const DayColumn = /* @__PURE__ */ _export_sfc(_sfc_main$y, [["render", _sfc_render$x], ["__file", "/app/src/campPrint/picasso/DayColumn.vue"]]);
const block0$l = (component) => {
  component.pdfStyle = { "category-label": { "padding": "2pt 8pt", "borderRadius": "18pt", "alignSelf": "center" } };
};
const _sfc_main$x = {
  name: "CategoryLabel",
  extends: PdfComponent,
  props: {
    category: { type: Object, required: true }
  },
  computed: {
    backgroundColor() {
      return this.category.color;
    },
    color() {
      return contrastColor(this.backgroundColor);
    }
  }
};
function _sfc_render$w(_ctx, _cache, $props, $setup, $data, $options) {
  return openBlock(), createElementBlock(
    "Text",
    {
      class: "category-label",
      style: normalizeStyle({ color: $options.color, backgroundColor: $options.backgroundColor })
    },
    toDisplayString($props.category.short),
    5
    /* TEXT, STYLE */
  );
}
if (typeof block0$l === "function") block0$l(_sfc_main$x);
const CategoryLabel = /* @__PURE__ */ _export_sfc(_sfc_main$x, [["render", _sfc_render$w], ["__file", "/app/src/campPrint/CategoryLabel.vue"]]);
const block0$k = (component) => {
  component.pdfStyle = { "picasso-categories": { "fontSize": "9pt", "display": "flex", "flexDirection": "row", "flexWrap": "wrap", "margin": "2pt 0 0" }, "picasso-category": { "flexDirection": "row", "alignItems": "center", "gap": "2pt", "marginRight": "6pt", "marginBottom": "3pt" } };
};
const _sfc_main$w = {
  name: "Categories",
  components: { CategoryLabel },
  extends: PdfComponent,
  props: {
    period: { type: Object, required: true }
  },
  computed: {
    categories() {
      return this.period.camp().categories().items;
    }
  }
};
const _hoisted_1$s = { class: "picasso-categories" };
const _hoisted_2$f = { class: "picasso-category" };
function _sfc_render$v(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_CategoryLabel = resolveComponent("CategoryLabel");
  return openBlock(), createElementBlock("View", _hoisted_1$s, [
    (openBlock(true), createElementBlock(
      Fragment,
      null,
      renderList($options.categories, (category) => {
        return openBlock(), createElementBlock("View", _hoisted_2$f, [
          createVNode(_component_CategoryLabel, {
            category,
            style: { "margin-right": "2pt" }
          }, null, 8, ["category"]),
          createBaseVNode(
            "Text",
            null,
            toDisplayString(category.name),
            1
            /* TEXT */
          )
        ]);
      }),
      256
      /* UNKEYED_FRAGMENT */
    ))
  ]);
}
if (typeof block0$k === "function") block0$k(_sfc_main$w);
const Categories = /* @__PURE__ */ _export_sfc(_sfc_main$w, [["render", _sfc_render$v], ["__file", "/app/src/campPrint/picasso/Categories.vue"]]);
function userLegalName(user) {
  if (!user || !user.profile() || user.profile()._meta.loading) return "";
  return user.profile().legalName || "";
}
function campCollaborationLegalName(campCollaboration) {
  if (!campCollaboration) {
    return "";
  }
  return typeof campCollaboration.user === "function" ? userLegalName(campCollaboration.user()) : "";
}
const block0$j = (component) => {
  component.pdfStyle = { "picasso-footer": { "width": "100%", "fontSize": "9pt", "display": "flex", "flexDirection": "row", "marginTop": "6pt", "border": "1pt solid grey", "padding": "0 0 3pt", "justifyContent": "space-between" }, "picasso-footer-column": { "maxWidth": "33%", "alignItems": "flex-start", "justifyContent": "flex-start", "gap": "6pt", "lineHeight": "0.5", "padding": "2pt 3pt 3pt" }, "picasso-footer-field": { "marginBottom": "6pt" } };
};
const _sfc_main$v = {
  name: "PicassoFooter",
  extends: PdfComponent,
  props: {
    period: { type: Object, required: true },
    locale: { type: String, default: "en" }
  },
  computed: {
    camp() {
      return this.period.camp();
    },
    address() {
      return this.joinWithoutBlanks([
        this.camp.addressName,
        this.camp.addressStreet,
        this.joinWithoutBlanks([this.camp.addressZipcode, this.camp.addressCity], " ")
      ]);
    },
    dates() {
      const startDate = this.$date.utc(this.period.start).hour(0).minute(0).second(0);
      const endDate = this.$date.utc(this.period.end).hour(0).minute(0).second(0);
      return this.$date.formatDatePeriod(
        startDate,
        endDate,
        this.$t("global.datetime.dateLong"),
        this.locale
      );
    },
    leaders() {
      const leaders = this.camp.campCollaborations().items.filter((campCollaboration) => {
        return campCollaboration.status === "established" && campCollaboration.role === "manager";
      });
      const leaderNames = leaders.map((campCollaboration) => {
        return campCollaborationLegalName(campCollaboration);
      });
      if ("Intl" in self && "ListFormat" in Intl) {
        return new Intl.ListFormat(this.locale, { style: "short" }).format(leaderNames);
      }
      return leaderNames.join(", ");
    }
  },
  methods: {
    joinWithoutBlanks(list, separator = ", ") {
      return list.filter((element) => !!element).join(separator);
    }
  }
};
const _hoisted_1$r = { class: "picasso-footer" };
const _hoisted_2$e = { class: "picasso-footer-column" };
const _hoisted_3$9 = {
  key: 0,
  class: "picasso-footer-field"
};
const _hoisted_4$6 = {
  key: 1,
  class: "picasso-footer-field"
};
const _hoisted_5$5 = {
  key: 2,
  class: "picasso-footer-field"
};
const _hoisted_6$5 = { class: "picasso-footer-column" };
const _hoisted_7$2 = {
  key: 0,
  class: "picasso-footer-field"
};
const _hoisted_8$2 = {
  key: 1,
  class: "picasso-footer-field"
};
const _hoisted_9$2 = { class: "picasso-footer-column" };
const _hoisted_10$1 = { class: "picasso-footer-field" };
const _hoisted_11$1 = {
  key: 0,
  class: "picasso-footer-field"
};
const _hoisted_12 = {
  key: 1,
  class: "picasso-footer-field"
};
function _sfc_render$u(_ctx, _cache, $props, $setup, $data, $options) {
  return openBlock(), createElementBlock("View", _hoisted_1$r, [
    createBaseVNode("View", _hoisted_2$e, [
      $options.camp.courseKind || $options.camp.kind ? (openBlock(), createElementBlock(
        "Text",
        _hoisted_3$9,
        toDisplayString($options.joinWithoutBlanks([$options.camp.courseKind, $options.camp.kind])),
        1
        /* TEXT */
      )) : createCommentVNode("v-if", true),
      $options.camp.courseNumber ? (openBlock(), createElementBlock(
        "Text",
        _hoisted_4$6,
        toDisplayString(_ctx.$t("print.picasso.picassoFooter.courseNumber", {
          courseNumber: $options.camp.courseNumber
        })),
        1
        /* TEXT */
      )) : createCommentVNode("v-if", true),
      $options.camp.motto ? (openBlock(), createElementBlock(
        "Text",
        _hoisted_5$5,
        toDisplayString($options.camp.motto),
        1
        /* TEXT */
      )) : createCommentVNode("v-if", true)
    ]),
    createBaseVNode("View", _hoisted_6$5, [
      $options.address ? (openBlock(), createElementBlock(
        "Text",
        _hoisted_7$2,
        toDisplayString($options.address),
        1
        /* TEXT */
      )) : createCommentVNode("v-if", true),
      $options.dates ? (openBlock(), createElementBlock(
        "Text",
        _hoisted_8$2,
        toDisplayString($options.dates),
        1
        /* TEXT */
      )) : createCommentVNode("v-if", true)
    ]),
    createBaseVNode("View", _hoisted_9$2, [
      createBaseVNode(
        "Text",
        _hoisted_10$1,
        toDisplayString(_ctx.$t("print.picasso.picassoFooter.leaders", { leaders: $options.leaders })),
        1
        /* TEXT */
      ),
      $options.camp.coachName ? (openBlock(), createElementBlock(
        "Text",
        _hoisted_11$1,
        toDisplayString(_ctx.$t("print.picasso.picassoFooter.coach", { coach: $options.camp.coachName })),
        1
        /* TEXT */
      )) : createCommentVNode("v-if", true),
      $options.camp.trainingAdvisorName ? (openBlock(), createElementBlock(
        "Text",
        _hoisted_12,
        toDisplayString(_ctx.$t("print.picasso.picassoFooter.trainingAdvisor", {
          trainingAdvisor: $options.camp.trainingAdvisorName
        })),
        1
        /* TEXT */
      )) : createCommentVNode("v-if", true)
    ])
  ]);
}
if (typeof block0$j === "function") block0$j(_sfc_main$v);
const PicassoFooter = /* @__PURE__ */ _export_sfc(_sfc_main$v, [["render", _sfc_render$u], ["__file", "/app/src/campPrint/picasso/PicassoFooter.vue"]]);
const block0$i = (component) => {
  component.pdfStyle = { "picasso-title-container": { "display": "flex", "flexDirection": "row", "marginTop": "-4pt", "marginBottom": "4pt", "alignItems": "center", "gap": "8pt" }, "picasso-title": { "flexGrow": "1", "fontWeight": "bold", "fontSize": "14pt" }, "picasso-organizer": { "fontSize": "10pt" }, "picasso-ys-logo": { "alignSelf": "flex-end", "marginTop": "3pt", "size": "20" }, "picasso-calendar-header-container": { "borderLeft": "1pt solid white", "borderRight": "1pt solid white", "flexGrow": "1", "display": "flex", "flexDirection": "row", "lineHeight": "0.8" }, "picasso-calendar-container": { "border": "1pt solid grey", "flexGrow": "1", "display": "flex", "flexDirection": "row" }, "picasso-day-header": { "borderRight": "1pt solid white", "flexBasis": "0", "flexGrow": "1", "overflow": "hidden", "padding": "4pt 0 5pt", "display": "flex", "flexDirection": "column" }, "picasso-day-header-left-border": { "borderLeft": "1pt solid white" }, "picasso-time-column": { "flexGrow": "0", "flexShrink": "0", "display": "flex", "flexDirection": "column" }, "picasso-time-column-text": { "fontSize": "8pt" }, "picasso-day-column-left-border": { "borderLeft": "1pt solid grey" } };
};
const _sfc_main$u = {
  name: "PicassoPage",
  components: {
    YSLogo,
    TimeColumnSpacer,
    DayHeader,
    TimeColumn,
    DayColumn,
    Categories,
    PicassoFooter
  },
  extends: PdfComponent,
  props: {
    config: { type: Object, required: true },
    content: { type: Object, required: true },
    period: { type: Object, required: true },
    days: { type: Object, required: true },
    bedtime: { type: Number, default: 0 },
    getUpTime: { type: Number, default: 24 },
    timeStep: { type: Number, default: 1 }
  },
  computed: {
    bookmark() {
      return {
        title: this.$t("print.picasso.title", {
          period: this.period.description
        }),
        fit: true
      };
    },
    times() {
      return times(this.getUpTime, this.bedtime, this.timeStep);
    },
    orientation() {
      return this.content.options.orientation === "L" ? "landscape" : "portrait";
    },
    anyDayResponsibles() {
      return this.days.some((day) => filterDayResponsiblesByDay(day).length > 0);
    },
    scheduleEntries() {
      return this.period.scheduleEntries().items.filter((scheduleEntry) => {
        return filterMatchScheduleEntry(scheduleEntry, this.content.options.filter);
      });
    }
  }
};
const _hoisted_1$q = ["orientation"];
const _hoisted_2$d = { class: "picasso-title-container" };
const _hoisted_3$8 = ["id", "bookmark"];
const _hoisted_4$5 = { class: "picasso-organizer" };
const _hoisted_5$4 = { class: "picasso-calendar-header-container" };
const _hoisted_6$4 = { class: "picasso-calendar-container" };
function _sfc_render$t(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_YSLogo = resolveComponent("YSLogo");
  const _component_TimeColumnSpacer = resolveComponent("TimeColumnSpacer");
  const _component_DayHeader = resolveComponent("DayHeader");
  const _component_TimeColumn = resolveComponent("TimeColumn");
  const _component_DayColumn = resolveComponent("DayColumn");
  const _component_Categories = resolveComponent("Categories");
  const _component_PicassoFooter = resolveComponent("PicassoFooter");
  return openBlock(), createElementBlock("Page", {
    size: "A4",
    orientation: $options.orientation,
    class: "page"
  }, [
    createBaseVNode("View", _hoisted_2$d, [
      $props.period.camp().printYSLogoOnPicasso ? (openBlock(), createBlock(_component_YSLogo, {
        key: 0,
        size: 20,
        locale: $props.config.language,
        class: "picasso-ys-logo"
      }, null, 8, ["locale"])) : createCommentVNode("v-if", true),
      createBaseVNode("Text", {
        id: `${_ctx.id}-${$props.period.id}`,
        bookmark: $options.bookmark,
        class: "picasso-title"
      }, toDisplayString(_ctx.$t("print.picasso.title", { period: $props.period.description })), 9, _hoisted_3$8),
      createBaseVNode(
        "Text",
        _hoisted_4$5,
        toDisplayString($props.period.camp().organizer),
        1
        /* TEXT */
      )
    ]),
    createBaseVNode("View", _hoisted_5$4, [
      createVNode(_component_TimeColumnSpacer, {
        times: $options.times.slice(0, $options.times.length - 1)
      }, null, 8, ["times"]),
      (openBlock(true), createElementBlock(
        Fragment,
        null,
        renderList($props.days, (day) => {
          return openBlock(), createBlock(_component_DayHeader, {
            day,
            "show-day-responsibles": $options.anyDayResponsibles,
            class: normalizeClass(["picasso-day-header", { "picasso-day-header-left-border": day.id === $props.days[0].id }])
          }, null, 8, ["day", "show-day-responsibles", "class"]);
        }),
        256
        /* UNKEYED_FRAGMENT */
      )),
      createVNode(_component_TimeColumnSpacer, {
        times: $options.times.slice(0, $options.times.length - 1)
      }, null, 8, ["times"])
    ]),
    createBaseVNode("View", _hoisted_6$4, [
      createVNode(_component_TimeColumn, {
        times: $options.times.slice(0, $options.times.length - 1),
        align: "right"
      }, null, 8, ["times"]),
      (openBlock(true), createElementBlock(
        Fragment,
        null,
        renderList($props.days, (day) => {
          return openBlock(), createBlock(_component_DayColumn, {
            times: $options.times,
            day,
            "schedule-entries": $options.scheduleEntries,
            class: normalizeClass({ "picasso-day-column-left-border": day.id === $props.days[0].id })
          }, null, 8, ["times", "day", "schedule-entries", "class"]);
        }),
        256
        /* UNKEYED_FRAGMENT */
      )),
      createVNode(_component_TimeColumn, {
        times: $options.times.slice(0, $options.times.length - 1),
        align: "left"
      }, null, 8, ["times"])
    ]),
    createVNode(_component_Categories, { period: $props.period }, null, 8, ["period"]),
    createVNode(_component_PicassoFooter, {
      period: $props.period,
      locale: $props.config.locale
    }, null, 8, ["period", "locale"])
  ], 8, _hoisted_1$q);
}
if (typeof block0$i === "function") block0$i(_sfc_main$u);
const PicassoPage = /* @__PURE__ */ _export_sfc(_sfc_main$u, [["render", _sfc_render$t], ["__file", "/app/src/campPrint/picasso/PicassoPage.vue"]]);
const _sfc_main$t = {
  name: "PicassoPeriod",
  components: { PicassoPage },
  extends: PdfComponent,
  props: {
    config: { type: Object, required: true },
    content: { type: Object, required: true },
    period: { type: Object, required: true },
    filter: { type: Object, default: () => ({}) }
  },
  computed: {
    days() {
      var _a;
      const dayFilter = (_a = this.content.options.filter) == null ? void 0 : _a.day;
      return sortBy(
        this.period.days().items,
        (day) => this.$date.utc(day.start).unix()
      ).filter((day) => {
        if (!dayFilter || dayFilter.length === 0) return true;
        return dayFilter.includes(day._meta.self);
      });
    },
    scheduleEntries() {
      return this.period.scheduleEntries().items.filter((scheduleEntry) => {
        return filterMatchScheduleEntry(scheduleEntry, this.content.options.filter);
      });
    },
    pages() {
      const maxDaysPerPage = this.content.options.orientation === "L" ? 8 : 4;
      return splitDaysIntoPages(this.days, maxDaysPerPage);
    },
    timeStep() {
      return 1;
    },
    bedtimes() {
      return calculateBedtime(
        this.scheduleEntries,
        this.$date,
        this.$date.utc(this.days[0].start),
        this.$date.utc(this.days[this.days.length - 1].end),
        this.timeStep
      );
    }
  }
};
function _sfc_render$s(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_PicassoPage = resolveComponent("PicassoPage");
  return openBlock(true), createElementBlock(
    Fragment,
    null,
    renderList($options.pages, (pageDays) => {
      return openBlock(), createBlock(_component_PicassoPage, {
        id: _ctx.id,
        config: $props.config,
        content: $props.content,
        period: $props.period,
        days: pageDays,
        bedtime: $options.bedtimes.bedtime,
        "get-up-time": $options.bedtimes.getUpTime,
        "time-step": $options.timeStep,
        filter: $props.filter
      }, null, 8, ["id", "config", "content", "period", "days", "bedtime", "get-up-time", "time-step", "filter"]);
    }),
    256
    /* UNKEYED_FRAGMENT */
  );
}
const PicassoPeriod = /* @__PURE__ */ _export_sfc(_sfc_main$t, [["render", _sfc_render$s], ["__file", "/app/src/campPrint/picasso/PicassoPeriod.vue"]]);
const _sfc_main$s = {
  name: "Picasso",
  components: { PicassoPeriod },
  extends: PdfComponent,
  props: {
    config: { type: Object, required: true },
    content: { type: Object, required: true }
  },
  computed: {
    periods() {
      var _a;
      return (((_a = this.content.options) == null ? void 0 : _a.periods) || []).map((periodUri) => {
        return this.api.get(periodUri);
      });
    }
  }
};
function _sfc_render$r(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_PicassoPeriod = resolveComponent("PicassoPeriod");
  return openBlock(true), createElementBlock(
    Fragment,
    null,
    renderList($options.periods, (period) => {
      return openBlock(), createBlock(_component_PicassoPeriod, {
        id: _ctx.id,
        config: $props.config,
        content: $props.content,
        period
      }, null, 8, ["id", "config", "content", "period"]);
    }),
    256
    /* UNKEYED_FRAGMENT */
  );
}
const Picasso = /* @__PURE__ */ _export_sfc(_sfc_main$s, [["render", _sfc_render$r], ["__file", "/app/src/campPrint/picasso/Picasso.vue"]]);
function dateLong(dateTimeString, tc) {
  return dayjs.utc(dateTimeString).format(tc("global.datetime.dateLong"));
}
function getDefaultExportFromCjs(x) {
  return x && x.__esModule && Object.prototype.hasOwnProperty.call(x, "default") ? x["default"] : x;
}
var voidElements;
var hasRequiredVoidElements;
function requireVoidElements() {
  if (hasRequiredVoidElements) return voidElements;
  hasRequiredVoidElements = 1;
  voidElements = {
    "area": true,
    "base": true,
    "br": true,
    "col": true,
    "embed": true,
    "hr": true,
    "img": true,
    "input": true,
    "link": true,
    "meta": true,
    "param": true,
    "source": true,
    "track": true,
    "wbr": true
  };
  return voidElements;
}
var voidElementsExports = requireVoidElements();
const e = /* @__PURE__ */ getDefaultExportFromCjs(voidElementsExports);
var t = /\s([^'"/\s><]+?)[\s/>]|([^\s=]+)=\s?(".*?"|'.*?')/g;
function n(n2) {
  var r2 = { type: "tag", name: "", voidElement: false, attrs: {}, children: [] }, i2 = n2.match(/<\/?([^\s]+?)[/\s>]/);
  if (i2 && (r2.name = i2[1], (e[i2[1]] || "/" === n2.charAt(n2.length - 2)) && (r2.voidElement = true), r2.name.startsWith("!--"))) {
    var s2 = n2.indexOf("-->");
    return { type: "comment", comment: -1 !== s2 ? n2.slice(4, s2) : "" };
  }
  for (var a2 = new RegExp(t), c2 = null; null !== (c2 = a2.exec(n2)); ) if (c2[0].trim()) if (c2[1]) {
    var o = c2[1].trim(), l = [o, ""];
    o.indexOf("=") > -1 && (l = o.split("=")), r2.attrs[l[0]] = l[1], a2.lastIndex--;
  } else c2[2] && (r2.attrs[c2[2]] = c2[3].trim().substring(1, c2[3].length - 1));
  return r2;
}
var r = /<[a-zA-Z0-9\-\!\/](?:"[^"]*"|'[^']*'|[^'">])*>/g, i = /^\s*$/, s = /* @__PURE__ */ Object.create(null);
function a(e2, t2) {
  switch (t2.type) {
    case "text":
      return e2 + t2.content;
    case "tag":
      return e2 += "<" + t2.name + (t2.attrs ? function(e3) {
        var t3 = [];
        for (var n2 in e3) t3.push(n2 + '="' + e3[n2] + '"');
        return t3.length ? " " + t3.join(" ") : "";
      }(t2.attrs) : "") + (t2.voidElement ? "/>" : ">"), t2.voidElement ? e2 : e2 + t2.children.reduce(a, "") + "</" + t2.name + ">";
    case "comment":
      return e2 + "<!--" + t2.comment + "-->";
  }
}
var c = { parse: function(e2, t2) {
  t2 || (t2 = {}), t2.components || (t2.components = s);
  var a2, c2 = [], o = [], l = -1, m = false;
  if (0 !== e2.indexOf("<")) {
    var u = e2.indexOf("<");
    c2.push({ type: "text", content: -1 === u ? e2 : e2.substring(0, u) });
  }
  return e2.replace(r, function(r2, s2) {
    if (m) {
      if (r2 !== "</" + a2.name + ">") return;
      m = false;
    }
    var u2, f = "/" !== r2.charAt(1), h2 = r2.startsWith("<!--"), p = s2 + r2.length, d = e2.charAt(p);
    if (h2) {
      var v = n(r2);
      return l < 0 ? (c2.push(v), c2) : ((u2 = o[l]).children.push(v), c2);
    }
    if (f && (l++, "tag" === (a2 = n(r2)).type && t2.components[a2.name] && (a2.type = "component", m = true), a2.voidElement || m || !d || "<" === d || a2.children.push({ type: "text", content: e2.slice(p, e2.indexOf("<", p)) }), 0 === l && c2.push(a2), (u2 = o[l - 1]) && u2.children.push(a2), o[l] = a2), (!f || a2.voidElement) && (l > -1 && (a2.voidElement || a2.name === r2.slice(2, -1)) && (l--, a2 = -1 === l ? c2 : o[l]), !m && "<" !== d && d)) {
      u2 = -1 === l ? c2 : o[l].children;
      var x = e2.indexOf("<", p), g = e2.slice(p, -1 === x ? void 0 : x);
      i.test(g) && (g = " "), (x > -1 && l + u2.length >= 0 || " " !== g) && u2.push({ type: "text", content: g });
    }
  }), c2;
}, stringify: function(e2) {
  return e2.reduce(function(e3, t2) {
    return e3 + a("", t2);
  }, "");
} };
var __assign$1 = function() {
  __assign$1 = Object.assign || function(t2) {
    for (var s2, i2 = 1, n2 = arguments.length; i2 < n2; i2++) {
      s2 = arguments[i2];
      for (var p in s2) if (Object.prototype.hasOwnProperty.call(s2, p))
        t2[p] = s2[p];
    }
    return t2;
  };
  return __assign$1.apply(this, arguments);
};
var pairDivider = "~";
var blockDivider = "~~";
function generateNamedReferences(input, prev) {
  var entities = {};
  var characters = {};
  var blocks = input.split(blockDivider);
  var isOptionalBlock = false;
  for (var i2 = 0; blocks.length > i2; i2++) {
    var entries = blocks[i2].split(pairDivider);
    for (var j = 0; j < entries.length; j += 2) {
      var entity = entries[j];
      var character = entries[j + 1];
      var fullEntity = "&" + entity + ";";
      entities[fullEntity] = character;
      if (isOptionalBlock) {
        entities["&" + entity] = character;
      }
      characters[character] = fullEntity;
    }
    isOptionalBlock = true;
  }
  return prev ? { entities: __assign$1(__assign$1({}, entities), prev.entities), characters: __assign$1(__assign$1({}, characters), prev.characters) } : { entities, characters };
}
var bodyRegExps = {
  xml: /&(?:#\d+|#[xX][\da-fA-F]+|[0-9a-zA-Z]+);?/g,
  html4: /&notin;|&(?:nbsp|iexcl|cent|pound|curren|yen|brvbar|sect|uml|copy|ordf|laquo|not|shy|reg|macr|deg|plusmn|sup2|sup3|acute|micro|para|middot|cedil|sup1|ordm|raquo|frac14|frac12|frac34|iquest|Agrave|Aacute|Acirc|Atilde|Auml|Aring|AElig|Ccedil|Egrave|Eacute|Ecirc|Euml|Igrave|Iacute|Icirc|Iuml|ETH|Ntilde|Ograve|Oacute|Ocirc|Otilde|Ouml|times|Oslash|Ugrave|Uacute|Ucirc|Uuml|Yacute|THORN|szlig|agrave|aacute|acirc|atilde|auml|aring|aelig|ccedil|egrave|eacute|ecirc|euml|igrave|iacute|icirc|iuml|eth|ntilde|ograve|oacute|ocirc|otilde|ouml|divide|oslash|ugrave|uacute|ucirc|uuml|yacute|thorn|yuml|quot|amp|lt|gt|#\d+|#[xX][\da-fA-F]+|[0-9a-zA-Z]+);?/g,
  html5: /&centerdot;|&copysr;|&divideontimes;|&gtcc;|&gtcir;|&gtdot;|&gtlPar;|&gtquest;|&gtrapprox;|&gtrarr;|&gtrdot;|&gtreqless;|&gtreqqless;|&gtrless;|&gtrsim;|&ltcc;|&ltcir;|&ltdot;|&lthree;|&ltimes;|&ltlarr;|&ltquest;|&ltrPar;|&ltri;|&ltrie;|&ltrif;|&notin;|&notinE;|&notindot;|&notinva;|&notinvb;|&notinvc;|&notni;|&notniva;|&notnivb;|&notnivc;|&parallel;|&timesb;|&timesbar;|&timesd;|&(?:AElig|AMP|Aacute|Acirc|Agrave|Aring|Atilde|Auml|COPY|Ccedil|ETH|Eacute|Ecirc|Egrave|Euml|GT|Iacute|Icirc|Igrave|Iuml|LT|Ntilde|Oacute|Ocirc|Ograve|Oslash|Otilde|Ouml|QUOT|REG|THORN|Uacute|Ucirc|Ugrave|Uuml|Yacute|aacute|acirc|acute|aelig|agrave|amp|aring|atilde|auml|brvbar|ccedil|cedil|cent|copy|curren|deg|divide|eacute|ecirc|egrave|eth|euml|frac12|frac14|frac34|gt|iacute|icirc|iexcl|igrave|iquest|iuml|laquo|lt|macr|micro|middot|nbsp|not|ntilde|oacute|ocirc|ograve|ordf|ordm|oslash|otilde|ouml|para|plusmn|pound|quot|raquo|reg|sect|shy|sup1|sup2|sup3|szlig|thorn|times|uacute|ucirc|ugrave|uml|uuml|yacute|yen|yuml|#\d+|#[xX][\da-fA-F]+|[0-9a-zA-Z]+);?/g
};
var namedReferences = {};
namedReferences["xml"] = generateNamedReferences(`lt~<~gt~>~quot~"~apos~'~amp~&`);
namedReferences["html4"] = generateNamedReferences(`apos~'~OElig~Œ~oelig~œ~Scaron~Š~scaron~š~Yuml~Ÿ~circ~ˆ~tilde~˜~ensp~ ~emsp~ ~thinsp~ ~zwnj~‌~zwj~‍~lrm~‎~rlm~‏~ndash~–~mdash~—~lsquo~‘~rsquo~’~sbquo~‚~ldquo~“~rdquo~”~bdquo~„~dagger~†~Dagger~‡~permil~‰~lsaquo~‹~rsaquo~›~euro~€~fnof~ƒ~Alpha~Α~Beta~Β~Gamma~Γ~Delta~Δ~Epsilon~Ε~Zeta~Ζ~Eta~Η~Theta~Θ~Iota~Ι~Kappa~Κ~Lambda~Λ~Mu~Μ~Nu~Ν~Xi~Ξ~Omicron~Ο~Pi~Π~Rho~Ρ~Sigma~Σ~Tau~Τ~Upsilon~Υ~Phi~Φ~Chi~Χ~Psi~Ψ~Omega~Ω~alpha~α~beta~β~gamma~γ~delta~δ~epsilon~ε~zeta~ζ~eta~η~theta~θ~iota~ι~kappa~κ~lambda~λ~mu~μ~nu~ν~xi~ξ~omicron~ο~pi~π~rho~ρ~sigmaf~ς~sigma~σ~tau~τ~upsilon~υ~phi~φ~chi~χ~psi~ψ~omega~ω~thetasym~ϑ~upsih~ϒ~piv~ϖ~bull~•~hellip~…~prime~′~Prime~″~oline~‾~frasl~⁄~weierp~℘~image~ℑ~real~ℜ~trade~™~alefsym~ℵ~larr~←~uarr~↑~rarr~→~darr~↓~harr~↔~crarr~↵~lArr~⇐~uArr~⇑~rArr~⇒~dArr~⇓~hArr~⇔~forall~∀~part~∂~exist~∃~empty~∅~nabla~∇~isin~∈~notin~∉~ni~∋~prod~∏~sum~∑~minus~−~lowast~∗~radic~√~prop~∝~infin~∞~ang~∠~and~∧~or~∨~cap~∩~cup~∪~int~∫~there4~∴~sim~∼~cong~≅~asymp~≈~ne~≠~equiv~≡~le~≤~ge~≥~sub~⊂~sup~⊃~nsub~⊄~sube~⊆~supe~⊇~oplus~⊕~otimes~⊗~perp~⊥~sdot~⋅~lceil~⌈~rceil~⌉~lfloor~⌊~rfloor~⌋~lang~〈~rang~〉~loz~◊~spades~♠~clubs~♣~hearts~♥~diams~♦~~nbsp~ ~iexcl~¡~cent~¢~pound~£~curren~¤~yen~¥~brvbar~¦~sect~§~uml~¨~copy~©~ordf~ª~laquo~«~not~¬~shy~­~reg~®~macr~¯~deg~°~plusmn~±~sup2~²~sup3~³~acute~´~micro~µ~para~¶~middot~·~cedil~¸~sup1~¹~ordm~º~raquo~»~frac14~¼~frac12~½~frac34~¾~iquest~¿~Agrave~À~Aacute~Á~Acirc~Â~Atilde~Ã~Auml~Ä~Aring~Å~AElig~Æ~Ccedil~Ç~Egrave~È~Eacute~É~Ecirc~Ê~Euml~Ë~Igrave~Ì~Iacute~Í~Icirc~Î~Iuml~Ï~ETH~Ð~Ntilde~Ñ~Ograve~Ò~Oacute~Ó~Ocirc~Ô~Otilde~Õ~Ouml~Ö~times~×~Oslash~Ø~Ugrave~Ù~Uacute~Ú~Ucirc~Û~Uuml~Ü~Yacute~Ý~THORN~Þ~szlig~ß~agrave~à~aacute~á~acirc~â~atilde~ã~auml~ä~aring~å~aelig~æ~ccedil~ç~egrave~è~eacute~é~ecirc~ê~euml~ë~igrave~ì~iacute~í~icirc~î~iuml~ï~eth~ð~ntilde~ñ~ograve~ò~oacute~ó~ocirc~ô~otilde~õ~ouml~ö~divide~÷~oslash~ø~ugrave~ù~uacute~ú~ucirc~û~uuml~ü~yacute~ý~thorn~þ~yuml~ÿ~quot~"~amp~&~lt~<~gt~>`);
namedReferences["html5"] = generateNamedReferences('Abreve~Ă~Acy~А~Afr~𝔄~Amacr~Ā~And~⩓~Aogon~Ą~Aopf~𝔸~ApplyFunction~⁡~Ascr~𝒜~Assign~≔~Backslash~∖~Barv~⫧~Barwed~⌆~Bcy~Б~Because~∵~Bernoullis~ℬ~Bfr~𝔅~Bopf~𝔹~Breve~˘~Bscr~ℬ~Bumpeq~≎~CHcy~Ч~Cacute~Ć~Cap~⋒~CapitalDifferentialD~ⅅ~Cayleys~ℭ~Ccaron~Č~Ccirc~Ĉ~Cconint~∰~Cdot~Ċ~Cedilla~¸~CenterDot~·~Cfr~ℭ~CircleDot~⊙~CircleMinus~⊖~CirclePlus~⊕~CircleTimes~⊗~ClockwiseContourIntegral~∲~CloseCurlyDoubleQuote~”~CloseCurlyQuote~’~Colon~∷~Colone~⩴~Congruent~≡~Conint~∯~ContourIntegral~∮~Copf~ℂ~Coproduct~∐~CounterClockwiseContourIntegral~∳~Cross~⨯~Cscr~𝒞~Cup~⋓~CupCap~≍~DD~ⅅ~DDotrahd~⤑~DJcy~Ђ~DScy~Ѕ~DZcy~Џ~Darr~↡~Dashv~⫤~Dcaron~Ď~Dcy~Д~Del~∇~Dfr~𝔇~DiacriticalAcute~´~DiacriticalDot~˙~DiacriticalDoubleAcute~˝~DiacriticalGrave~`~DiacriticalTilde~˜~Diamond~⋄~DifferentialD~ⅆ~Dopf~𝔻~Dot~¨~DotDot~⃜~DotEqual~≐~DoubleContourIntegral~∯~DoubleDot~¨~DoubleDownArrow~⇓~DoubleLeftArrow~⇐~DoubleLeftRightArrow~⇔~DoubleLeftTee~⫤~DoubleLongLeftArrow~⟸~DoubleLongLeftRightArrow~⟺~DoubleLongRightArrow~⟹~DoubleRightArrow~⇒~DoubleRightTee~⊨~DoubleUpArrow~⇑~DoubleUpDownArrow~⇕~DoubleVerticalBar~∥~DownArrow~↓~DownArrowBar~⤓~DownArrowUpArrow~⇵~DownBreve~̑~DownLeftRightVector~⥐~DownLeftTeeVector~⥞~DownLeftVector~↽~DownLeftVectorBar~⥖~DownRightTeeVector~⥟~DownRightVector~⇁~DownRightVectorBar~⥗~DownTee~⊤~DownTeeArrow~↧~Downarrow~⇓~Dscr~𝒟~Dstrok~Đ~ENG~Ŋ~Ecaron~Ě~Ecy~Э~Edot~Ė~Efr~𝔈~Element~∈~Emacr~Ē~EmptySmallSquare~◻~EmptyVerySmallSquare~▫~Eogon~Ę~Eopf~𝔼~Equal~⩵~EqualTilde~≂~Equilibrium~⇌~Escr~ℰ~Esim~⩳~Exists~∃~ExponentialE~ⅇ~Fcy~Ф~Ffr~𝔉~FilledSmallSquare~◼~FilledVerySmallSquare~▪~Fopf~𝔽~ForAll~∀~Fouriertrf~ℱ~Fscr~ℱ~GJcy~Ѓ~Gammad~Ϝ~Gbreve~Ğ~Gcedil~Ģ~Gcirc~Ĝ~Gcy~Г~Gdot~Ġ~Gfr~𝔊~Gg~⋙~Gopf~𝔾~GreaterEqual~≥~GreaterEqualLess~⋛~GreaterFullEqual~≧~GreaterGreater~⪢~GreaterLess~≷~GreaterSlantEqual~⩾~GreaterTilde~≳~Gscr~𝒢~Gt~≫~HARDcy~Ъ~Hacek~ˇ~Hat~^~Hcirc~Ĥ~Hfr~ℌ~HilbertSpace~ℋ~Hopf~ℍ~HorizontalLine~─~Hscr~ℋ~Hstrok~Ħ~HumpDownHump~≎~HumpEqual~≏~IEcy~Е~IJlig~Ĳ~IOcy~Ё~Icy~И~Idot~İ~Ifr~ℑ~Im~ℑ~Imacr~Ī~ImaginaryI~ⅈ~Implies~⇒~Int~∬~Integral~∫~Intersection~⋂~InvisibleComma~⁣~InvisibleTimes~⁢~Iogon~Į~Iopf~𝕀~Iscr~ℐ~Itilde~Ĩ~Iukcy~І~Jcirc~Ĵ~Jcy~Й~Jfr~𝔍~Jopf~𝕁~Jscr~𝒥~Jsercy~Ј~Jukcy~Є~KHcy~Х~KJcy~Ќ~Kcedil~Ķ~Kcy~К~Kfr~𝔎~Kopf~𝕂~Kscr~𝒦~LJcy~Љ~Lacute~Ĺ~Lang~⟪~Laplacetrf~ℒ~Larr~↞~Lcaron~Ľ~Lcedil~Ļ~Lcy~Л~LeftAngleBracket~⟨~LeftArrow~←~LeftArrowBar~⇤~LeftArrowRightArrow~⇆~LeftCeiling~⌈~LeftDoubleBracket~⟦~LeftDownTeeVector~⥡~LeftDownVector~⇃~LeftDownVectorBar~⥙~LeftFloor~⌊~LeftRightArrow~↔~LeftRightVector~⥎~LeftTee~⊣~LeftTeeArrow~↤~LeftTeeVector~⥚~LeftTriangle~⊲~LeftTriangleBar~⧏~LeftTriangleEqual~⊴~LeftUpDownVector~⥑~LeftUpTeeVector~⥠~LeftUpVector~↿~LeftUpVectorBar~⥘~LeftVector~↼~LeftVectorBar~⥒~Leftarrow~⇐~Leftrightarrow~⇔~LessEqualGreater~⋚~LessFullEqual~≦~LessGreater~≶~LessLess~⪡~LessSlantEqual~⩽~LessTilde~≲~Lfr~𝔏~Ll~⋘~Lleftarrow~⇚~Lmidot~Ŀ~LongLeftArrow~⟵~LongLeftRightArrow~⟷~LongRightArrow~⟶~Longleftarrow~⟸~Longleftrightarrow~⟺~Longrightarrow~⟹~Lopf~𝕃~LowerLeftArrow~↙~LowerRightArrow~↘~Lscr~ℒ~Lsh~↰~Lstrok~Ł~Lt~≪~Map~⤅~Mcy~М~MediumSpace~ ~Mellintrf~ℳ~Mfr~𝔐~MinusPlus~∓~Mopf~𝕄~Mscr~ℳ~NJcy~Њ~Nacute~Ń~Ncaron~Ň~Ncedil~Ņ~Ncy~Н~NegativeMediumSpace~​~NegativeThickSpace~​~NegativeThinSpace~​~NegativeVeryThinSpace~​~NestedGreaterGreater~≫~NestedLessLess~≪~NewLine~\n~Nfr~𝔑~NoBreak~⁠~NonBreakingSpace~ ~Nopf~ℕ~Not~⫬~NotCongruent~≢~NotCupCap~≭~NotDoubleVerticalBar~∦~NotElement~∉~NotEqual~≠~NotEqualTilde~≂̸~NotExists~∄~NotGreater~≯~NotGreaterEqual~≱~NotGreaterFullEqual~≧̸~NotGreaterGreater~≫̸~NotGreaterLess~≹~NotGreaterSlantEqual~⩾̸~NotGreaterTilde~≵~NotHumpDownHump~≎̸~NotHumpEqual~≏̸~NotLeftTriangle~⋪~NotLeftTriangleBar~⧏̸~NotLeftTriangleEqual~⋬~NotLess~≮~NotLessEqual~≰~NotLessGreater~≸~NotLessLess~≪̸~NotLessSlantEqual~⩽̸~NotLessTilde~≴~NotNestedGreaterGreater~⪢̸~NotNestedLessLess~⪡̸~NotPrecedes~⊀~NotPrecedesEqual~⪯̸~NotPrecedesSlantEqual~⋠~NotReverseElement~∌~NotRightTriangle~⋫~NotRightTriangleBar~⧐̸~NotRightTriangleEqual~⋭~NotSquareSubset~⊏̸~NotSquareSubsetEqual~⋢~NotSquareSuperset~⊐̸~NotSquareSupersetEqual~⋣~NotSubset~⊂⃒~NotSubsetEqual~⊈~NotSucceeds~⊁~NotSucceedsEqual~⪰̸~NotSucceedsSlantEqual~⋡~NotSucceedsTilde~≿̸~NotSuperset~⊃⃒~NotSupersetEqual~⊉~NotTilde~≁~NotTildeEqual~≄~NotTildeFullEqual~≇~NotTildeTilde~≉~NotVerticalBar~∤~Nscr~𝒩~Ocy~О~Odblac~Ő~Ofr~𝔒~Omacr~Ō~Oopf~𝕆~OpenCurlyDoubleQuote~“~OpenCurlyQuote~‘~Or~⩔~Oscr~𝒪~Otimes~⨷~OverBar~‾~OverBrace~⏞~OverBracket~⎴~OverParenthesis~⏜~PartialD~∂~Pcy~П~Pfr~𝔓~PlusMinus~±~Poincareplane~ℌ~Popf~ℙ~Pr~⪻~Precedes~≺~PrecedesEqual~⪯~PrecedesSlantEqual~≼~PrecedesTilde~≾~Product~∏~Proportion~∷~Proportional~∝~Pscr~𝒫~Qfr~𝔔~Qopf~ℚ~Qscr~𝒬~RBarr~⤐~Racute~Ŕ~Rang~⟫~Rarr~↠~Rarrtl~⤖~Rcaron~Ř~Rcedil~Ŗ~Rcy~Р~Re~ℜ~ReverseElement~∋~ReverseEquilibrium~⇋~ReverseUpEquilibrium~⥯~Rfr~ℜ~RightAngleBracket~⟩~RightArrow~→~RightArrowBar~⇥~RightArrowLeftArrow~⇄~RightCeiling~⌉~RightDoubleBracket~⟧~RightDownTeeVector~⥝~RightDownVector~⇂~RightDownVectorBar~⥕~RightFloor~⌋~RightTee~⊢~RightTeeArrow~↦~RightTeeVector~⥛~RightTriangle~⊳~RightTriangleBar~⧐~RightTriangleEqual~⊵~RightUpDownVector~⥏~RightUpTeeVector~⥜~RightUpVector~↾~RightUpVectorBar~⥔~RightVector~⇀~RightVectorBar~⥓~Rightarrow~⇒~Ropf~ℝ~RoundImplies~⥰~Rrightarrow~⇛~Rscr~ℛ~Rsh~↱~RuleDelayed~⧴~SHCHcy~Щ~SHcy~Ш~SOFTcy~Ь~Sacute~Ś~Sc~⪼~Scedil~Ş~Scirc~Ŝ~Scy~С~Sfr~𝔖~ShortDownArrow~↓~ShortLeftArrow~←~ShortRightArrow~→~ShortUpArrow~↑~SmallCircle~∘~Sopf~𝕊~Sqrt~√~Square~□~SquareIntersection~⊓~SquareSubset~⊏~SquareSubsetEqual~⊑~SquareSuperset~⊐~SquareSupersetEqual~⊒~SquareUnion~⊔~Sscr~𝒮~Star~⋆~Sub~⋐~Subset~⋐~SubsetEqual~⊆~Succeeds~≻~SucceedsEqual~⪰~SucceedsSlantEqual~≽~SucceedsTilde~≿~SuchThat~∋~Sum~∑~Sup~⋑~Superset~⊃~SupersetEqual~⊇~Supset~⋑~TRADE~™~TSHcy~Ћ~TScy~Ц~Tab~	~Tcaron~Ť~Tcedil~Ţ~Tcy~Т~Tfr~𝔗~Therefore~∴~ThickSpace~  ~ThinSpace~ ~Tilde~∼~TildeEqual~≃~TildeFullEqual~≅~TildeTilde~≈~Topf~𝕋~TripleDot~⃛~Tscr~𝒯~Tstrok~Ŧ~Uarr~↟~Uarrocir~⥉~Ubrcy~Ў~Ubreve~Ŭ~Ucy~У~Udblac~Ű~Ufr~𝔘~Umacr~Ū~UnderBar~_~UnderBrace~⏟~UnderBracket~⎵~UnderParenthesis~⏝~Union~⋃~UnionPlus~⊎~Uogon~Ų~Uopf~𝕌~UpArrow~↑~UpArrowBar~⤒~UpArrowDownArrow~⇅~UpDownArrow~↕~UpEquilibrium~⥮~UpTee~⊥~UpTeeArrow~↥~Uparrow~⇑~Updownarrow~⇕~UpperLeftArrow~↖~UpperRightArrow~↗~Upsi~ϒ~Uring~Ů~Uscr~𝒰~Utilde~Ũ~VDash~⊫~Vbar~⫫~Vcy~В~Vdash~⊩~Vdashl~⫦~Vee~⋁~Verbar~‖~Vert~‖~VerticalBar~∣~VerticalLine~|~VerticalSeparator~❘~VerticalTilde~≀~VeryThinSpace~ ~Vfr~𝔙~Vopf~𝕍~Vscr~𝒱~Vvdash~⊪~Wcirc~Ŵ~Wedge~⋀~Wfr~𝔚~Wopf~𝕎~Wscr~𝒲~Xfr~𝔛~Xopf~𝕏~Xscr~𝒳~YAcy~Я~YIcy~Ї~YUcy~Ю~Ycirc~Ŷ~Ycy~Ы~Yfr~𝔜~Yopf~𝕐~Yscr~𝒴~ZHcy~Ж~Zacute~Ź~Zcaron~Ž~Zcy~З~Zdot~Ż~ZeroWidthSpace~​~Zfr~ℨ~Zopf~ℤ~Zscr~𝒵~abreve~ă~ac~∾~acE~∾̳~acd~∿~acy~а~af~⁡~afr~𝔞~aleph~ℵ~amacr~ā~amalg~⨿~andand~⩕~andd~⩜~andslope~⩘~andv~⩚~ange~⦤~angle~∠~angmsd~∡~angmsdaa~⦨~angmsdab~⦩~angmsdac~⦪~angmsdad~⦫~angmsdae~⦬~angmsdaf~⦭~angmsdag~⦮~angmsdah~⦯~angrt~∟~angrtvb~⊾~angrtvbd~⦝~angsph~∢~angst~Å~angzarr~⍼~aogon~ą~aopf~𝕒~ap~≈~apE~⩰~apacir~⩯~ape~≊~apid~≋~approx~≈~approxeq~≊~ascr~𝒶~ast~*~asympeq~≍~awconint~∳~awint~⨑~bNot~⫭~backcong~≌~backepsilon~϶~backprime~‵~backsim~∽~backsimeq~⋍~barvee~⊽~barwed~⌅~barwedge~⌅~bbrk~⎵~bbrktbrk~⎶~bcong~≌~bcy~б~becaus~∵~because~∵~bemptyv~⦰~bepsi~϶~bernou~ℬ~beth~ℶ~between~≬~bfr~𝔟~bigcap~⋂~bigcirc~◯~bigcup~⋃~bigodot~⨀~bigoplus~⨁~bigotimes~⨂~bigsqcup~⨆~bigstar~★~bigtriangledown~▽~bigtriangleup~△~biguplus~⨄~bigvee~⋁~bigwedge~⋀~bkarow~⤍~blacklozenge~⧫~blacksquare~▪~blacktriangle~▴~blacktriangledown~▾~blacktriangleleft~◂~blacktriangleright~▸~blank~␣~blk12~▒~blk14~░~blk34~▓~block~█~bne~=⃥~bnequiv~≡⃥~bnot~⌐~bopf~𝕓~bot~⊥~bottom~⊥~bowtie~⋈~boxDL~╗~boxDR~╔~boxDl~╖~boxDr~╓~boxH~═~boxHD~╦~boxHU~╩~boxHd~╤~boxHu~╧~boxUL~╝~boxUR~╚~boxUl~╜~boxUr~╙~boxV~║~boxVH~╬~boxVL~╣~boxVR~╠~boxVh~╫~boxVl~╢~boxVr~╟~boxbox~⧉~boxdL~╕~boxdR~╒~boxdl~┐~boxdr~┌~boxh~─~boxhD~╥~boxhU~╨~boxhd~┬~boxhu~┴~boxminus~⊟~boxplus~⊞~boxtimes~⊠~boxuL~╛~boxuR~╘~boxul~┘~boxur~└~boxv~│~boxvH~╪~boxvL~╡~boxvR~╞~boxvh~┼~boxvl~┤~boxvr~├~bprime~‵~breve~˘~bscr~𝒷~bsemi~⁏~bsim~∽~bsime~⋍~bsol~\\~bsolb~⧅~bsolhsub~⟈~bullet~•~bump~≎~bumpE~⪮~bumpe~≏~bumpeq~≏~cacute~ć~capand~⩄~capbrcup~⩉~capcap~⩋~capcup~⩇~capdot~⩀~caps~∩︀~caret~⁁~caron~ˇ~ccaps~⩍~ccaron~č~ccirc~ĉ~ccups~⩌~ccupssm~⩐~cdot~ċ~cemptyv~⦲~centerdot~·~cfr~𝔠~chcy~ч~check~✓~checkmark~✓~cir~○~cirE~⧃~circeq~≗~circlearrowleft~↺~circlearrowright~↻~circledR~®~circledS~Ⓢ~circledast~⊛~circledcirc~⊚~circleddash~⊝~cire~≗~cirfnint~⨐~cirmid~⫯~cirscir~⧂~clubsuit~♣~colon~:~colone~≔~coloneq~≔~comma~,~commat~@~comp~∁~compfn~∘~complement~∁~complexes~ℂ~congdot~⩭~conint~∮~copf~𝕔~coprod~∐~copysr~℗~cross~✗~cscr~𝒸~csub~⫏~csube~⫑~csup~⫐~csupe~⫒~ctdot~⋯~cudarrl~⤸~cudarrr~⤵~cuepr~⋞~cuesc~⋟~cularr~↶~cularrp~⤽~cupbrcap~⩈~cupcap~⩆~cupcup~⩊~cupdot~⊍~cupor~⩅~cups~∪︀~curarr~↷~curarrm~⤼~curlyeqprec~⋞~curlyeqsucc~⋟~curlyvee~⋎~curlywedge~⋏~curvearrowleft~↶~curvearrowright~↷~cuvee~⋎~cuwed~⋏~cwconint~∲~cwint~∱~cylcty~⌭~dHar~⥥~daleth~ℸ~dash~‐~dashv~⊣~dbkarow~⤏~dblac~˝~dcaron~ď~dcy~д~dd~ⅆ~ddagger~‡~ddarr~⇊~ddotseq~⩷~demptyv~⦱~dfisht~⥿~dfr~𝔡~dharl~⇃~dharr~⇂~diam~⋄~diamond~⋄~diamondsuit~♦~die~¨~digamma~ϝ~disin~⋲~div~÷~divideontimes~⋇~divonx~⋇~djcy~ђ~dlcorn~⌞~dlcrop~⌍~dollar~$~dopf~𝕕~dot~˙~doteq~≐~doteqdot~≑~dotminus~∸~dotplus~∔~dotsquare~⊡~doublebarwedge~⌆~downarrow~↓~downdownarrows~⇊~downharpoonleft~⇃~downharpoonright~⇂~drbkarow~⤐~drcorn~⌟~drcrop~⌌~dscr~𝒹~dscy~ѕ~dsol~⧶~dstrok~đ~dtdot~⋱~dtri~▿~dtrif~▾~duarr~⇵~duhar~⥯~dwangle~⦦~dzcy~џ~dzigrarr~⟿~eDDot~⩷~eDot~≑~easter~⩮~ecaron~ě~ecir~≖~ecolon~≕~ecy~э~edot~ė~ee~ⅇ~efDot~≒~efr~𝔢~eg~⪚~egs~⪖~egsdot~⪘~el~⪙~elinters~⏧~ell~ℓ~els~⪕~elsdot~⪗~emacr~ē~emptyset~∅~emptyv~∅~emsp13~ ~emsp14~ ~eng~ŋ~eogon~ę~eopf~𝕖~epar~⋕~eparsl~⧣~eplus~⩱~epsi~ε~epsiv~ϵ~eqcirc~≖~eqcolon~≕~eqsim~≂~eqslantgtr~⪖~eqslantless~⪕~equals~=~equest~≟~equivDD~⩸~eqvparsl~⧥~erDot~≓~erarr~⥱~escr~ℯ~esdot~≐~esim~≂~excl~!~expectation~ℰ~exponentiale~ⅇ~fallingdotseq~≒~fcy~ф~female~♀~ffilig~ﬃ~fflig~ﬀ~ffllig~ﬄ~ffr~𝔣~filig~ﬁ~fjlig~fj~flat~♭~fllig~ﬂ~fltns~▱~fopf~𝕗~fork~⋔~forkv~⫙~fpartint~⨍~frac13~⅓~frac15~⅕~frac16~⅙~frac18~⅛~frac23~⅔~frac25~⅖~frac35~⅗~frac38~⅜~frac45~⅘~frac56~⅚~frac58~⅝~frac78~⅞~frown~⌢~fscr~𝒻~gE~≧~gEl~⪌~gacute~ǵ~gammad~ϝ~gap~⪆~gbreve~ğ~gcirc~ĝ~gcy~г~gdot~ġ~gel~⋛~geq~≥~geqq~≧~geqslant~⩾~ges~⩾~gescc~⪩~gesdot~⪀~gesdoto~⪂~gesdotol~⪄~gesl~⋛︀~gesles~⪔~gfr~𝔤~gg~≫~ggg~⋙~gimel~ℷ~gjcy~ѓ~gl~≷~glE~⪒~gla~⪥~glj~⪤~gnE~≩~gnap~⪊~gnapprox~⪊~gne~⪈~gneq~⪈~gneqq~≩~gnsim~⋧~gopf~𝕘~grave~`~gscr~ℊ~gsim~≳~gsime~⪎~gsiml~⪐~gtcc~⪧~gtcir~⩺~gtdot~⋗~gtlPar~⦕~gtquest~⩼~gtrapprox~⪆~gtrarr~⥸~gtrdot~⋗~gtreqless~⋛~gtreqqless~⪌~gtrless~≷~gtrsim~≳~gvertneqq~≩︀~gvnE~≩︀~hairsp~ ~half~½~hamilt~ℋ~hardcy~ъ~harrcir~⥈~harrw~↭~hbar~ℏ~hcirc~ĥ~heartsuit~♥~hercon~⊹~hfr~𝔥~hksearow~⤥~hkswarow~⤦~hoarr~⇿~homtht~∻~hookleftarrow~↩~hookrightarrow~↪~hopf~𝕙~horbar~―~hscr~𝒽~hslash~ℏ~hstrok~ħ~hybull~⁃~hyphen~‐~ic~⁣~icy~и~iecy~е~iff~⇔~ifr~𝔦~ii~ⅈ~iiiint~⨌~iiint~∭~iinfin~⧜~iiota~℩~ijlig~ĳ~imacr~ī~imagline~ℐ~imagpart~ℑ~imath~ı~imof~⊷~imped~Ƶ~in~∈~incare~℅~infintie~⧝~inodot~ı~intcal~⊺~integers~ℤ~intercal~⊺~intlarhk~⨗~intprod~⨼~iocy~ё~iogon~į~iopf~𝕚~iprod~⨼~iscr~𝒾~isinE~⋹~isindot~⋵~isins~⋴~isinsv~⋳~isinv~∈~it~⁢~itilde~ĩ~iukcy~і~jcirc~ĵ~jcy~й~jfr~𝔧~jmath~ȷ~jopf~𝕛~jscr~𝒿~jsercy~ј~jukcy~є~kappav~ϰ~kcedil~ķ~kcy~к~kfr~𝔨~kgreen~ĸ~khcy~х~kjcy~ќ~kopf~𝕜~kscr~𝓀~lAarr~⇚~lAtail~⤛~lBarr~⤎~lE~≦~lEg~⪋~lHar~⥢~lacute~ĺ~laemptyv~⦴~lagran~ℒ~langd~⦑~langle~⟨~lap~⪅~larrb~⇤~larrbfs~⤟~larrfs~⤝~larrhk~↩~larrlp~↫~larrpl~⤹~larrsim~⥳~larrtl~↢~lat~⪫~latail~⤙~late~⪭~lates~⪭︀~lbarr~⤌~lbbrk~❲~lbrace~{~lbrack~[~lbrke~⦋~lbrksld~⦏~lbrkslu~⦍~lcaron~ľ~lcedil~ļ~lcub~{~lcy~л~ldca~⤶~ldquor~„~ldrdhar~⥧~ldrushar~⥋~ldsh~↲~leftarrow~←~leftarrowtail~↢~leftharpoondown~↽~leftharpoonup~↼~leftleftarrows~⇇~leftrightarrow~↔~leftrightarrows~⇆~leftrightharpoons~⇋~leftrightsquigarrow~↭~leftthreetimes~⋋~leg~⋚~leq~≤~leqq~≦~leqslant~⩽~les~⩽~lescc~⪨~lesdot~⩿~lesdoto~⪁~lesdotor~⪃~lesg~⋚︀~lesges~⪓~lessapprox~⪅~lessdot~⋖~lesseqgtr~⋚~lesseqqgtr~⪋~lessgtr~≶~lesssim~≲~lfisht~⥼~lfr~𝔩~lg~≶~lgE~⪑~lhard~↽~lharu~↼~lharul~⥪~lhblk~▄~ljcy~љ~ll~≪~llarr~⇇~llcorner~⌞~llhard~⥫~lltri~◺~lmidot~ŀ~lmoust~⎰~lmoustache~⎰~lnE~≨~lnap~⪉~lnapprox~⪉~lne~⪇~lneq~⪇~lneqq~≨~lnsim~⋦~loang~⟬~loarr~⇽~lobrk~⟦~longleftarrow~⟵~longleftrightarrow~⟷~longmapsto~⟼~longrightarrow~⟶~looparrowleft~↫~looparrowright~↬~lopar~⦅~lopf~𝕝~loplus~⨭~lotimes~⨴~lowbar~_~lozenge~◊~lozf~⧫~lpar~(~lparlt~⦓~lrarr~⇆~lrcorner~⌟~lrhar~⇋~lrhard~⥭~lrtri~⊿~lscr~𝓁~lsh~↰~lsim~≲~lsime~⪍~lsimg~⪏~lsqb~[~lsquor~‚~lstrok~ł~ltcc~⪦~ltcir~⩹~ltdot~⋖~lthree~⋋~ltimes~⋉~ltlarr~⥶~ltquest~⩻~ltrPar~⦖~ltri~◃~ltrie~⊴~ltrif~◂~lurdshar~⥊~luruhar~⥦~lvertneqq~≨︀~lvnE~≨︀~mDDot~∺~male~♂~malt~✠~maltese~✠~map~↦~mapsto~↦~mapstodown~↧~mapstoleft~↤~mapstoup~↥~marker~▮~mcomma~⨩~mcy~м~measuredangle~∡~mfr~𝔪~mho~℧~mid~∣~midast~*~midcir~⫰~minusb~⊟~minusd~∸~minusdu~⨪~mlcp~⫛~mldr~…~mnplus~∓~models~⊧~mopf~𝕞~mp~∓~mscr~𝓂~mstpos~∾~multimap~⊸~mumap~⊸~nGg~⋙̸~nGt~≫⃒~nGtv~≫̸~nLeftarrow~⇍~nLeftrightarrow~⇎~nLl~⋘̸~nLt~≪⃒~nLtv~≪̸~nRightarrow~⇏~nVDash~⊯~nVdash~⊮~nacute~ń~nang~∠⃒~nap~≉~napE~⩰̸~napid~≋̸~napos~ŉ~napprox~≉~natur~♮~natural~♮~naturals~ℕ~nbump~≎̸~nbumpe~≏̸~ncap~⩃~ncaron~ň~ncedil~ņ~ncong~≇~ncongdot~⩭̸~ncup~⩂~ncy~н~neArr~⇗~nearhk~⤤~nearr~↗~nearrow~↗~nedot~≐̸~nequiv~≢~nesear~⤨~nesim~≂̸~nexist~∄~nexists~∄~nfr~𝔫~ngE~≧̸~nge~≱~ngeq~≱~ngeqq~≧̸~ngeqslant~⩾̸~nges~⩾̸~ngsim~≵~ngt~≯~ngtr~≯~nhArr~⇎~nharr~↮~nhpar~⫲~nis~⋼~nisd~⋺~niv~∋~njcy~њ~nlArr~⇍~nlE~≦̸~nlarr~↚~nldr~‥~nle~≰~nleftarrow~↚~nleftrightarrow~↮~nleq~≰~nleqq~≦̸~nleqslant~⩽̸~nles~⩽̸~nless~≮~nlsim~≴~nlt~≮~nltri~⋪~nltrie~⋬~nmid~∤~nopf~𝕟~notinE~⋹̸~notindot~⋵̸~notinva~∉~notinvb~⋷~notinvc~⋶~notni~∌~notniva~∌~notnivb~⋾~notnivc~⋽~npar~∦~nparallel~∦~nparsl~⫽⃥~npart~∂̸~npolint~⨔~npr~⊀~nprcue~⋠~npre~⪯̸~nprec~⊀~npreceq~⪯̸~nrArr~⇏~nrarr~↛~nrarrc~⤳̸~nrarrw~↝̸~nrightarrow~↛~nrtri~⋫~nrtrie~⋭~nsc~⊁~nsccue~⋡~nsce~⪰̸~nscr~𝓃~nshortmid~∤~nshortparallel~∦~nsim~≁~nsime~≄~nsimeq~≄~nsmid~∤~nspar~∦~nsqsube~⋢~nsqsupe~⋣~nsubE~⫅̸~nsube~⊈~nsubset~⊂⃒~nsubseteq~⊈~nsubseteqq~⫅̸~nsucc~⊁~nsucceq~⪰̸~nsup~⊅~nsupE~⫆̸~nsupe~⊉~nsupset~⊃⃒~nsupseteq~⊉~nsupseteqq~⫆̸~ntgl~≹~ntlg~≸~ntriangleleft~⋪~ntrianglelefteq~⋬~ntriangleright~⋫~ntrianglerighteq~⋭~num~#~numero~№~numsp~ ~nvDash~⊭~nvHarr~⤄~nvap~≍⃒~nvdash~⊬~nvge~≥⃒~nvgt~>⃒~nvinfin~⧞~nvlArr~⤂~nvle~≤⃒~nvlt~<⃒~nvltrie~⊴⃒~nvrArr~⤃~nvrtrie~⊵⃒~nvsim~∼⃒~nwArr~⇖~nwarhk~⤣~nwarr~↖~nwarrow~↖~nwnear~⤧~oS~Ⓢ~oast~⊛~ocir~⊚~ocy~о~odash~⊝~odblac~ő~odiv~⨸~odot~⊙~odsold~⦼~ofcir~⦿~ofr~𝔬~ogon~˛~ogt~⧁~ohbar~⦵~ohm~Ω~oint~∮~olarr~↺~olcir~⦾~olcross~⦻~olt~⧀~omacr~ō~omid~⦶~ominus~⊖~oopf~𝕠~opar~⦷~operp~⦹~orarr~↻~ord~⩝~order~ℴ~orderof~ℴ~origof~⊶~oror~⩖~orslope~⩗~orv~⩛~oscr~ℴ~osol~⊘~otimesas~⨶~ovbar~⌽~par~∥~parallel~∥~parsim~⫳~parsl~⫽~pcy~п~percnt~%~period~.~pertenk~‱~pfr~𝔭~phiv~ϕ~phmmat~ℳ~phone~☎~pitchfork~⋔~planck~ℏ~planckh~ℎ~plankv~ℏ~plus~+~plusacir~⨣~plusb~⊞~pluscir~⨢~plusdo~∔~plusdu~⨥~pluse~⩲~plussim~⨦~plustwo~⨧~pm~±~pointint~⨕~popf~𝕡~pr~≺~prE~⪳~prap~⪷~prcue~≼~pre~⪯~prec~≺~precapprox~⪷~preccurlyeq~≼~preceq~⪯~precnapprox~⪹~precneqq~⪵~precnsim~⋨~precsim~≾~primes~ℙ~prnE~⪵~prnap~⪹~prnsim~⋨~profalar~⌮~profline~⌒~profsurf~⌓~propto~∝~prsim~≾~prurel~⊰~pscr~𝓅~puncsp~ ~qfr~𝔮~qint~⨌~qopf~𝕢~qprime~⁗~qscr~𝓆~quaternions~ℍ~quatint~⨖~quest~?~questeq~≟~rAarr~⇛~rAtail~⤜~rBarr~⤏~rHar~⥤~race~∽̱~racute~ŕ~raemptyv~⦳~rangd~⦒~range~⦥~rangle~⟩~rarrap~⥵~rarrb~⇥~rarrbfs~⤠~rarrc~⤳~rarrfs~⤞~rarrhk~↪~rarrlp~↬~rarrpl~⥅~rarrsim~⥴~rarrtl~↣~rarrw~↝~ratail~⤚~ratio~∶~rationals~ℚ~rbarr~⤍~rbbrk~❳~rbrace~}~rbrack~]~rbrke~⦌~rbrksld~⦎~rbrkslu~⦐~rcaron~ř~rcedil~ŗ~rcub~}~rcy~р~rdca~⤷~rdldhar~⥩~rdquor~”~rdsh~↳~realine~ℛ~realpart~ℜ~reals~ℝ~rect~▭~rfisht~⥽~rfr~𝔯~rhard~⇁~rharu~⇀~rharul~⥬~rhov~ϱ~rightarrow~→~rightarrowtail~↣~rightharpoondown~⇁~rightharpoonup~⇀~rightleftarrows~⇄~rightleftharpoons~⇌~rightrightarrows~⇉~rightsquigarrow~↝~rightthreetimes~⋌~ring~˚~risingdotseq~≓~rlarr~⇄~rlhar~⇌~rmoust~⎱~rmoustache~⎱~rnmid~⫮~roang~⟭~roarr~⇾~robrk~⟧~ropar~⦆~ropf~𝕣~roplus~⨮~rotimes~⨵~rpar~)~rpargt~⦔~rppolint~⨒~rrarr~⇉~rscr~𝓇~rsh~↱~rsqb~]~rsquor~’~rthree~⋌~rtimes~⋊~rtri~▹~rtrie~⊵~rtrif~▸~rtriltri~⧎~ruluhar~⥨~rx~℞~sacute~ś~sc~≻~scE~⪴~scap~⪸~sccue~≽~sce~⪰~scedil~ş~scirc~ŝ~scnE~⪶~scnap~⪺~scnsim~⋩~scpolint~⨓~scsim~≿~scy~с~sdotb~⊡~sdote~⩦~seArr~⇘~searhk~⤥~searr~↘~searrow~↘~semi~;~seswar~⤩~setminus~∖~setmn~∖~sext~✶~sfr~𝔰~sfrown~⌢~sharp~♯~shchcy~щ~shcy~ш~shortmid~∣~shortparallel~∥~sigmav~ς~simdot~⩪~sime~≃~simeq~≃~simg~⪞~simgE~⪠~siml~⪝~simlE~⪟~simne~≆~simplus~⨤~simrarr~⥲~slarr~←~smallsetminus~∖~smashp~⨳~smeparsl~⧤~smid~∣~smile~⌣~smt~⪪~smte~⪬~smtes~⪬︀~softcy~ь~sol~/~solb~⧄~solbar~⌿~sopf~𝕤~spadesuit~♠~spar~∥~sqcap~⊓~sqcaps~⊓︀~sqcup~⊔~sqcups~⊔︀~sqsub~⊏~sqsube~⊑~sqsubset~⊏~sqsubseteq~⊑~sqsup~⊐~sqsupe~⊒~sqsupset~⊐~sqsupseteq~⊒~squ~□~square~□~squarf~▪~squf~▪~srarr~→~sscr~𝓈~ssetmn~∖~ssmile~⌣~sstarf~⋆~star~☆~starf~★~straightepsilon~ϵ~straightphi~ϕ~strns~¯~subE~⫅~subdot~⪽~subedot~⫃~submult~⫁~subnE~⫋~subne~⊊~subplus~⪿~subrarr~⥹~subset~⊂~subseteq~⊆~subseteqq~⫅~subsetneq~⊊~subsetneqq~⫋~subsim~⫇~subsub~⫕~subsup~⫓~succ~≻~succapprox~⪸~succcurlyeq~≽~succeq~⪰~succnapprox~⪺~succneqq~⪶~succnsim~⋩~succsim~≿~sung~♪~supE~⫆~supdot~⪾~supdsub~⫘~supedot~⫄~suphsol~⟉~suphsub~⫗~suplarr~⥻~supmult~⫂~supnE~⫌~supne~⊋~supplus~⫀~supset~⊃~supseteq~⊇~supseteqq~⫆~supsetneq~⊋~supsetneqq~⫌~supsim~⫈~supsub~⫔~supsup~⫖~swArr~⇙~swarhk~⤦~swarr~↙~swarrow~↙~swnwar~⤪~target~⌖~tbrk~⎴~tcaron~ť~tcedil~ţ~tcy~т~tdot~⃛~telrec~⌕~tfr~𝔱~therefore~∴~thetav~ϑ~thickapprox~≈~thicksim~∼~thkap~≈~thksim~∼~timesb~⊠~timesbar~⨱~timesd~⨰~tint~∭~toea~⤨~top~⊤~topbot~⌶~topcir~⫱~topf~𝕥~topfork~⫚~tosa~⤩~tprime~‴~triangle~▵~triangledown~▿~triangleleft~◃~trianglelefteq~⊴~triangleq~≜~triangleright~▹~trianglerighteq~⊵~tridot~◬~trie~≜~triminus~⨺~triplus~⨹~trisb~⧍~tritime~⨻~trpezium~⏢~tscr~𝓉~tscy~ц~tshcy~ћ~tstrok~ŧ~twixt~≬~twoheadleftarrow~↞~twoheadrightarrow~↠~uHar~⥣~ubrcy~ў~ubreve~ŭ~ucy~у~udarr~⇅~udblac~ű~udhar~⥮~ufisht~⥾~ufr~𝔲~uharl~↿~uharr~↾~uhblk~▀~ulcorn~⌜~ulcorner~⌜~ulcrop~⌏~ultri~◸~umacr~ū~uogon~ų~uopf~𝕦~uparrow~↑~updownarrow~↕~upharpoonleft~↿~upharpoonright~↾~uplus~⊎~upsi~υ~upuparrows~⇈~urcorn~⌝~urcorner~⌝~urcrop~⌎~uring~ů~urtri~◹~uscr~𝓊~utdot~⋰~utilde~ũ~utri~▵~utrif~▴~uuarr~⇈~uwangle~⦧~vArr~⇕~vBar~⫨~vBarv~⫩~vDash~⊨~vangrt~⦜~varepsilon~ϵ~varkappa~ϰ~varnothing~∅~varphi~ϕ~varpi~ϖ~varpropto~∝~varr~↕~varrho~ϱ~varsigma~ς~varsubsetneq~⊊︀~varsubsetneqq~⫋︀~varsupsetneq~⊋︀~varsupsetneqq~⫌︀~vartheta~ϑ~vartriangleleft~⊲~vartriangleright~⊳~vcy~в~vdash~⊢~vee~∨~veebar~⊻~veeeq~≚~vellip~⋮~verbar~|~vert~|~vfr~𝔳~vltri~⊲~vnsub~⊂⃒~vnsup~⊃⃒~vopf~𝕧~vprop~∝~vrtri~⊳~vscr~𝓋~vsubnE~⫋︀~vsubne~⊊︀~vsupnE~⫌︀~vsupne~⊋︀~vzigzag~⦚~wcirc~ŵ~wedbar~⩟~wedge~∧~wedgeq~≙~wfr~𝔴~wopf~𝕨~wp~℘~wr~≀~wreath~≀~wscr~𝓌~xcap~⋂~xcirc~◯~xcup~⋃~xdtri~▽~xfr~𝔵~xhArr~⟺~xharr~⟷~xlArr~⟸~xlarr~⟵~xmap~⟼~xnis~⋻~xodot~⨀~xopf~𝕩~xoplus~⨁~xotime~⨂~xrArr~⟹~xrarr~⟶~xscr~𝓍~xsqcup~⨆~xuplus~⨄~xutri~△~xvee~⋁~xwedge~⋀~yacy~я~ycirc~ŷ~ycy~ы~yfr~𝔶~yicy~ї~yopf~𝕪~yscr~𝓎~yucy~ю~zacute~ź~zcaron~ž~zcy~з~zdot~ż~zeetrf~ℨ~zfr~𝔷~zhcy~ж~zigrarr~⇝~zopf~𝕫~zscr~𝓏~~AMP~&~COPY~©~GT~>~LT~<~QUOT~"~REG~®', namedReferences["html4"]);
var numericUnicodeMap = {
  0: 65533,
  128: 8364,
  130: 8218,
  131: 402,
  132: 8222,
  133: 8230,
  134: 8224,
  135: 8225,
  136: 710,
  137: 8240,
  138: 352,
  139: 8249,
  140: 338,
  142: 381,
  145: 8216,
  146: 8217,
  147: 8220,
  148: 8221,
  149: 8226,
  150: 8211,
  151: 8212,
  152: 732,
  153: 8482,
  154: 353,
  155: 8250,
  156: 339,
  158: 382,
  159: 376
};
var fromCodePoint = String.fromCodePoint || function(astralCodePoint) {
  return String.fromCharCode(Math.floor((astralCodePoint - 65536) / 1024) + 55296, (astralCodePoint - 65536) % 1024 + 56320);
};
var __assign = function() {
  __assign = Object.assign || function(t2) {
    for (var s2, i2 = 1, n2 = arguments.length; i2 < n2; i2++) {
      s2 = arguments[i2];
      for (var p in s2) if (Object.prototype.hasOwnProperty.call(s2, p))
        t2[p] = s2[p];
    }
    return t2;
  };
  return __assign.apply(this, arguments);
};
var allNamedReferences = __assign(__assign({}, namedReferences), { all: namedReferences.html5 });
var defaultDecodeOptions = {
  scope: "body",
  level: "all"
};
var strict = /&(?:#\d+|#[xX][\da-fA-F]+|[0-9a-zA-Z]+);/g;
var attribute = /&(?:#\d+|#[xX][\da-fA-F]+|[0-9a-zA-Z]+)[;=]?/g;
var baseDecodeRegExps = {
  xml: {
    strict,
    attribute,
    body: bodyRegExps.xml
  },
  html4: {
    strict,
    attribute,
    body: bodyRegExps.html4
  },
  html5: {
    strict,
    attribute,
    body: bodyRegExps.html5
  }
};
var decodeRegExps = __assign(__assign({}, baseDecodeRegExps), { all: baseDecodeRegExps.html5 });
var fromCharCode = String.fromCharCode;
var outOfBoundsChar = fromCharCode(65533);
function getDecodedEntity(entity, references, isAttribute, isStrict) {
  var decodeResult = entity;
  var decodeEntityLastChar = entity[entity.length - 1];
  if (isAttribute && decodeEntityLastChar === "=") {
    decodeResult = entity;
  } else if (isStrict && decodeEntityLastChar !== ";") {
    decodeResult = entity;
  } else {
    var decodeResultByReference = references[entity];
    if (decodeResultByReference) {
      decodeResult = decodeResultByReference;
    } else if (entity[0] === "&" && entity[1] === "#") {
      var decodeSecondChar = entity[2];
      var decodeCode = decodeSecondChar == "x" || decodeSecondChar == "X" ? parseInt(entity.substr(3), 16) : parseInt(entity.substr(2));
      decodeResult = decodeCode >= 1114111 ? outOfBoundsChar : decodeCode > 65535 ? fromCodePoint(decodeCode) : fromCharCode(numericUnicodeMap[decodeCode] || decodeCode);
    }
  }
  return decodeResult;
}
function decode(text, _a) {
  var _b = _a === void 0 ? defaultDecodeOptions : _a, _c = _b.level, level = _c === void 0 ? "all" : _c, _d = _b.scope, scope = _d === void 0 ? level === "xml" ? "strict" : "body" : _d;
  if (!text) {
    return "";
  }
  var decodeRegExp = decodeRegExps[level][scope];
  var references = allNamedReferences[level].entities;
  var isAttribute = scope === "attribute";
  var isStrict = scope === "strict";
  return text.replace(decodeRegExp, function(entity) {
    return getDecodedEntity(entity, references, isAttribute, isStrict);
  });
}
const block0$h = (component) => {
  component.pdfStyle = { "p": { "marginBottom": "2pt" }, "bold": { "fontWeight": "bold" }, "italic": { "fontStyle": "italic" }, "underlined": { "textDecoration": "underline" }, "strikethrough": { "textDecoration": "line-through" } };
};
function visit(node, parent = null) {
  const rule = rules.find((rule2) => rule2.shouldProcessNode(node, parent));
  if (!rule) {
    console.log("unknown HTML node type", node);
    return null;
  }
  return rule.processNode(node, parent);
}
function visitChildren(children, parent) {
  return children.length ? children.map((child) => visit(child, parent)) : [visit({ type: "text", content: "&nbsp;" }, parent)];
}
const rules = [
  {
    shouldProcessNode: (node) => node.type === "text",
    processNode: (node) => decode(node.content, { scope: "strict" })
  },
  {
    shouldProcessNode: (node) => node.type === "tag" && node.name === "p",
    processNode: (node) => h("Text", { class: "p" }, visitChildren(node.children, node))
  },
  {
    shouldProcessNode: (node) => node.type === "tag" && node.name === "br",
    processNode: (_) => h("Text", {}, "\n")
  },
  {
    shouldProcessNode: (node) => node.type === "tag" && ["h1", "h2", "h3"].includes(node.name),
    processNode: function(node) {
      return h("Text", { class: node.name }, visitChildren(node.children, node));
    }
  },
  {
    shouldProcessNode: (node) => node.type === "tag" && (node.name === "strong" || node.name === "b"),
    processNode: (node) => h("Text", { class: "bold" }, visitChildren(node.children, node))
  },
  {
    shouldProcessNode: (node) => node.type === "tag" && node.name === "em",
    processNode: (node) => h("Text", { class: "italic" }, visitChildren(node.children, node))
  },
  {
    shouldProcessNode: (node) => node.type === "tag" && node.name === "u",
    processNode: (node) => h("Text", { class: "underlined" }, visitChildren(node.children, node))
  },
  {
    shouldProcessNode: (node) => node.type === "tag" && (node.name === "s" || node.name === "strike"),
    processNode: (node) => h("Text", { class: "strikethrough" }, visitChildren(node.children, node))
  },
  {
    shouldProcessNode: (node) => node.type === "tag" && ["ul", "ol"].includes(node.name),
    processNode: (node) => h("View", visitChildren(node.children, node))
  },
  {
    shouldProcessNode: (node) => node.type === "tag" && node.name === "li",
    processNode: (node, parent) => {
      const emptyChild = {
        type: "text",
        attrs: {},
        children: []
      };
      if (!node.children.length) {
        node.children.push({ ...emptyChild });
      }
      if (!node.children[0].children.length) {
        node.children[0].children.push({ ...emptyChild, content: "" });
      }
      if (parent.name === "ul") {
        node.children[0].children[0].content = "• " + node.children[0].children[0].content;
      } else if (parent.name === "ol") {
        const number = calculateListNumber(node, parent);
        node.children[0].children[0].content = `${number}. ` + node.children[0].children[0].content;
      }
      return h(
        "View",
        { style: { marginLeft: "4pt" } },
        visitChildren(node.children, node)
      );
    }
  }
];
function calculateListNumber(node, parent) {
  const index = parent.children.filter((child) => child.type === "tag" && child.name === "li").indexOf(node);
  if (parent.attrs.start !== void 0) {
    const start = parseInt(parent.attrs.start);
    if (!isNaN(start)) return start + index;
  }
  return index + 1;
}
const _sfc_main$r = {
  name: "RichText",
  extends: PdfComponent,
  props: {
    richText: { type: String, default: "" }
  },
  computed: {
    parsed() {
      return c.parse(this.richText);
    }
  },
  render() {
    return [this.parsed].flat().map((node) => visit(node));
  }
};
if (typeof block0$h === "function") block0$h(_sfc_main$r);
const RichText = /* @__PURE__ */ _export_sfc(_sfc_main$r, [["__file", "/app/src/campPrint/RichText.vue"]]);
function isEmptyHtml(html) {
  if (html === null) {
    return true;
  }
  return html.trim() === "" || html.trim() === "<p></p>";
}
const block0$g = (component) => {
  component.pdfStyle = { "summary-day-title-container": { "display": "flex", "flexDirection": "row", "justifyContent": "space-between", "alignItems": "baseline", "borderBottom": "2pt solid #aaaaaa", "paddingBottom": "2pt", "marginBottom": "1pt" }, "summary-day-title": { "fontSize": "14", "fontWeight": "semibold", "margin": "10pt 0 3pt" }, "summary-day-date": { "fontSize": "11pt" }, "summary-chapter-title": { "display": "flex", "flexDirection": "row", "alignItems": "center", "fontWeight": "bold", "margin": "10pt 0 4.5pt" } };
};
const _sfc_main$q = {
  name: "SummaryDay",
  components: { RichText, CategoryLabel },
  extends: PdfComponent,
  props: {
    period: { type: Object, required: true },
    day: { type: Object, required: true },
    contentType: { type: String, required: true },
    filter: { type: Object, default: () => ({}) }
  },
  computed: {
    date() {
      return dateLong(this.day.start, this.$tc);
    },
    scheduleEntries() {
      return this.period.scheduleEntries().items.filter((scheduleEntry) => {
        return scheduleEntry.day()._meta.self === this.day._meta.self && filterMatchScheduleEntry(scheduleEntry, this.filter);
      });
    },
    entries() {
      return this.scheduleEntries.map((scheduleEntry) => ({
        scheduleEntry,
        contentNodes: this.period.contentNodes().items.filter(
          (contentNode) => contentNode.contentTypeName === this.contentType && contentNode.root()._meta.self === scheduleEntry.activity().rootContentNode()._meta.self && !isEmptyHtml(contentNode.data.html)
        ).map((chapter) => ({
          ...chapter,
          title: this.chapterTitle(chapter, scheduleEntry)
        }))
      }));
    },
    entriesWithContentNodes() {
      return this.entries.filter(({ contentNodes }) => contentNodes.length);
    }
  },
  methods: {
    chapterTitle(chapter, scheduleEntry) {
      return scheduleEntry.activity().title + (chapter.instanceName ? " - " + chapter.instanceName : "");
    }
  }
};
const _hoisted_1$p = ["id"];
const _hoisted_2$c = { class: "summary-day-title" };
const _hoisted_3$7 = { class: "summary-day-date" };
const _hoisted_4$4 = {
  class: "summary-chapter-title",
  "min-presence-ahead": 30
};
const _hoisted_5$3 = ["id"];
const _hoisted_6$3 = { style: { "line-height": "1" } };
function _sfc_render$q(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_CategoryLabel = resolveComponent("CategoryLabel");
  const _component_RichText = resolveComponent("RichText");
  return openBlock(), createElementBlock(
    Fragment,
    null,
    [
      createBaseVNode("View", {
        id: `${_ctx.id}-${$props.period.id}-${$props.day.id}`,
        class: "summary-day-title-container"
      }, [
        createBaseVNode(
          "Text",
          _hoisted_2$c,
          toDisplayString(_ctx.$t("entity.day.name")) + " " + toDisplayString($props.day.number),
          1
          /* TEXT */
        ),
        createBaseVNode(
          "Text",
          _hoisted_3$7,
          toDisplayString($options.date),
          1
          /* TEXT */
        )
      ], 8, _hoisted_1$p),
      (openBlock(true), createElementBlock(
        Fragment,
        null,
        renderList($options.entriesWithContentNodes, ({ scheduleEntry, contentNodes }) => {
          return openBlock(), createElementBlock(
            Fragment,
            null,
            [
              (openBlock(true), createElementBlock(
                Fragment,
                null,
                renderList(contentNodes, (chapter) => {
                  return openBlock(), createElementBlock(
                    Fragment,
                    null,
                    [
                      createBaseVNode("View", _hoisted_4$4, [
                        createVNode(_component_CategoryLabel, {
                          category: scheduleEntry.activity().category(),
                          style: { "font-size": "10pt" }
                        }, null, 8, ["category"]),
                        createBaseVNode("Text", {
                          id: `${_ctx.id}-${$props.period.id}-${scheduleEntry.id}`,
                          style: { "margin": "0 3pt" }
                        }, toDisplayString(scheduleEntry.number) + " " + toDisplayString(chapter.title), 9, _hoisted_5$3)
                      ]),
                      createBaseVNode("View", _hoisted_6$3, [
                        createVNode(_component_RichText, {
                          "rich-text": chapter.data.html
                        }, null, 8, ["rich-text"])
                      ])
                    ],
                    64
                    /* STABLE_FRAGMENT */
                  );
                }),
                256
                /* UNKEYED_FRAGMENT */
              ))
            ],
            64
            /* STABLE_FRAGMENT */
          );
        }),
        256
        /* UNKEYED_FRAGMENT */
      ))
    ],
    64
    /* STABLE_FRAGMENT */
  );
}
if (typeof block0$g === "function") block0$g(_sfc_main$q);
const SummaryDay = /* @__PURE__ */ _export_sfc(_sfc_main$q, [["render", _sfc_render$q], ["__file", "/app/src/campPrint/summary/SummaryDay.vue"]]);
const block0$f = (component) => {
  component.pdfStyle = { "summary-period-title": { "fontSize": "10pt", "fontWeight": "bold", "textAlign": "center" } };
};
const _sfc_main$p = {
  name: "SummaryPeriod",
  components: { SummaryDay },
  extends: PdfComponent,
  props: {
    period: { type: Object, required: true },
    contentType: { type: String, required: true },
    filter: { type: Object, default: () => ({}) }
  },
  computed: {
    days() {
      const days = this.period.days().items.filter((day) => {
        return this.period.scheduleEntries().items.filter(
          (scheduleEntry) => scheduleEntry.day()._meta.self === day._meta.self && filterMatchScheduleEntry(scheduleEntry, this.filter)
        ).length > 0;
      });
      return sortBy(days, (day) => this.$date.utc(day.start).unix());
    },
    title() {
      return this.$tc("print.summary." + this.camelCase(this.contentType) + ".title");
    }
  },
  methods: { camelCase }
};
const _hoisted_1$o = ["id", "bookmark"];
function _sfc_render$p(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_SummaryDay = resolveComponent("SummaryDay");
  return openBlock(), createElementBlock(
    Fragment,
    null,
    [
      createBaseVNode("Text", {
        id: `${_ctx.id}-${$props.period.id}`,
        bookmark: { title: $options.title + ": " + $props.period.description, fit: true },
        class: "summary-period-title"
      }, toDisplayString($options.title) + ": " + toDisplayString($props.period.description), 9, _hoisted_1$o),
      (openBlock(true), createElementBlock(
        Fragment,
        null,
        renderList($options.days, (day) => {
          return openBlock(), createBlock(_component_SummaryDay, {
            id: _ctx.id,
            period: $props.period,
            day,
            "content-type": $props.contentType,
            filter: $props.filter
          }, null, 8, ["id", "period", "day", "content-type", "filter"]);
        }),
        256
        /* UNKEYED_FRAGMENT */
      ))
    ],
    64
    /* STABLE_FRAGMENT */
  );
}
if (typeof block0$f === "function") block0$f(_sfc_main$p);
const SummaryPeriod = /* @__PURE__ */ _export_sfc(_sfc_main$p, [["render", _sfc_render$p], ["__file", "/app/src/campPrint/summary/SummaryPeriod.vue"]]);
const _sfc_main$o = {
  name: "Story",
  components: { SummaryPeriod },
  extends: PdfComponent,
  props: {
    content: { type: Object, required: true },
    config: { type: Object, required: true }
  },
  computed: {
    periods() {
      return this.content.options.periods.map((periodUri) => this.api.get(periodUri));
    }
  }
};
const _hoisted_1$n = ["id"];
function _sfc_render$o(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_SummaryPeriod = resolveComponent("SummaryPeriod");
  return openBlock(), createElementBlock("Page", {
    id: _ctx.id,
    class: "page"
  }, [
    (openBlock(true), createElementBlock(
      Fragment,
      null,
      renderList($options.periods, (period) => {
        return openBlock(), createBlock(_component_SummaryPeriod, {
          id: _ctx.id,
          period,
          "content-type": $props.content.options.contentType,
          filter: $props.content.options.filter
        }, null, 8, ["id", "period", "content-type", "filter"]);
      }),
      256
      /* UNKEYED_FRAGMENT */
    ))
  ], 8, _hoisted_1$n);
}
const Story = /* @__PURE__ */ _export_sfc(_sfc_main$o, [["render", _sfc_render$o], ["__file", "/app/src/campPrint/summary/Story.vue"]]);
const _sfc_main$n = {
  name: "SafetyConsiderations",
  components: { SummaryPeriod },
  extends: PdfComponent,
  props: {
    content: { type: Object, required: true },
    config: { type: Object, required: true }
  },
  computed: {
    periods() {
      return this.content.options.periods.map((periodUri) => this.api.get(periodUri));
    }
  }
};
const _hoisted_1$m = ["id"];
function _sfc_render$n(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_SummaryPeriod = resolveComponent("SummaryPeriod");
  return openBlock(), createElementBlock("Page", {
    id: _ctx.id,
    class: "page"
  }, [
    (openBlock(true), createElementBlock(
      Fragment,
      null,
      renderList($options.periods, (period) => {
        return openBlock(), createBlock(_component_SummaryPeriod, {
          id: _ctx.id,
          period,
          "content-type": $props.content.options.contentType,
          filter: $props.content.options.filter
        }, null, 8, ["id", "period", "content-type", "filter"]);
      }),
      256
      /* UNKEYED_FRAGMENT */
    ))
  ], 8, _hoisted_1$m);
}
const SafetyConsiderations$1 = /* @__PURE__ */ _export_sfc(_sfc_main$n, [["render", _sfc_render$n], ["__file", "/app/src/campPrint/summary/SafetyConsiderations.vue"]]);
const block0$e = (component) => {
  component.pdfStyle = { "schedule-entry-header-title": { "display": "flex", "flexDirection": "row", "justifyContent": "space-between", "alignItems": "baseline", "paddingBottom": "2pt", "borderBottom": "2pt solid #aaaaaa" }, "schedule-entry-title": { "flexGrow": "1", "display": "flex", "flexDirection": "row", "fontSize": "14", "fontWeight": "semibold" }, "schedule-entry-category-label": { "margin": "4pt 0", "fontSize": "12pt" }, "schedule-entry-number-and-title": { "margin": "4pt 4pt", "maxWidth": "345pt" }, "schedule-entry-date": { "fontSize": "11pt" }, "schedule-entry-header": { "display": "flex", "flexDirection": "row", "justifyContent": "space-between", "borderBottom": "0.5pt solid black", "fontSize": "10pt", "marginBottom": "10pt" }, "schedule-entry-header-divider": { "borderLeft": "0.5pt solid black", "marginLeft": "3.5pt", "paddingLeft": "5pt" }, "schedule-entry-header-metadata": { "width": "50%", "padding": "2pt 0" }, "schedule-entry-header-metadata-entry": { "flexDirection": "row", "alignItems": "flex-start", "columnGap": "6pt" }, "schedule-entry-header-metadata-label": { "fontWeight": "semibold", "flexShrink": "0", "flexGrow": "0" } };
};
const _sfc_main$m = {
  name: "ScheduleEntryTitle",
  components: { Responsibles, CategoryLabel },
  extends: PdfComponent,
  props: {
    scheduleEntry: { type: Object, required: true },
    showHeader: { type: Boolean, required: false, default: true }
  },
  computed: {
    activity() {
      return this.scheduleEntry.activity();
    },
    bookmarkTitle() {
      return [
        this.activity.category().short,
        this.scheduleEntry.number,
        this.activity.title
      ].filter((entry) => entry).join(" ");
    },
    start() {
      return this.$date.utc(this.scheduleEntry.start);
    },
    end() {
      return this.$date.utc(this.scheduleEntry.end);
    },
    startAt() {
      return this.start.format("ddd l LT");
    },
    endAt() {
      return this.start.format("ddd l") === this.end.format("ddd l") ? this.end.format("LT") : this.end.format("ddd l LT");
    },
    showHeaderData() {
      return (this.activity.location.length || this.activity.activityResponsibles().items.length) && this.showHeader;
    }
  }
};
const _hoisted_1$l = {
  wrap: false,
  "min-presence-ahead": 75
};
const _hoisted_2$b = ["id"];
const _hoisted_3$6 = ["id", "bookmark"];
const _hoisted_4$3 = { class: "schedule-entry-date" };
const _hoisted_5$2 = {
  key: 0,
  class: "schedule-entry-header"
};
const _hoisted_6$2 = { class: "schedule-entry-header-metadata" };
const _hoisted_7$1 = { class: "schedule-entry-header-metadata-entry" };
const _hoisted_8$1 = {
  key: 0,
  class: "schedule-entry-header-metadata-label"
};
const _hoisted_9$1 = { class: "schedule-entry-header-metadata" };
const _hoisted_10 = { class: "schedule-entry-header-metadata-entry" };
const _hoisted_11 = {
  key: 0,
  class: "schedule-entry-header-metadata-label"
};
function _sfc_render$m(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_CategoryLabel = resolveComponent("CategoryLabel");
  const _component_Responsibles = resolveComponent("Responsibles");
  return openBlock(), createElementBlock("View", _hoisted_1$l, [
    createBaseVNode(
      "View",
      {
        class: "schedule-entry-header-title",
        style: normalizeStyle({ borderBottomColor: $options.activity.category().color })
      },
      [
        createBaseVNode("View", {
          id: `scheduleEntry_${$props.scheduleEntry.id}`,
          class: "schedule-entry-title"
        }, [
          createVNode(_component_CategoryLabel, {
            category: $options.activity.category(),
            class: "schedule-entry-category-label"
          }, null, 8, ["category"]),
          createBaseVNode("Text", {
            id: _ctx.id,
            bookmark: $options.bookmarkTitle,
            class: "schedule-entry-number-and-title"
          }, toDisplayString($props.scheduleEntry.number) + " " + toDisplayString($options.activity.title), 9, _hoisted_3$6)
        ], 8, _hoisted_2$b),
        createBaseVNode(
          "Text",
          _hoisted_4$3,
          toDisplayString($options.startAt) + " - " + toDisplayString($options.endAt),
          1
          /* TEXT */
        )
      ],
      4
      /* STYLE */
    ),
    $options.showHeaderData ? (openBlock(), createElementBlock("View", _hoisted_5$2, [
      createBaseVNode("View", _hoisted_6$2, [
        createBaseVNode("View", _hoisted_7$1, [
          $options.activity.location ? (openBlock(), createElementBlock(
            "Text",
            _hoisted_8$1,
            toDisplayString(_ctx.$t("entity.activity.fields.location")) + ":",
            1
            /* TEXT */
          )) : createCommentVNode("v-if", true),
          createBaseVNode(
            "Text",
            null,
            toDisplayString($options.activity.location),
            1
            /* TEXT */
          )
        ])
      ]),
      _cache[0] || (_cache[0] = createBaseVNode(
        "View",
        { class: "schedule-entry-header-divider" },
        null,
        -1
        /* CACHED */
      )),
      createBaseVNode("View", _hoisted_9$1, [
        createBaseVNode("View", _hoisted_10, [
          $options.activity.activityResponsibles().items.length ? (openBlock(), createElementBlock(
            "Text",
            _hoisted_11,
            toDisplayString(_ctx.$t("entity.activity.fields.responsible")) + ":",
            1
            /* TEXT */
          )) : createCommentVNode("v-if", true),
          createVNode(_component_Responsibles, {
            activity: $options.activity,
            style: { "max-width": "200pt" }
          }, null, 8, ["activity"])
        ])
      ])
    ])) : createCommentVNode("v-if", true)
  ]);
}
if (typeof block0$e === "function") block0$e(_sfc_main$m);
const ScheduleEntryTitle = /* @__PURE__ */ _export_sfc(_sfc_main$m, [["render", _sfc_render$m], ["__file", "/app/src/campPrint/scheduleEntry/ScheduleEntryTitle.vue"]]);
const block0$d = (component) => {
  component.pdfStyle = { "column-layout-container": { "display": "flex", "flexDirection": "row" } };
};
let contentNodeComponent$1;
function setContentNodeComponent$1(component) {
  contentNodeComponent$1 = component;
}
const _sfc_main$l = {
  name: "ColumnLayout",
  extends: PdfComponent,
  props: {
    contentNode: { type: Object, required: true }
  },
  computed: {
    columns() {
      return this.contentNode.data.columns;
    },
    firstSlot() {
      return this.columns.length ? this.columns[0].slot : "1";
    },
    lastSlot() {
      return this.columns.length ? this.columns[this.columns.length - 1].slot : "1";
    },
    children() {
      return groupBy(
        sortBy(this.contentNode.children().items, (child) => parseInt(child.position)),
        (child) => child.slot
      );
    },
    contentNodeComponent() {
      return contentNodeComponent$1;
    }
  },
  methods: {
    columnStyle(slot, width) {
      return {
        borderLeft: slot === this.firstSlot ? "none" : "1pt solid black",
        padding: "2pt " + (slot === this.lastSlot ? "0" : "1%") + " 2pt " + (slot === this.firstSlot ? "0" : "1%"),
        flexBasis: width * 1e3 + "pt"
      };
    }
  }
};
const _hoisted_1$k = {
  key: 0,
  class: "column-layout-container"
};
function _sfc_render$l(_ctx, _cache, $props, $setup, $data, $options) {
  return $options.columns.length ? (openBlock(), createElementBlock("View", _hoisted_1$k, [
    (openBlock(true), createElementBlock(
      Fragment,
      null,
      renderList($options.columns, ({ slot, width }) => {
        return openBlock(), createElementBlock(
          "View",
          {
            style: normalizeStyle($options.columnStyle(slot, width))
          },
          [
            (openBlock(true), createElementBlock(
              Fragment,
              null,
              renderList($options.children[slot], (child) => {
                return openBlock(), createBlock(resolveDynamicComponent($options.contentNodeComponent), { "content-node": child }, null, 8, ["content-node"]);
              }),
              256
              /* UNKEYED_FRAGMENT */
            ))
          ],
          4
          /* STYLE */
        );
      }),
      256
      /* UNKEYED_FRAGMENT */
    ))
  ])) : createCommentVNode("v-if", true);
}
if (typeof block0$d === "function") block0$d(_sfc_main$l);
const ColumnLayout = /* @__PURE__ */ _export_sfc(_sfc_main$l, [["render", _sfc_render$l], ["__file", "/app/src/campPrint/scheduleEntry/contentNode/ColumnLayout.vue"]]);
const block0$c = (component) => {
  component.pdfStyle = { "responsive-layout__container": { "display": "flex", "flexDirection": "column" }, "responsive-layout__flex": { "display": "flex", "flexDirection": "row", "flexWrap": "wrap", "overflow": "hidden", "marginLeft": "-10pt", "marginRight": "-10pt" }, "responsive-layout__flex_item": { "flexGrow": "1", "flexBasis": "200pt", "backgroundColor": "#fff", "borderLeft": "0.75pt solid black", "marginLeft": "-1pt", "paddingLeft": "9pt", "paddingRight": "10pt" } };
};
let contentNodeComponent;
function setContentNodeComponent(component) {
  contentNodeComponent = component;
}
const _sfc_main$k = {
  name: "ResponsiveLayout",
  extends: PdfComponent,
  props: {
    contentNode: { type: Object, required: true }
  },
  computed: {
    hasChildren() {
      return this.contentNode.children().items.length > 0;
    },
    children() {
      return groupBy(
        sortBy(this.contentNode.children().items, (child) => parseInt(child.position)),
        (child) => child.slot
      );
    },
    contentNodeComponent() {
      return contentNodeComponent;
    }
  }
};
const _hoisted_1$j = {
  key: 0,
  class: "responsive-layout__container"
};
const _hoisted_2$a = { class: "responsive-layout__flex" };
const _hoisted_3$5 = { class: "responsive-layout__flex" };
function _sfc_render$k(_ctx, _cache, $props, $setup, $data, $options) {
  return $options.hasChildren ? (openBlock(), createElementBlock("View", _hoisted_1$j, [
    createBaseVNode("View", _hoisted_2$a, [
      (openBlock(true), createElementBlock(
        Fragment,
        null,
        renderList($options.children["aside-top"], (child) => {
          return openBlock(), createElementBlock("View", {
            key: child.id,
            class: "responsive-layout__flex_item"
          }, [
            (openBlock(), createBlock(resolveDynamicComponent($options.contentNodeComponent), { "content-node": child }, null, 8, ["content-node"]))
          ]);
        }),
        128
        /* KEYED_FRAGMENT */
      ))
    ]),
    createBaseVNode("View", null, [
      (openBlock(true), createElementBlock(
        Fragment,
        null,
        renderList($options.children["main"], (child) => {
          return openBlock(), createBlock(resolveDynamicComponent($options.contentNodeComponent), {
            key: child.id,
            "content-node": child
          }, null, 8, ["content-node"]);
        }),
        128
        /* KEYED_FRAGMENT */
      ))
    ]),
    createBaseVNode("View", _hoisted_3$5, [
      (openBlock(true), createElementBlock(
        Fragment,
        null,
        renderList($options.children["aside-bottom"], (child) => {
          return openBlock(), createElementBlock("View", {
            key: child.id,
            class: "responsive-layout__flex_item"
          }, [
            (openBlock(), createBlock(resolveDynamicComponent($options.contentNodeComponent), { "content-node": child }, null, 8, ["content-node"]))
          ]);
        }),
        128
        /* KEYED_FRAGMENT */
      ))
    ])
  ])) : createCommentVNode("v-if", true);
}
if (typeof block0$c === "function") block0$c(_sfc_main$k);
const ResponsiveLayout = /* @__PURE__ */ _export_sfc(_sfc_main$k, [["render", _sfc_render$k], ["__file", "/app/src/campPrint/scheduleEntry/contentNode/ResponsiveLayout.vue"]]);
const block0$b = (component) => {
  component.pdfStyle = { "content-node-title": { "borderBottom": "1.5pt solid black", "marginBottom": "1pt", "display": "flex", "flexDirection": "row", "justifyContent": "space-between", "alignItems": "baseline" }, "content-node-instance-name": { "flexGrow": "1", "fontWeight": "bold", "fontSize": "11pt", "paddingBottom": "3pt" }, "content-type-name": { "fontSize": "8pt", "fontWeight": "normal", "color": "grey" } };
};
const _sfc_main$j = {
  name: "InstanceName",
  extends: PdfComponent,
  props: {
    contentNode: { type: Object, required: true }
  },
  computed: {
    instanceName() {
      return this.contentNode.instanceName || this.contentTypeName;
    },
    contentTypeName() {
      return this.$tc(`contentNode.${camelCase(this.contentNode.contentTypeName)}.name`);
    }
  }
};
const _hoisted_1$i = { class: "content-node-title" };
const _hoisted_2$9 = { class: "content-node-instance-name" };
const _hoisted_3$4 = {
  key: 0,
  class: "content-type-name"
};
function _sfc_render$j(_ctx, _cache, $props, $setup, $data, $options) {
  return openBlock(), createElementBlock("View", _hoisted_1$i, [
    createBaseVNode(
      "Text",
      _hoisted_2$9,
      toDisplayString($options.instanceName),
      1
      /* TEXT */
    ),
    $props.contentNode.instanceName ? (openBlock(), createElementBlock(
      "Text",
      _hoisted_3$4,
      toDisplayString($options.contentTypeName),
      1
      /* TEXT */
    )) : createCommentVNode("v-if", true)
  ]);
}
if (typeof block0$b === "function") block0$b(_sfc_main$j);
const InstanceName = /* @__PURE__ */ _export_sfc(_sfc_main$j, [["render", _sfc_render$j], ["__file", "/app/src/campPrint/scheduleEntry/InstanceName.vue"]]);
const block0$a = (component) => {
  component.pdfStyle = { "checkmark": { "marginTop": "1.5pt" } };
};
const _sfc_main$i = {
  name: "Checkmark",
  extends: PdfComponent,
  props: {
    size: { type: Number, default: 12 }
  },
  computed: {
    style() {
      return { transform: `scale(${this.size / 8})` };
    }
  }
};
function _sfc_render$i(_ctx, _cache, $props, $setup, $data, $options) {
  return openBlock(), createElementBlock(
    "Svg",
    {
      class: "checkmark",
      style: normalizeStyle($options.style),
      height: "8",
      width: "8"
    },
    _cache[0] || (_cache[0] = [
      createBaseVNode(
        "Circle",
        {
          cx: "4",
          cy: "4",
          r: "4",
          fill: "green"
        },
        null,
        -1
        /* CACHED */
      ),
      createBaseVNode(
        "Polygon",
        {
          points: "3.3,4.725 2.25,3.65 1.6,4.275 3.3,5.975 6.4,2.9 5.75,2.275",
          fill: "white"
        },
        null,
        -1
        /* CACHED */
      )
    ]),
    4
    /* STYLE */
  );
}
if (typeof block0$a === "function") block0$a(_sfc_main$i);
const Checkmark = /* @__PURE__ */ _export_sfc(_sfc_main$i, [["render", _sfc_render$i], ["__file", "/app/src/campPrint/Checkmark.vue"]]);
const block0$9 = (component) => {
  component.pdfStyle = { "la-thematic-area-entry": { "display": "flex", "flexDirection": "row", "alignItems": "top" } };
};
const _sfc_main$h = {
  name: "LAThematicArea",
  components: { InstanceName, Checkmark },
  extends: PdfComponent,
  props: {
    contentNode: { type: Object, required: true }
  },
  methods: {
    translation(key) {
      return this.$tc(`contentNode.laThematicArea.entity.option.${key}.name`);
    }
  }
};
const _hoisted_1$h = { class: "content-node" };
const _hoisted_2$8 = {
  key: 0,
  class: "la-thematic-area-entry"
};
const _hoisted_3$3 = { style: { "margin-left": "2pt" } };
function _sfc_render$h(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_InstanceName = resolveComponent("InstanceName");
  const _component_Checkmark = resolveComponent("Checkmark");
  return openBlock(), createElementBlock("View", _hoisted_1$h, [
    createVNode(_component_InstanceName, { "content-node": $props.contentNode }, null, 8, ["content-node"]),
    (openBlock(true), createElementBlock(
      Fragment,
      null,
      renderList($props.contentNode.data.options, (data, key) => {
        return openBlock(), createElementBlock(
          Fragment,
          null,
          [
            data.checked ? (openBlock(), createElementBlock("View", _hoisted_2$8, [
              createVNode(_component_Checkmark, { size: 8 }),
              createBaseVNode(
                "Text",
                _hoisted_3$3,
                toDisplayString($options.translation(key)),
                1
                /* TEXT */
              )
            ])) : createCommentVNode("v-if", true)
          ],
          64
          /* STABLE_FRAGMENT */
        );
      }),
      256
      /* UNKEYED_FRAGMENT */
    ))
  ]);
}
if (typeof block0$9 === "function") block0$9(_sfc_main$h);
const LAThematicArea = /* @__PURE__ */ _export_sfc(_sfc_main$h, [["render", _sfc_render$h], ["__file", "/app/src/campPrint/scheduleEntry/contentNode/LAThematicArea.vue"]]);
const _sfc_main$g = {
  name: "LearningObjectives",
  components: { RichText, InstanceName },
  extends: PdfComponent,
  props: {
    contentNode: { type: Object, required: true }
  }
};
const _hoisted_1$g = { class: "content-node" };
const _hoisted_2$7 = { style: { "line-height": "0.8" } };
function _sfc_render$g(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_InstanceName = resolveComponent("InstanceName");
  const _component_RichText = resolveComponent("RichText");
  return openBlock(), createElementBlock("View", _hoisted_1$g, [
    createVNode(_component_InstanceName, { "content-node": $props.contentNode }, null, 8, ["content-node"]),
    createBaseVNode("View", _hoisted_2$7, [
      createVNode(_component_RichText, {
        "rich-text": $props.contentNode.data.html
      }, null, 8, ["rich-text"])
    ])
  ]);
}
const LearningObjectives = /* @__PURE__ */ _export_sfc(_sfc_main$g, [["render", _sfc_render$g], ["__file", "/app/src/campPrint/scheduleEntry/contentNode/LearningObjectives.vue"]]);
const _sfc_main$f = {
  name: "LearningTopics",
  components: { RichText, InstanceName },
  extends: PdfComponent,
  props: {
    contentNode: { type: Object, required: true }
  }
};
const _hoisted_1$f = { class: "content-node" };
const _hoisted_2$6 = { style: { "line-height": "0.8" } };
function _sfc_render$f(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_InstanceName = resolveComponent("InstanceName");
  const _component_RichText = resolveComponent("RichText");
  return openBlock(), createElementBlock("View", _hoisted_1$f, [
    createVNode(_component_InstanceName, { "content-node": $props.contentNode }, null, 8, ["content-node"]),
    createBaseVNode("View", _hoisted_2$6, [
      createVNode(_component_RichText, {
        "rich-text": $props.contentNode.data.html
      }, null, 8, ["rich-text"])
    ])
  ]);
}
const LearningTopics = /* @__PURE__ */ _export_sfc(_sfc_main$f, [["render", _sfc_render$f], ["__file", "/app/src/campPrint/scheduleEntry/contentNode/LearningTopics.vue"]]);
const block0$8 = (component) => {
  component.pdfStyle = { "storyboard-container": { "display": "flex", "flexDirection": "column" }, "storyboard-header-row": { "display": "flex", "flexDirection": "row", "borderBottom": "0.75pt solid #94A3B8" }, "storyboard-row": { "display": "flex", "flexDirection": "row" }, "storyboard-header-cell": { "lineHeight": "0.8", "fontWeight": "semibold" }, "storyboard-cell": { "lineHeight": "0.8", "paddingTop": "1pt", "paddingBottom": "3pt" }, "storyboard-column-1": { "width": "28pt", "flexShrink": "0", "flexGrow": "0", "paddingRight": "2pt", "overflow": "hidden" }, "storyboard-column-2": { "flexBasis": "0", "flexGrow": "1", "borderLeft": "0.75pt solid #94A3B8", "paddingHorizontal": "2pt" }, "storyboard-column-3": { "flexBasis": "40pt", "flexShrink": "0", "flexGrow": "0", "borderLeft": "0.75pt solid #94A3B8", "paddingLeft": "2pt", "overflow": "hidden" } };
};
const _sfc_main$e = {
  name: "Storyboard",
  components: { InstanceName, RichText },
  extends: PdfComponent,
  props: {
    contentNode: { type: Object, required: true }
  },
  computed: {
    sections() {
      const sections = this.contentNode.data.sections;
      return sortBy(
        Object.keys(sections).map((key) => ({
          key,
          column1: sections[key].column1,
          column2Html: sections[key].column2Html,
          column3: sections[key].column3,
          position: sections[key].position
        })),
        (section) => section.position
      );
    },
    anyContent() {
      return this.sections.length && (this.sections[0].column1.length || !isEmptyHtml(this.sections[0].column2Html) || this.sections[0].column3.length);
    }
  }
};
const _hoisted_1$e = { class: "content-node storyboard-container" };
const _hoisted_2$5 = {
  key: 0,
  class: "storyboard-header-row"
};
const _hoisted_3$2 = { class: "storyboard-header-cell storyboard-column-1" };
const _hoisted_4$2 = { class: "storyboard-header-cell storyboard-column-2" };
const _hoisted_5$1 = { class: "storyboard-header-cell storyboard-column-3" };
const _hoisted_6$1 = { class: "storyboard-row" };
const _hoisted_7 = { class: "storyboard-cell storyboard-column-1" };
const _hoisted_8 = { class: "storyboard-cell storyboard-column-2" };
const _hoisted_9 = { class: "storyboard-cell storyboard-column-3" };
function _sfc_render$e(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_InstanceName = resolveComponent("InstanceName");
  const _component_RichText = resolveComponent("RichText");
  return openBlock(), createElementBlock("View", _hoisted_1$e, [
    createVNode(_component_InstanceName, { "content-node": $props.contentNode }, null, 8, ["content-node"]),
    $options.anyContent ? (openBlock(), createElementBlock("View", _hoisted_2$5, [
      createBaseVNode("View", _hoisted_3$2, [
        createBaseVNode(
          "Text",
          null,
          toDisplayString(_ctx.$t("contentNode.storyboard.entity.section.fields.column1")),
          1
          /* TEXT */
        )
      ]),
      createBaseVNode("View", _hoisted_4$2, [
        createBaseVNode(
          "Text",
          null,
          toDisplayString(_ctx.$t("contentNode.storyboard.entity.section.fields.column2Html")),
          1
          /* TEXT */
        )
      ]),
      createBaseVNode("View", _hoisted_5$1, [
        createBaseVNode(
          "Text",
          null,
          toDisplayString(_ctx.$t("contentNode.storyboard.entity.section.fields.column3")),
          1
          /* TEXT */
        )
      ])
    ])) : createCommentVNode("v-if", true),
    (openBlock(true), createElementBlock(
      Fragment,
      null,
      renderList($options.sections, (section) => {
        return openBlock(), createElementBlock("View", _hoisted_6$1, [
          createBaseVNode("View", _hoisted_7, [
            createBaseVNode(
              "Text",
              null,
              toDisplayString(section.column1),
              1
              /* TEXT */
            )
          ]),
          createBaseVNode("View", _hoisted_8, [
            createVNode(_component_RichText, {
              "rich-text": section.column2Html
            }, null, 8, ["rich-text"])
          ]),
          createBaseVNode("View", _hoisted_9, [
            createBaseVNode(
              "Text",
              null,
              toDisplayString(section.column3),
              1
              /* TEXT */
            )
          ])
        ]);
      }),
      256
      /* UNKEYED_FRAGMENT */
    ))
  ]);
}
if (typeof block0$8 === "function") block0$8(_sfc_main$e);
const Storyboard = /* @__PURE__ */ _export_sfc(_sfc_main$e, [["render", _sfc_render$e], ["__file", "/app/src/campPrint/scheduleEntry/contentNode/Storyboard.vue"]]);
const _sfc_main$d = {
  name: "Notes",
  components: { RichText, InstanceName },
  extends: PdfComponent,
  props: {
    contentNode: { type: Object, required: true }
  }
};
const _hoisted_1$d = { class: "content-node" };
const _hoisted_2$4 = { style: { "line-height": "0.8" } };
function _sfc_render$d(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_InstanceName = resolveComponent("InstanceName");
  const _component_RichText = resolveComponent("RichText");
  return openBlock(), createElementBlock("View", _hoisted_1$d, [
    createVNode(_component_InstanceName, { "content-node": $props.contentNode }, null, 8, ["content-node"]),
    createBaseVNode("View", _hoisted_2$4, [
      createVNode(_component_RichText, {
        "rich-text": $props.contentNode.data.html
      }, null, 8, ["rich-text"])
    ])
  ]);
}
const Notes = /* @__PURE__ */ _export_sfc(_sfc_main$d, [["render", _sfc_render$d], ["__file", "/app/src/campPrint/scheduleEntry/contentNode/Notes.vue"]]);
const _sfc_main$c = {
  name: "SafetyConsiderations",
  components: { RichText, InstanceName },
  extends: PdfComponent,
  props: {
    contentNode: { type: Object, required: true }
  }
};
const _hoisted_1$c = { class: "content-node" };
const _hoisted_2$3 = { style: { "line-height": "0.8" } };
function _sfc_render$c(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_InstanceName = resolveComponent("InstanceName");
  const _component_RichText = resolveComponent("RichText");
  return openBlock(), createElementBlock("View", _hoisted_1$c, [
    createVNode(_component_InstanceName, { "content-node": $props.contentNode }, null, 8, ["content-node"]),
    createBaseVNode("View", _hoisted_2$3, [
      createVNode(_component_RichText, {
        "rich-text": $props.contentNode.data.html
      }, null, 8, ["rich-text"])
    ])
  ]);
}
const SafetyConsiderations = /* @__PURE__ */ _export_sfc(_sfc_main$c, [["render", _sfc_render$c], ["__file", "/app/src/campPrint/scheduleEntry/contentNode/SafetyConsiderations.vue"]]);
const block0$7 = (component) => {
  component.pdfStyle = { "material": { "display": "flex", "flexDirection": "column" }, "material-item": { "display": "flex", "flexDirection": "row" }, "material-item-column": { "flexGrow": "1", "borderBottom": "0.3pt solid black" }, "material-item-column-1": { "flexBasis": "7000pt", "paddingRight": "2pt" }, "material-item-column-2": { "flexBasis": "3000pt", "paddingLeft": "2pt" } };
};
const _sfc_main$b = {
  name: "Material",
  components: { InstanceName },
  extends: PdfComponent,
  props: {
    contentNode: { type: Object, required: true }
  },
  computed: {
    sortedMaterialItems() {
      return sortBy(
        this.contentNode.materialItems().items,
        (item) => {
          var _a;
          return item.materialList ? (_a = item.materialList()) == null ? void 0 : _a.name : "";
        }
      );
    }
  }
};
const _hoisted_1$b = { class: "content-node material" };
const _hoisted_2$2 = { class: "material-item" };
const _hoisted_3$1 = { class: "material-item-column material-item-column-1" };
const _hoisted_4$1 = { class: "material-item-column material-item-column-2" };
function _sfc_render$b(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_InstanceName = resolveComponent("InstanceName");
  return openBlock(), createElementBlock("View", _hoisted_1$b, [
    createVNode(_component_InstanceName, { "content-node": $props.contentNode }, null, 8, ["content-node"]),
    (openBlock(true), createElementBlock(
      Fragment,
      null,
      renderList($options.sortedMaterialItems, (item) => {
        var _a;
        return openBlock(), createElementBlock("View", _hoisted_2$2, [
          createBaseVNode("View", _hoisted_3$1, [
            createBaseVNode(
              "Text",
              null,
              toDisplayString(item.quantity) + " " + toDisplayString(item.unit) + " " + toDisplayString(item.article),
              1
              /* TEXT */
            )
          ]),
          createBaseVNode("View", _hoisted_4$1, [
            createBaseVNode(
              "Text",
              null,
              toDisplayString(item.materialList ? (_a = item.materialList()) == null ? void 0 : _a.name : ""),
              1
              /* TEXT */
            )
          ])
        ]);
      }),
      256
      /* UNKEYED_FRAGMENT */
    ))
  ]);
}
if (typeof block0$7 === "function") block0$7(_sfc_main$b);
const Material = /* @__PURE__ */ _export_sfc(_sfc_main$b, [["render", _sfc_render$b], ["__file", "/app/src/campPrint/scheduleEntry/contentNode/Material.vue"]]);
const _sfc_main$a = {
  name: "Storycontext",
  components: { RichText, InstanceName },
  extends: PdfComponent,
  props: {
    contentNode: { type: Object, required: true }
  }
};
const _hoisted_1$a = { class: "content-node" };
const _hoisted_2$1 = { style: { "line-height": "0.8" } };
function _sfc_render$a(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_InstanceName = resolveComponent("InstanceName");
  const _component_RichText = resolveComponent("RichText");
  return openBlock(), createElementBlock("View", _hoisted_1$a, [
    createVNode(_component_InstanceName, { "content-node": $props.contentNode }, null, 8, ["content-node"]),
    createBaseVNode("View", _hoisted_2$1, [
      createVNode(_component_RichText, {
        "rich-text": $props.contentNode.data.html
      }, null, 8, ["rich-text"])
    ])
  ]);
}
const Storycontext = /* @__PURE__ */ _export_sfc(_sfc_main$a, [["render", _sfc_render$a], ["__file", "/app/src/campPrint/scheduleEntry/contentNode/Storycontext.vue"]]);
const block0$6 = (component) => {
  component.pdfStyle = { "checklist": { "display": "flex", "flexDirection": "column", "marginBottom": "8pt" }, "checklist-title": { "fontWeight": "bold", "marginBottom": "3pt", "marginTop": "2pt" }, "checklist-item": { "display": "flex", "flexDirection": "row", "paddingBottom": "5pt" }, "checklist-item-column": { "flexGrow": "1" }, "checklist-item-column-1": { "flexBasis": "17pt", "flexShrink": "0", "flexGrow": "0", "paddingRight": "2pt", "fontVariantNumeric": "tabular-nums" }, "checklist-item-column-2": { "flexBasis": "0", "flexGrow": "1", "paddingLeft": "2pt" } };
};
const __default__ = {
  name: "Checklist",
  components: { InstanceName },
  extends: PdfComponent
};
const _sfc_main$9 = /* @__PURE__ */ Object.assign(__default__, {
  props: {
    contentNode: { type: Object, required: true }
  },
  setup(__props, { expose: __expose }) {
    __expose();
    const props = __props;
    function calculateItemNumber(item) {
      if (!item.parent) {
        return item.position + 1;
      }
      return calculateItemNumber(item.parent()) + "." + (item.position + 1);
    }
    const items = props.contentNode.checklistItems().items.map((item) => {
      const number = calculateItemNumber(item);
      return {
        ...item,
        number
      };
    });
    const checklists = uniqWith(
      props.contentNode.checklistItems().items.map((checklistItem) => checklistItem.checklist()),
      function(checklist1, checklist2) {
        return checklist1._meta.self === checklist2._meta.self;
      }
    );
    const checklistsWithItems = checklists.map((checklist) => ({
      checklist,
      items: sortBy(
        items.filter((item) => item.checklist()._meta.self === checklist._meta.self),
        "number"
      )
    }));
    const __returned__ = { props, calculateItemNumber, items, checklists, checklistsWithItems, get PdfComponent() {
      return PdfComponent;
    }, InstanceName, get uniqWith() {
      return uniqWith;
    }, get sortBy() {
      return sortBy;
    } };
    Object.defineProperty(__returned__, "__isScriptSetup", { enumerable: false, value: true });
    return __returned__;
  }
});
const _hoisted_1$9 = { class: "content-node" };
const _hoisted_2 = { class: "checklist" };
const _hoisted_3 = { class: "checklist-title" };
const _hoisted_4 = { class: "checklist-item" };
const _hoisted_5 = { class: "checklist-item-column checklist-item-column-1" };
const _hoisted_6 = { class: "checklist-item-column checklist-item-column-2" };
function _sfc_render$9(_ctx, _cache, $props, $setup, $data, $options) {
  return openBlock(), createElementBlock("View", _hoisted_1$9, [
    createVNode($setup["InstanceName"], { "content-node": $props.contentNode }, null, 8, ["content-node"]),
    (openBlock(true), createElementBlock(
      Fragment,
      null,
      renderList($setup.checklistsWithItems, (entry) => {
        return openBlock(), createElementBlock("View", _hoisted_2, [
          createBaseVNode(
            "Text",
            _hoisted_3,
            toDisplayString(entry.checklist.name),
            1
            /* TEXT */
          ),
          (openBlock(true), createElementBlock(
            Fragment,
            null,
            renderList(entry.items, (item) => {
              return openBlock(), createElementBlock("View", _hoisted_4, [
                createBaseVNode("View", _hoisted_5, [
                  createBaseVNode(
                    "Text",
                    null,
                    toDisplayString(item.number),
                    1
                    /* TEXT */
                  )
                ]),
                createBaseVNode("View", _hoisted_6, [
                  createBaseVNode(
                    "Text",
                    null,
                    toDisplayString(item.text),
                    1
                    /* TEXT */
                  )
                ])
              ]);
            }),
            256
            /* UNKEYED_FRAGMENT */
          ))
        ]);
      }),
      256
      /* UNKEYED_FRAGMENT */
    ))
  ]);
}
if (typeof block0$6 === "function") block0$6(_sfc_main$9);
const Checklist = /* @__PURE__ */ _export_sfc(_sfc_main$9, [["render", _sfc_render$9], ["__file", "/app/src/campPrint/scheduleEntry/contentNode/Checklist.vue"]]);
const block0$5 = (component) => {
  component.pdfStyle = { "content-node": { "marginBottom": "6pt" } };
};
const _sfc_main$8 = {
  name: "ContentNode",
  extends: PdfComponent,
  props: {
    contentNode: { type: Object, required: true }
  },
  computed: {
    contentTypeName() {
      return this.contentNode.contentTypeName;
    },
    contentNodeComponent() {
      return {
        ColumnLayout,
        ResponsiveLayout,
        LAThematicArea,
        LearningObjectives,
        LearningTopics,
        Storyboard,
        Notes,
        SafetyConsiderations,
        Material,
        Storycontext,
        Checklist
      }[this.contentTypeName];
    }
  }
};
const _hoisted_1$8 = { key: 1 };
function _sfc_render$8(_ctx, _cache, $props, $setup, $data, $options) {
  return $options.contentNodeComponent ? (openBlock(), createBlock(resolveDynamicComponent($options.contentNodeComponent), {
    key: 0,
    "content-node": $props.contentNode
  }, null, 8, ["content-node"])) : (openBlock(), createElementBlock(
    "Text",
    _hoisted_1$8,
    toDisplayString($options.contentTypeName),
    1
    /* TEXT */
  ));
}
if (typeof block0$5 === "function") block0$5(_sfc_main$8);
const ContentNode = /* @__PURE__ */ _export_sfc(_sfc_main$8, [["render", _sfc_render$8], ["__file", "/app/src/campPrint/scheduleEntry/contentNode/ContentNode.vue"]]);
setContentNodeComponent$1(ContentNode);
setContentNodeComponent(ContentNode);
const _sfc_main$7 = {
  name: "ScheduleEntry",
  components: { ScheduleEntryTitle, ContentNode },
  extends: PdfComponent,
  props: {
    scheduleEntry: { type: Object, required: true }
  },
  computed: {
    activity() {
      return this.scheduleEntry.activity();
    }
  }
};
const _hoisted_1$7 = { style: { "padding-bottom": "20pt", "font-size": "10pt" } };
function _sfc_render$7(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_ScheduleEntryTitle = resolveComponent("ScheduleEntryTitle");
  const _component_ContentNode = resolveComponent("ContentNode");
  return openBlock(), createElementBlock(
    Fragment,
    null,
    [
      createVNode(_component_ScheduleEntryTitle, { "schedule-entry": $props.scheduleEntry }, null, 8, ["schedule-entry"]),
      createBaseVNode("View", _hoisted_1$7, [
        createVNode(_component_ContentNode, {
          "content-node": $options.activity.rootContentNode()
        }, null, 8, ["content-node"])
      ])
    ],
    64
    /* STABLE_FRAGMENT */
  );
}
const ScheduleEntry = /* @__PURE__ */ _export_sfc(_sfc_main$7, [["render", _sfc_render$7], ["__file", "/app/src/campPrint/scheduleEntry/ScheduleEntry.vue"]]);
const block0$4 = (component) => {
  component.pdfStyle = { "program-period-title": { "fontSize": "10pt", "fontWeight": "bold", "textAlign": "center" } };
};
const _sfc_main$6 = {
  name: "ProgramPeriod",
  components: { ScheduleEntry },
  extends: PdfComponent,
  props: {
    period: { type: Object, required: true },
    filter: { type: Object, default: () => ({}) }
  },
  computed: {
    scheduleEntries() {
      return this.period.scheduleEntries().items.filter((scheduleEntry) => {
        return filterMatchScheduleEntry(scheduleEntry, this.filter);
      });
    }
  }
};
const _hoisted_1$6 = ["id", "bookmark"];
function _sfc_render$6(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_ScheduleEntry = resolveComponent("ScheduleEntry");
  return openBlock(), createElementBlock(
    Fragment,
    null,
    [
      createBaseVNode("Text", {
        id: `${_ctx.id}-${$props.period.id}`,
        bookmark: { title: $props.period.description, fit: true },
        class: "program-period-title"
      }, toDisplayString(_ctx.$t("print.program.title")) + ": " + toDisplayString($props.period.description), 9, _hoisted_1$6),
      (openBlock(true), createElementBlock(
        Fragment,
        null,
        renderList($options.scheduleEntries, (scheduleEntry) => {
          return openBlock(), createBlock(_component_ScheduleEntry, {
            id: `${_ctx.id}-${$props.period.id}-${scheduleEntry.id}`,
            "schedule-entry": scheduleEntry
          }, null, 8, ["id", "schedule-entry"]);
        }),
        256
        /* UNKEYED_FRAGMENT */
      ))
    ],
    64
    /* STABLE_FRAGMENT */
  );
}
if (typeof block0$4 === "function") block0$4(_sfc_main$6);
const ProgramPeriod = /* @__PURE__ */ _export_sfc(_sfc_main$6, [["render", _sfc_render$6], ["__file", "/app/src/campPrint/program/ProgramPeriod.vue"]]);
const block0$3 = (component) => {
  component.pdfStyle = { "program-page": { "fontSize": "8pt" } };
};
const _sfc_main$5 = {
  name: "Program",
  components: { ProgramPeriod },
  extends: PdfComponent,
  props: {
    content: { type: Object, required: true },
    config: { type: Object, required: true }
  },
  computed: {
    periods() {
      return this.content.options.periods.map((periodUri) => this.api.get(periodUri)).filter((period) => {
        return period.scheduleEntries().items.filter(
          (scheduleEntry) => filterMatchScheduleEntry(scheduleEntry, this.content.options.filter)
        ).length;
      });
    }
  }
};
const _hoisted_1$5 = ["id"];
function _sfc_render$5(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_ProgramPeriod = resolveComponent("ProgramPeriod");
  return openBlock(), createElementBlock("Page", {
    id: _ctx.id,
    class: "page program-page"
  }, [
    (openBlock(true), createElementBlock(
      Fragment,
      null,
      renderList($options.periods, (period) => {
        return openBlock(), createBlock(_component_ProgramPeriod, {
          id: _ctx.id,
          period,
          filter: $props.content.options.filter
        }, null, 8, ["id", "period", "filter"]);
      }),
      256
      /* UNKEYED_FRAGMENT */
    ))
  ], 8, _hoisted_1$5);
}
if (typeof block0$3 === "function") block0$3(_sfc_main$5);
const Program = /* @__PURE__ */ _export_sfc(_sfc_main$5, [["render", _sfc_render$5], ["__file", "/app/src/campPrint/program/Program.vue"]]);
const block0$2 = (component) => {
  component.pdfStyle = { "activity-page": { "fontSize": "8pt" } };
};
const _sfc_main$4 = {
  name: "Activity",
  components: { ScheduleEntry },
  extends: PdfComponent,
  props: {
    content: { type: Object, required: true },
    config: { type: Object, required: true }
  },
  computed: {
    scheduleEntry() {
      return this.api.get(this.content.options.scheduleEntry);
    }
  }
};
const _hoisted_1$4 = ["id"];
function _sfc_render$4(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_ScheduleEntry = resolveComponent("ScheduleEntry");
  return openBlock(), createElementBlock("Page", {
    id: _ctx.id,
    class: "page activity-page"
  }, [
    createVNode(_component_ScheduleEntry, {
      id: `${_ctx.id}-${$options.scheduleEntry.id}`,
      "schedule-entry": $options.scheduleEntry
    }, null, 8, ["id", "schedule-entry"])
  ], 8, _hoisted_1$4);
}
if (typeof block0$2 === "function") block0$2(_sfc_main$4);
const Activity = /* @__PURE__ */ _export_sfc(_sfc_main$4, [["render", _sfc_render$4], ["__file", "/app/src/campPrint/activity/Activity.vue"]]);
const _sfc_main$3 = {
  name: "ScheduleEntry",
  components: { ContentNode, ScheduleEntryTitle },
  extends: PdfComponent,
  props: {
    scheduleEntry: { type: Object, required: true },
    contentTypes: { type: Array, required: true },
    contentNodes: { type: Array, required: true }
  },
  computed: {
    contentNodeEntries() {
      return sortBy(
        this.contentNodes.map(
          (contentNodeList) => contentNodeList.filter(
            (contentNode) => contentNode.root()._meta.self === this.scheduleEntry.activity().rootContentNode()._meta.self
          )
        ),
        ["parent", "slot", "position"]
      ).flat();
    }
  }
};
const _hoisted_1$3 = { style: { "margin-top": "10pt", "padding-bottom": "20pt", "font-size": "10pt" } };
function _sfc_render$3(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_ScheduleEntryTitle = resolveComponent("ScheduleEntryTitle");
  const _component_ContentNode = resolveComponent("ContentNode");
  return openBlock(), createElementBlock(
    Fragment,
    null,
    [
      createVNode(_component_ScheduleEntryTitle, {
        "schedule-entry": $props.scheduleEntry,
        "show-header": false
      }, null, 8, ["schedule-entry"]),
      createBaseVNode("View", _hoisted_1$3, [
        (openBlock(true), createElementBlock(
          Fragment,
          null,
          renderList($options.contentNodeEntries, (contentNode) => {
            return openBlock(), createBlock(_component_ContentNode, {
              key: contentNode.id,
              "content-node": contentNode
            }, null, 8, ["content-node"]);
          }),
          128
          /* KEYED_FRAGMENT */
        ))
      ])
    ],
    64
    /* STABLE_FRAGMENT */
  );
}
const ActivityListScheduleEntry = /* @__PURE__ */ _export_sfc(_sfc_main$3, [["render", _sfc_render$3], ["__file", "/app/src/campPrint/activityList/ActivityListScheduleEntry.vue"]]);
const block0$1 = (component) => {
  component.pdfStyle = { "activity-list-period-title": { "fontSize": "10pt", "fontWeight": "bold", "textAlign": "center" } };
};
const _sfc_main$2 = {
  name: "ActivityListPeriod",
  components: { ActivityListScheduleEntry },
  extends: PdfComponent,
  props: {
    period: { type: Object, required: true },
    contentTypeNames: { type: Array, required: true },
    config: { type: Object, required: true },
    filter: { type: Object, default: () => ({}) }
  },
  computed: {
    scheduleEntries() {
      return this.period.scheduleEntries().items.filter(
        (scheduleEntry) => filterMatchScheduleEntry(scheduleEntry, this.filter)
      );
    },
    allContentTypes() {
      return this.api.get("/content_types").items;
    },
    contentTypes() {
      return this.contentTypeNames.map(
        (contentTypeName) => this.allContentTypes.find((contentType) => contentType.name === contentTypeName)
      );
    },
    contentNodes() {
      return this.contentTypes.map(
        (contentType) => this.period.contentNodes().items.filter((contentNode) => {
          return contentNode.contentType()._meta.self === contentType._meta.self;
        })
      );
    },
    title() {
      return this.$tc("print.activityList.title");
    }
  },
  methods: { camelCase }
};
const _hoisted_1$2 = ["id", "bookmark"];
function _sfc_render$2(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_ActivityListScheduleEntry = resolveComponent("ActivityListScheduleEntry");
  return openBlock(), createElementBlock(
    Fragment,
    null,
    [
      createBaseVNode("Text", {
        id: `${_ctx.id}-${$props.period.id}`,
        bookmark: { title: $options.title + ": " + $props.period.description, fit: true },
        class: "activity-list-period-title"
      }, toDisplayString($options.title) + ": " + toDisplayString($props.period.description), 9, _hoisted_1$2),
      (openBlock(true), createElementBlock(
        Fragment,
        null,
        renderList($options.scheduleEntries, (scheduleEntry) => {
          return openBlock(), createBlock(_component_ActivityListScheduleEntry, {
            id: _ctx.id,
            "schedule-entry": scheduleEntry,
            "content-types": $options.contentTypes,
            "content-nodes": $options.contentNodes
          }, null, 8, ["id", "schedule-entry", "content-types", "content-nodes"]);
        }),
        256
        /* UNKEYED_FRAGMENT */
      ))
    ],
    64
    /* STABLE_FRAGMENT */
  );
}
if (typeof block0$1 === "function") block0$1(_sfc_main$2);
const ActivityListPeriod = /* @__PURE__ */ _export_sfc(_sfc_main$2, [["render", _sfc_render$2], ["__file", "/app/src/campPrint/activityList/ActivityListPeriod.vue"]]);
const _sfc_main$1 = {
  name: "ActivityList",
  components: { ActivityListPeriod },
  extends: PdfComponent,
  props: {
    content: { type: Object, required: true },
    config: { type: Object, required: true }
  },
  computed: {
    periods() {
      return this.content.options.periods.map((periodUri) => this.api.get(periodUri));
    }
  }
};
const _hoisted_1$1 = ["id"];
function _sfc_render$1(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_ActivityListPeriod = resolveComponent("ActivityListPeriod");
  return openBlock(), createElementBlock("Page", {
    id: _ctx.id,
    class: "page"
  }, [
    (openBlock(true), createElementBlock(
      Fragment,
      null,
      renderList($options.periods, (period) => {
        return openBlock(), createBlock(_component_ActivityListPeriod, {
          id: _ctx.id,
          period,
          config: $props.config,
          "content-type-names": ["LearningObjectives", "LearningTopics", "Checklist"],
          filter: $props.content.options.filter
        }, null, 8, ["id", "period", "config", "filter"]);
      }),
      256
      /* UNKEYED_FRAGMENT */
    ))
  ], 8, _hoisted_1$1);
}
const ActivityList = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["render", _sfc_render$1], ["__file", "/app/src/campPrint/activityList/ActivityList.vue"]]);
const block0 = (component) => {
  component.pdfStyle = { "page": { "fontFamily": "InterDisplay", "padding": "30", "fontSize": "12", "display": "flex", "flexDirection": "column" }, "h1": { "fontSize": "16", "fontWeight": "semibold", "margin": "12pt 0 3pt" }, "h2": { "fontSize": "14", "fontWeight": "semibold", "margin": "10pt 0 3pt" }, "h3": { "fontSize": "12", "fontWeight": "semibold", "margin": "8pt 0 3pt" } };
};
const originalHyphenationCallback = wordHyphenation();
const _sfc_main = {
  name: "CampPrint",
  extends: PdfComponent,
  props: {
    config: { type: Object, required: true }
  },
  computed: {
    components() {
      return {
        Cover: Cover$1,
        Toc: TableOfContents,
        Picasso,
        Program,
        Activity,
        Story,
        SafetyConsiderations: SafetyConsiderations$1,
        ActivityList
      };
    }
  }
};
const registerFonts = async () => {
  Font.registerHyphenationCallback((word) => {
    if (word && word.length > 70) {
      return word.split("");
    }
    return originalHyphenationCallback(word);
  });
  Font.register({
    family: "InterDisplay",
    fonts: [
      { src: InterDisplay },
      { src: InterDisplayMedium, fontWeight: "medium" },
      { src: InterDisplaySemiBold, fontWeight: "semibold" },
      { src: InterDisplayBold, fontWeight: "bold" },
      { src: InterDisplayItalic, fontStyle: "italic" },
      { src: InterDisplayBoldItalic, fontWeight: "bold", fontStyle: "italic" }
    ]
  });
  Font.registerEmojiSource({
    formag: "png",
    url: "/twemoji/assets/72x72/"
  });
  return await Promise.all([
    Font.load({ fontFamily: "InterDisplay" }),
    Font.load({ fontFamily: "InterDisplay", fontWeight: 600 }),
    Font.load({ fontFamily: "InterDisplay", fontWeight: 700 }),
    Font.load({ fontFamily: "InterDisplay", fontStyle: "italic" }),
    Font.load({ fontFamily: "InterDisplay", fontWeight: 600, fontStyle: "italic" }),
    Font.load({ fontFamily: "InterDisplay", fontWeight: 700, fontStyle: "italic" })
  ]);
};
const prepare = async (config) => {
  return await registerFonts();
};
const _hoisted_1 = { "pdf-version": "1.7" };
function _sfc_render(_ctx, _cache, $props, $setup, $data, $options) {
  return openBlock(), createElementBlock("Document", _hoisted_1, [
    (openBlock(true), createElementBlock(
      Fragment,
      null,
      renderList($props.config.contents, (content, idx) => {
        return openBlock(), createElementBlock(
          Fragment,
          null,
          [
            content.type in $options.components ? (openBlock(), createBlock(resolveDynamicComponent($options.components[content.type]), {
              key: 0,
              id: `entry-${idx}`,
              config: $props.config,
              content
            }, null, 8, ["id", "config", "content"])) : createCommentVNode("v-if", true)
          ],
          64
          /* STABLE_FRAGMENT */
        );
      }),
      256
      /* UNKEYED_FRAGMENT */
    ))
  ]);
}
if (typeof block0 === "function") block0(_sfc_main);
const CampPrint = /* @__PURE__ */ _export_sfc(_sfc_main, [["render", _sfc_render], ["__file", "/app/src/CampPrint.vue"]]);
const render = (props = {}) => pdf(CampPrint, props);
export {
  prepare,
  render
};
