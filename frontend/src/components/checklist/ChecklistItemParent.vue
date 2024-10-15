<template>
  <tr
    class="ec-checklist-check-item vertical-baseline"
    :class="{ 'ec-checklist-check-item--is-parent': !depth }"
  >
    <th class="text-left pl-4 pr-4" :class="{ 'font-weight-regular': depth }">
      {{ getPositionNumber(checklistItem) }}
    </th>
    <td
      class="pr-1"
      style="text-wrap: wrap"
      :class="{
        'pt-8 pb-1': !depth && checklistItem.position !== 0,
        'pt-1 pb-1': (!depth && checklistItem.position === 0) || !depth,
        'py-1': depth,
      }"
    >
      <p class="ma-0" :class="{ 'font-weight-bold': !depth }">{{ checklistItem.text }}</p>
      <div class="d-md-none d-flex gap-2 align-center my-1" style="font-size: 80%">
        <ScheduleEntryLinks
          v-for="activity in activities"
          :key="activity._meta.self"
          :activity-promise="activity._meta.load"
        />
        <v-btn
          v-if="activities.length > 0"
          class="d-inline-block ml-auto my-n1"
          icon
          @click="copyToClipboard(checklistItem)"
        >
          <v-icon>mdi-clipboard-check-multiple-outline</v-icon>
        </v-btn>
      </div>
    </td>
    <td class="pr-4" style="font-size: 80%; vertical-align: text-top">
      <div class="d-none d-md-flex align-center my-1">
        <v-btn
          v-if="activities.length > 0"
          icon
          small
          @click="copyToClipboard(checklistItem)"
        >
          <v-icon small>mdi-clipboard-check-multiple-outline</v-icon>
        </v-btn>
        <div class="d-grid">
          <ScheduleEntryLinks
            v-for="activity in activities"
            :key="activity._meta.self"
            :activity-promise="activity._meta.load"
          />
        </div>
      </div>
    </td>
  </tr>
</template>

<script>
import { sortBy } from 'lodash'
import ScheduleEntryLinks from '../material/ScheduleEntryLinks.vue'

export default {
  name: 'ChecklistItemParent',
  components: {
    ScheduleEntryLinks,
  },
  props: {
    checklistItem: { type: Object, required: true },
    depth: { type: Number, required: true },
    allChecklistNodes: { type: Array, required: true },
  },
  computed: {
    activities() {
      const camp = this.checklistItem.checklist().camp()
      const activities = camp.activities().items

      // Activities ordered first ScheduleEntry start-time
      return sortBy(
        activities.filter((a) =>
          this.checklistNodes.some((cn) => cn.root().id === a.rootContentNode().id)
        ),
        (activity) =>
          activity
            .scheduleEntries()
            .items.map(
              (s) =>
                `${s.dayNumber}`.padStart(3, '0') +
                `${s.scheduleEntryNumber}`.padStart(3, '0')
            )
            .reduce((p, v) => (p < v ? p : v))
      )
    },
  },
  watch: {
    allChecklistNodes: {
      immediate: true,
      handler(allChecklistNodes) {
        this.checklistNodes = allChecklistNodes.filter((cn) =>
          cn.checklistItems().items.some((ci) => ci.id === this.checklistItem.id)
        )
      },
    },
  },

  methods: {
    getPositionNumber(checklistItem) {
      if (checklistItem.parent == null) {
        return 1 + checklistItem.position
      }

      return (
        this.getPositionNumber(checklistItem.parent()) +
        '.' +
        (1 + checklistItem.position)
      )
    },

    copyToClipboard() {
      const scheduleEntries = this.activities
        .map((a) => a.scheduleEntries().items)
        .reduce(function (items, item) {
          return items.concat(item)
        }, [])
        .map((s) => s.number)
        .join(', ')

      navigator.clipboard.writeText(scheduleEntries)
    },
    sortBy,
  },
}
</script>

<style scoped>
.ec-checklist-check-item :is(th, td) {
  border-bottom: 1px solid rgba(0, 0, 0, 0.2);
}

.ec-checklist-check-item--is-parent :is(th, td) {
  border-bottom: 1px solid rgba(0, 0, 0, 0.6);
}

.ec-checklist-check-item:hover :is(th, td) {
  background-color: #f5f5f5;
}
</style>
