<script>
import { filterMatchScheduleEntry } from '@/common/helpers/filterMatchScheduleEntry.js'

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
    selectedPeriods() {
      if (!this.options.filter.period) return this.camp.periods().items
      return this.camp.periods().items.filter((period) => {
        return this.filter.periods.includes(period._meta.self)
      })
    },
  },
  methods: {
    filterFn() {
      return (filter) =>
        this.selectedPeriods
          .flatMap((period) => period.scheduleEntries().items)
          .filter((scheduleEntry) => filterMatchScheduleEntry(scheduleEntry, filter))
    },
    updateFilter(newFilter) {
      this.options.filter = newFilter
      this.$emit('input')
    },
  },
}
</script>
