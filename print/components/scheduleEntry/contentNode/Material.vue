<template>
  <content-node-content :content-node="contentNode" :icon-path="mdiPackageVariant">
    <generic-error-message v-if="error" :error="error" />
    <table v-else>
      <tr
        v-for="item in items"
        :key="item.id"
        class="item tw-tabular-nums tw-break-anywhere"
      >
        <td align="right">
          {{ item.quantity }}
        </td>
        <td>{{ item.unit || (item.quantity && '×') }}</td>
        <td width="65%">
          {{ item.article }}
        </td>
        <td width="30%">
          {{ item.materialList().name }}
        </td>
      </tr>
    </table>
  </content-node-content>
</template>

<script setup>
const props = defineProps({
  contentNode: { type: Object, required: true },
})

const { error } = await useAsyncData(
  `ContentNodeMaterial-${props.contentNode._meta.self}`,
  async () => {
    await props.contentNode.materialItems().$loadItems()
  }
)
</script>

<script>
import ContentNodeContent from './ContentNodeContent.vue'
import { mdiPackageVariant } from '@mdi/js'

export default {
  components: {
    ContentNodeContent,
  },
  data() {
    return {
      mdiPackageVariant,
    }
  },
  computed: {
    items() {
      return this.contentNode.materialItems().items
    },
  },
}
</script>

<style scoped lang="scss">
.item {
  flex-basis: 7000px;
  padding-right: 4px;

  td:not(:last-child) {
    padding-right: 4px;
  }
}
</style>
