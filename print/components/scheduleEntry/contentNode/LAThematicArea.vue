<template>
  <div class="wrapper">
    <div class="instance-name">{{ instanceName }}</div>
    <div v-for="entry in selectedOptions" :key="entry.id" class="entry">
      <check-mark :size="12" />
      {{
        $tc(
          `contentNode.laThematicArea.entity.option.${entry.translateKey}.name`
        )
      }}
    </div>
  </div>
</template>

<script>
import CheckMark from '../../CheckMark.vue'

export default {
  components: {
    CheckMark,
  },
  props: {
    contentNode: { type: Object, required: true },
  },
  async fetch() {
    await this.contentNode.options().$loadItems()
  },
  computed: {
    instanceName() {
      return (
        this.contentNode.instanceName ||
        this.$tc(`contentNode.laThematicArea.name`)
      )
    },
    selectedOptions() {
      return this.contentNode.options().items.filter((item) => item.checked)
    },
  },
}
</script>

<style scoped lang="scss">
.wrapper {
  margin-bottom: 12px;
}
.instance-name {
  font-weight: bold;
}
</style>
