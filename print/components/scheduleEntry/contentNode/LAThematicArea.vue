<template>
  <content-node-content :content-node="contentNode">
    <div v-for="option in selectedOptions" :key="option.translateKey" class="entry">
      <check-mark :size="12" />
      {{ $tc(`contentNode.laThematicArea.entity.option.${option.translateKey}.name`) }}
    </div>
  </content-node-content>
</template>

<script>
import CheckMark from '../../generic/CheckMark.vue'
import ContentNodeContent from './ContentNodeContent.vue'

export default {
  components: {
    CheckMark,
    ContentNodeContent,
  },
  props: {
    contentNode: { type: Object, required: true },
  },
  computed: {
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
