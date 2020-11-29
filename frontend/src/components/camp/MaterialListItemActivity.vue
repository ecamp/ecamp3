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
        <template v-if="$vuetify.breakpoint.smAndUp">
          {{ item.scheduleEntry.number }}:
          <template v-if="item.scheduleEntry.activity().title.length > 15">
            {{ item.scheduleEntry.activity().title.substr(0, 13) }}...
          </template>
          <template v-else>
            {{ item.scheduleEntry.activity().title }}
          </template>
        </template>
        <template v-else>
          {{ item.scheduleEntry.number }}
        </template>
      </v-btn>
    </td>
  </tr>
</template>

<script>
import { scheduleEntryRoute } from '@/router'

export default {
  name: 'MaterialListItemActivity',
  components: {
  },
  props: {
    camp: { type: Object, required: true },
    item: { type: Object, required: true }
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
