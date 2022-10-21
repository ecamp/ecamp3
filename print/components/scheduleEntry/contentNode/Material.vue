<template>
  <content-node-content :content-node="contentNode" :icon-path="mdiPackageVariant">
    <generic-error-message v-if="$fetchState.error" :error="$fetchState.error" />
    <div v-for="item in items" v-else :key="item.id" class="item">
      <div class="material-row">
        <div class="item">{{ item.quantity }} {{ item.unit }} {{ item.article }}</div>
        <div class="list-name">{{ item.materialList().name }}</div>
      </div>
    </div>
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
}

.list-name {
  flex-basis: 3000px;
  padding-left: 4px;
}
</style>
