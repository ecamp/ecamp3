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
}
