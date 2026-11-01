<template>
  <View class="content-node" :class="{ 'content-node-compact': compact }">
    <InstanceName :content-node="contentNode" :icon="mdiShape" />
    <View
      class="ys-activity-characteristics-list"
      :class="{ 'ys-activity-characteristics-list-compact': compact }"
    >
      <template v-for="(data, key) in contentNode.data.options">
        <View v-if="data.checked" class="ys-activity-characteristics-entry">
          <Checkmark :size="8" style="margin-top: 2pt" />
          <Text class="ys-activity-characteristics-entry-text">{{
            translation(key)
          }}</Text>
        </View>
      </template>
    </View>
  </View>
</template>
<script>
import PdfComponent from '@/pdf/PdfComponent.js'
import InstanceName from '../InstanceName.vue'
import Checkmark from '../../Checkmark.vue'
import { mdiShape } from '@mdi/js'

export default {
  name: 'YSActivityCharacteristics',
  components: { InstanceName, Checkmark },
  extends: PdfComponent,
  isEmpty(contentNode) {
    return !Object.values(contentNode.data.options).some((option) => option.checked)
  },
  props: {
    contentNode: { type: Object, required: true },
  },
  data() {
    return { mdiShape }
  },
  methods: {
    translation(key) {
      return this.$tc(`contentNode.ysActivityCharacteristics.entity.option.${key}.name`)
    },
  },
}
</script>
<style lang="react-pdf">
.ys-activity-characteristics-list {
  gap: 4pt;
}
.ys-activity-characteristics-list-compact {
  gap: 1pt;
}
.ys-activity-characteristics-entry {
  display: flex;
  flex-direction: row;
  align-items: top;
  gap: 4pt;
}
.ys-activity-characteristics-entry-text {
  flex-basis: 0;
  flex-grow: 1;
}
</style>
