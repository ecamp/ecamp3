<template>
  <table style="width: 100%; border-spacing: 0" :style="getRowStyle()">
    <tr>
      <td style="width: 30px; padding: 2px">({{ checklistItem.position + 1 }})</td>
      <td style="text-wrap: wrap">
        {{ checklistItem.text }}
      </td>
      <td style="width: 400px">
        <div
          v-for="activity in getActivities(checklistItem)"
          :key="activity._meta.self"
          style="display: inline"
        >
          <ScheduleEntryLinks :activity-promise="activity._meta.load" />
        </div>
      </td>
      <td style="width: 30px">
        <v-btn
          v-if="getActivities(checklistItem).length > 0"
          icon
          @click="copyToClipboard(checklistItem)"
        >
          <v-icon>mdi-content-copy</v-icon>
        </v-btn>
      </td>
    </tr>
    <tr
      v-for="subItem in sortBy(checklistItem.children().items, (c) => c.position)"
      :key="subItem._meta.self"
    >
      <td style="width: 30px"></td>
      <td colspan="4">
        <ChecklistItemTree :checklist-item="subItem" />
      </td>
    </tr>
  </table>
</template>

<script>
import { sortBy } from 'lodash'
import ScheduleEntryLinks from '../material/ScheduleEntryLinks.vue'

export default {
  name: 'ChecklistItemTree',
  components: {
    ScheduleEntryLinks,
  },
  props: {
    checklistItem: { type: Object, required: true },
  },

  methods: {
    getActivities(checklistItem) {
      const camp = checklistItem.checklist().camp()
      const activities = camp.activities().items
      const checklistNodes = checklistItem.checklistNodes().items

      return activities.filter((a) =>
        checklistNodes.some((cn) => cn.root().id == a.rootContentNode().id)
      )
    },
    getRowStyle() {
      return this.checklistItem.position % 2 == 0
        ? { backgroundColor: 'rgba(0, 0, 0, 0.1)' }
        : { backgroundColor: 'rgba(0, 0, 0, 0.2)' }
    },
    copyToClipboard(checklistItem) {
      const activities = this.getActivities(checklistItem)

      console.log(activities)
      console.log(activities.map((a) => a.scheduleEntries().items))

      const scheduleEntries = activities
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

<style scoped></style>
