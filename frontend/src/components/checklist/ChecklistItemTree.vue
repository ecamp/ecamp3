<template>
  <table style="width: 100%; border-spacing: 0" :style="getRowStyle()">
    <tr>
      <td colspan="2" style="text-wrap: wrap; line-height: 36px; padding-left: 6px">
        {{ getPositionNumber(checklistItem) }})
        {{ checklistItem.text }}
      </td>
      <td style="max-width: 400px; text-align: right">
        <div
          v-for="activity in activities"
          :key="activity._meta.self"
          style="display: inline; text-wrap: nowrap; padding-right: 4px"
        >
          <ScheduleEntryLinks :activity-promise="activity._meta.load" />
        </div>
      </td>
      <td style="width: 30px">
        <v-btn v-if="activities.length > 0" icon @click="copyToClipboard()">
          <v-icon>mdi-content-copy</v-icon>
        </v-btn>
      </td>
    </tr>
    <tr
      v-for="subItem in sortBy(checklistItem.children().items, (c) => c.position)"
      :key="subItem._meta.self"
    >
      <td style="width: 20px"></td>
      <td colspan="3">
        <ChecklistItemTree
          :checklist-item="subItem"
          :all-checklist-nodes="allChecklistNodes"
        />
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
    allChecklistNodes: { type: Array, required: true },
  },
  data() {
    return {
      checklistNodes: [],
    }
  },
  computed: {
    activities() {
      const camp = this.checklistItem.checklist().camp()
      const activities = camp.activities().items

      // Activities ordered first ScheduleEntry start-time
      return sortBy(
        activities.filter((a) =>
          this.checklistNodes.some((cn) => cn.root().id == a.rootContentNode().id)
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
          cn.checklistItems().items.some((ci) => ci.id == this.checklistItem.id)
        )
      },
    },
  },
  methods: {
    getRowStyle() {
      const globalPos = this.getTotalNumberOfItemsAbove(this.checklistItem)
      return globalPos % 2 == 0
        ? { backgroundColor: '#EEE' }
        : { backgroundColor: '#DDD' }
    },

    getTotalNumberOfItemsAbove(checklistItem) {
      let globalPos = 0

      if (checklistItem.parent == null) {
        const checklist = checklistItem.checklist()
        globalPos = checklist
          .checklistItems()
          .items.filter((c) => c.parent == null)
          .filter((c) => c.position < checklistItem.position)
          .reduce((cnt, item) => {
            return cnt + this.getTotalNumberOfItems(item)
          }, 0)
      } else {
        const parent = checklistItem.parent()
        globalPos =
          1 +
          this.getTotalNumberOfItemsAbove(parent) +
          parent
            .children()
            .items.filter((c) => c.position < checklistItem.position)
            .reduce((cnt, item) => {
              return cnt + this.getTotalNumberOfItems(item)
            }, 0)
      }

      return globalPos
    },

    getTotalNumberOfItems(checklistItem) {
      return (
        1 +
        checklistItem.children().items.reduce((cnt, item) => {
          return cnt + this.getTotalNumberOfItems(item)
        }, 0)
      )
    },

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

<style scoped></style>
