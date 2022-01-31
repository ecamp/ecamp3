<template>
  <div class="wrapper">
    <div class="instance-name">{{ instanceName }}</div>
    <div v-for="item in items" :key="item.id" class="item">
      <div class="material-row">
        <div class="item">
          {{ item.quantity }} {{ item.unit }} {{ item.article }}
        </div>
        <div class="list-name">{{ item.materialList().name }}</div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    contentNode: { type: Object, required: true },
  },
  async fetch() {
    await this.contentNode.materialItems().$loadItems()
  },
  computed: {
    instanceName() {
      return (
        this.contentNode.instanceName || this.$tc(`contentNode.material.name`)
      )
    },
    items() {
      return this.contentNode.materialItems().items
    },
  },
}
</script>

<style scoped lang="scss">
.instance-name {
  font-weight: bold;
}
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
