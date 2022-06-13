<template>
  <span>
    <span v-for="(scheduleEntry, index) in items" :key="scheduleEntry._meta.self">
      <router-link :to="scheduleEntryRoute(scheduleEntry)" small class="short-button">
        {{ getScheduleEntryCaption(scheduleEntry) }}
      </router-link>
      <span v-if="index + 1 < items.length"><br></span>
    </span>
  </span>
</template>

<script>
import { scheduleEntryRoute } from '@/router.js'

export default {
  name: 'MaterialTable',
  components: {},
  props: {
    activity: { type: Function, required: true }
  },
  computed: {
    items () {
      return this.activity().scheduleEntries().items
    }
  },
  methods: {
    scheduleEntryRoute,
    getScheduleEntryCaption (scheduleEntry) {
      const number = scheduleEntry.number
      const title = scheduleEntry.activity().title

      if (this.$vuetify.breakpoint.smAndUp) {
        if (title.length > 13) {
          return number + ': ' + title.substr(0, 13) + '...'
        } else {
          return number + ': ' + title
        }
      } else {
        return number
      }
    }
  }
}
</script>
