<template>
  <div class="wrapper">
    <div class="instance-name">{{ instanceName }}</div>
    <div v-for="option in selectedOptions" :key="option.translateKey" class="entry">
      <check-mark :size="12" />
      {{ $tc(`contentNode.laThematicArea.entity.option.${option.translateKey}.name`) }}
    </div>
  </div>
</template>

<script>
import CheckMark from '../../generic/CheckMark.vue'

export default {
  components: {
    CheckMark,
  },
  props: {
    contentNode: { type: Object, required: true },
  },
  computed: {
    instanceName() {
      return this.contentNode.instanceName || this.$tc(`contentNode.laThematicArea.name`)
    },
    selectedOptions() {
      const options = this.contentNode.data.options

      const optionsArray = Object.keys(options).map((key) => ({
        translateKey: key,
        checked: options[key].checked,
      }))

      return optionsArray.filter((item) => item.checked)
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
