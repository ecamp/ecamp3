<template>
  <tr>
    <td class="font-size-16 text-align-bottom">
      <div class="text-align-right">
        {{ item.materialItem.quantity }}
      </div>
    </td>
    <td class="font-size-16 text-align-bottom">
      {{ item.materialItem.unit }}
    </td>
    <td class="font-size-16 text-align-bottom">
      {{ item.materialItem.article }}
    </td>
    <td style="text-align: center;">
      <v-btn
        small
        class="short-button"
        :to="scheduleEntryRoute(camp, item.scheduleEntry)">
        {{ scheduleEntryCaption }}
      </v-btn>
    </td>
  </tr>
</template>

<script>
import { scheduleEntryRoute } from '@/router.js'

export default {
  name: 'MaterialListItemContentNode',
  components: {
  },
  props: {
    camp: { type: Object, required: true },
    item: { type: Object, required: true }
  },
  computed: {
    scheduleEntry () {
      return this.item.scheduleEntry
    },
    activity () {
      return this.scheduleEntry.activity()
    },
    scheduleEntryCaption () {
      if (this.$vuetify.breakpoint.smAndUp) {
        if (this.activity.title.length > 13) {
          return this.scheduleEntry.number + ': ' + this.activity.title.substr(0, 13) + '...'
        } else {
          return this.scheduleEntry.number + ': ' + this.activity.title
        }
      } else {
        return this.scheduleEntry.number
      }
    }
  },
  methods: {
    scheduleEntryRoute
  }
}
</script>

<style scoped>
  .short-button {
    min-width: 40px !important;
    padding: 0 7px !important;
  }
  .text-align-right {
    text-align: right;
    padding-right: 9px !important;
  }
  .text-align-bottom {
    vertical-align: bottom;
  }
  .font-size-16 {
    font-size: 16px !important;
  }
</style>
