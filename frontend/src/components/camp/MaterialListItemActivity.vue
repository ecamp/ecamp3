<template>
  <tr>
    <td>
      {{ item.materialItem.quantity }}
    </td>
    <td>
      {{ item.materialItem.unit }}
    </td>
    <td>
      {{ item.materialItem.article }}
    </td>
    <td>
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
