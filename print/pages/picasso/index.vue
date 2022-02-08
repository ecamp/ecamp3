<template>
  <div>
    <schedule-entry
      v-for="scheduleEntry in scheduleEntries"
      :key="scheduleEntry.id"
      :schedule-entry="scheduleEntry"
    />
  </div>
</template>

<script>
import {sortBy} from 'lodash'
// import PicassoLandscape from '../../components/PicassoLandscape.vue'
import ScheduleEntry from '../../components/scheduleEntry/ScheduleEntry.vue'

export default {
  components: {
    ScheduleEntry,
    // PicassoLandscape,
  },
  layout: 'no-vuetify',
  data() {
    return {
      config: {},
      pagedjs: '',
      period: {
        description: 'Demo',
        start: '2019-01-06',
        end: '2019-01-11',
      },
      isDemo: true,
    }
  },
  async fetch() {
    const query = this.$route.query

    if (query.period) {
      // TODO prevent specifying arbitrary absolute URLs that the print container should fetch...
      this.period = await this.$api.get(query.period)._meta.load

      // Load all data that we can here, to avoid n+1 queries
      // prettier-ignore
      await Promise.all([
        this.period.camp()._meta.load,
        this.period.scheduleEntries().$loadItems(),
      ])
      this.isDemo = false
    }
  },
  computed: {
    scheduleEntries() {
      return sortBy(this.period.scheduleEntries().items, ['dayNumber', 'scheduleEntryNumber'])
    },
  },
}
</script>
