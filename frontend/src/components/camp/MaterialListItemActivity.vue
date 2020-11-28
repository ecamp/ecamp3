<template>
  <tr>
    <td>
      <div class="font-size-16 text-align-right">
        {{ item.materialItem.quantity }}
      </div>
    </td>
    <td class="font-size-16">
      {{ item.materialItem.unit }}
    </td>
    <td class="font-size-16">
      {{ item.materialItem.article }}
    </td>
    <td class="font-size-16">
      <router-link
        :to="scheduleEntryRoute(camp, item.scheduleEntry)">
        {{ item.scheduleEntry.number }}:
        {{ item.scheduleEntry.activity().title }}
      </router-link>
    </td>
    <td />
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
  .text-align-right {
    text-align: right;
    margin-right: 9px;
  }
  .font-size-16 {
    font-size: 16px !important;
  }
</style>
