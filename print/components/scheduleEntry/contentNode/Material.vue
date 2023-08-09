<template>
  <content-node-content :content-node="contentNode" :icon-path="mdiPackageVariant">
    <generic-error-message v-if="$fetchState.error" :error="$fetchState.error" />
    <table v-else>
      <tr v-for="item in items" :key="item.id" class="item tw-tabular-nums">
        <td align="right">{{ item.quantity }}</td>
        <td>{{ item.unit || (item.quantity && '×') }}</td>
        <td width="65%">{{ item.article }}</td>
        <td width="30%">{{ item.materialList().name }}</td>
      </tr>
    </table>
  </content-node-content>
</template>

<script>
import ContentNodeContent from './ContentNodeContent.vue'
import { mdiPackageVariant } from '@mdi/js'

export default {
  components: {
    ContentNodeContent,
  },
  props: {
    contentNode: { type: Object, required: true },
  },
  data() {
    return {
      mdiPackageVariant,
    }
  },
  async fetch() {
    await this.contentNode.materialItems().$loadItems()
  },
  computed: {
    items() {
      return this.contentNode.materialItems().items
    },
  },
}
</script>

<style scoped lang="scss">
.material-row {
  display: flex;
  flex-direction: row;
}

.item {
  flex-basis: 7000px;
  padding-right: 4px;

  td:not(:last-child) {
    padding-right: 4px;
  }
}

.list-name {
  flex-basis: 3000px;
  padding-left: 4px;
}
</style>
