export const presentationAttributesMixin = {
  props: {
    color: { type: String, default: undefined },
    dominantBaseline: { type: String, default: 'auto' },
    fill: { type: String, default: undefined },
    fillOpacity: { type: [String, Number], default: 1 },
    fillRule: { type: String, default: 'nonzero' },
    opacity: { type: [String, Number], default: 1 },
    stroke: { type: String, default: undefined },
    strokeWidth: { type: [String, Number], default: 1 },
    strokeOpacity: { type: [String, Number], default: 1 },
    strokeLinecap: { type: String, default: 'butt' },
    strokeDasharray: { type: String, default: undefined },
    transform: { type: String, default: undefined },
    textAnchor: { type: String, default: undefined },
    visibility: { type: String, default: 'visible' },
  },
  computed: {
    presentProps() {
      // Props take precedence over style in react-pdf, so we must not pass props which
      // have an undefined value set, in order to let style attributes through.
      return Object.fromEntries(
        Object.entries(this.$props).filter(([_, value]) => value !== undefined)
      )
    },
  },
}
