<template>
  <pdf-text v-bind="presentProps">
    <slot />
  </pdf-text>
</template>
<script>
import { presentationAttributesMixin } from './presentationAttributesMixin.js'

// https://react-pdf.org/components#text
export default {
  name: 'Text',
  mixins: [presentationAttributesMixin],
  props: {
    wrap: { type: Boolean, default: true },
    render: { type: Function, default: undefined },
    // "style" is not an allowed prop name in Vue, even though we don't render to HTML
    //style: { type: [Object, Array], default: undefined },
    debug: { type: Boolean, default: false },
    fixed: { type: Boolean, default: false },
    hyphenationCallback: { type: Function, default: undefined },
    id: { type: String, default: undefined },
    bookmark: { type: [String, Object], default: undefined },
    // Props used inside SVG images:
    // https://react-pdf.org/svg#text
    x: { type: [String, Number], default: undefined },
    y: { type: [String, Number], default: undefined },
  },
}
</script>
