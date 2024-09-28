<template>
    <table style="width: 100%;">
        <tr>
            <td style="width: 30px;">
                ({{ checklistItem.position + 1 }})
            </td>
            <td style="text-wrap: wrap;">
                {{ checklistItem.text }}
            </td>
            <td style="width: 400px">
                <div
                    v-for="activity in this.getActivities(checklistItem)"
                    :key="activity._meta.self"
                    style="display: inline"
                >
                    <div
                        v-for="scheduleEntry in activity.scheduleEntries().items"
                        :key="scheduleEntry._meta.self"
                        style="display: inline"
                    >
                        ({{ scheduleEntry.number }})
                    </div>
                    {{ activity.title }}
                </div>
                
            </td>
        </tr>
        <tr
            v-for="subItem in this.sortBy(checklistItem.children().items, c => c.position)"
            :key="subItem._meta.self"
        >
            <td style="width: 30px;"></td>
            <td colspan="2">
                <ChecklistItemTree :checklistItem="subItem" />
            </td>
        </tr>
    </table>

</template>

<script>
import { sortBy } from 'lodash'

export default {
  name: 'ChecklistItemTree',

  props: {
    checklistItem: { type: Object, required: true },
  },

  methods: {
    getActivities(checklistItem) {
        const camp = checklistItem.checklist().camp()
        const activities = camp.activities().items
        const checklistNodes = checklistItem.checklistNodes().items

        return activities.filter(a => checklistNodes.some(cn => cn.root().id == a.rootContentNode().id))
    },
    sortBy
  }
}

</script>
