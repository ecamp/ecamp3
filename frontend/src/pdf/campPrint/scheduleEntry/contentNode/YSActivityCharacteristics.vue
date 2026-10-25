<template>
  <View class="content-node">
    <InstanceName :content-node="contentNode" />
    <template v-for="(data, key) in contentNode.data.options">
      <View v-if="data.checked" class="ys-activity-characteristics-entry">
        <Checkmark :size="8" />
        <Text style="margin-left: 2pt">{{ translation(key) }}</Text>
      </View>
    </template>
  </View>
</template>
<script>
import PdfComponent from '@/pdf/PdfComponent.js'
import InstanceName from '../InstanceName.vue'
import Checkmark from '../../Checkmark.vue'

export default {
  name: 'YSActivityCharacteristics',
  components: { InstanceName, Checkmark },
  extends: PdfComponent,
  props: {
    contentNode: { type: Object, required: true },
  },
  methods: {
    translation(key) {
      return this.$tc(`contentNode.ysActivityCharacteristics.entity.option.${key}.name`)
    },
  },
}
</script>
<style lang="react-pdf">
.ys-activity-characteristics-entry {
  display: flex;
  flex-direction: row;
  align-items: top;
}
</style>
