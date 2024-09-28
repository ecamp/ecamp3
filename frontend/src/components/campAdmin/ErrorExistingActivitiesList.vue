<template>
  <div>
    <p class="mb-2">
      {{ $tc('components.campAdmin.errorExistingActivitiesList.description') }}
    </p>
    <ul class="list">
      <li v-for="activity in existingActivities" :key="activity._meta.self" class="mt-2">
        <strong>{{ activity.title }}</strong>
        <ul>
          <li
            v-for="scheduleEntry in activity.scheduleEntries().items"
            :key="scheduleEntry._meta.self"
          >
            <router-link
              :to="{
                name: 'camp/activity',
                params: {
                  campId: camp.id,
                  scheduleEntryId: scheduleEntry.id,
                },
              }"
              class="e-title-link"
            >
              <strong>{{ scheduleEntry.number }}</strong>
              {{ rangeShort(scheduleEntry.start, scheduleEntry.end, translate) }}
            </router-link>
          </li>
        </ul>
      </li>
    </ul>
  </div>
</template>

<script>
import { rangeShort } from '@/common/helpers/dateHelperUTCFormatted.js'
export default {
  name: 'ErrorExistingActivitiesList',
  props: {
    camp: { type: Object, required: true },
    existingActivities: { type: Array, required: true },
  },
  methods: {
    rangeShort,
    translate(...args) {
      return this.$tc(...args)
    },
  },
}
</script>
<style scoped>
.list {
  list-style: none;
  padding-left: 0;
}
</style>
