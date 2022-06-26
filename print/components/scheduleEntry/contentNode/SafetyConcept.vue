<template>
  <div class="wrapper">
    <div v-if="instanceName" class="instance-name">{{ instanceName }}</div>
    <rich-text :rich-text="contentNode.text" />
  </div>
</template>

<script>
import RichText from '../../generic/RichText.vue'

export default {
  components: {
    RichText,
  },
  props: {
    contentNode: { type: Object, required: true },
  },
  computed: {
    instanceName() {
      return this.contentNode.instanceName
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
