<script>
export const SUMMARY_CONTENTTYPES = ['Storycontext', 'SafetyConsiderations']

export default {
  name: 'SummaryConfig',
  props: {
    value: { type: Object, required: true },
    camp: { type: Object, required: true },
  },
  data() {
    return {
      contentTypes: SUMMARY_CONTENTTYPES,
    }
  },
  computed: {
    options: {
      get() {
        return this.value
      },
      set(v) {
        this.$emit('input', v)
      },
    },
    periods() {
      return this.camp.periods().items.map((p) => ({
        value: p._meta.self,
        text: p.description,
      }))
    },
  },
}
</script>
