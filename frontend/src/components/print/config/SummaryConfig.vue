<script>
import { filterMatchScheduleEntry } from '@/common/helpers/filterMatchScheduleEntry.js'

export const SUMMARY_CONTENTTYPES = ['Storycontext', 'SafetyConsiderations']

export default {
  name: 'SummaryConfig',
  props: {
    modelValue: { type: Object, required: true },
    camp: { type: Object, required: true },
  },
  emits: ['update:modelValue'],
  data() {
    return {
      contentTypes: SUMMARY_CONTENTTYPES,
    }
  },
  computed: {
    options: {
      get() {
        return this.modelValue
      },
      set(v) {
        this.$emit('update:modelValue', v)
      },
    },
    periods() {
      return this.camp.periods().items.map((p) => ({
        value: p._meta.self,
        text: p.description,
      }))
    },
    selectedPeriods() {
      if (!this.options.periods) return this.camp.periods().items
      return this.camp.periods().items.filter((period) => {
        return this.options.periods.includes(period._meta.self)
      })
    },
    selectedScheduleEntries() {
      return this.selectedPeriods.flatMap((period) => period.scheduleEntries().items)
    },
  },
  methods: {
    filterFn() {
      return (filter) =>
        this.selectedScheduleEntries.filter((scheduleEntry) =>
          filterMatchScheduleEntry(scheduleEntry, filter)
        )
    },
    updateFilter(newFilter) {
      this.options.filter = newFilter
      this.$emit('update:modelValue', this.options)
    },
  },
}
</script>
