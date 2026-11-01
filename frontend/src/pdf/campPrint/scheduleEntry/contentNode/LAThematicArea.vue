<template>
  <View class="content-node" :class="{ 'content-node-compact': compact }">
    <InstanceName :content-node="contentNode" :icon="mdiShape" />
    <template v-for="(data, key) in contentNode.data.options">
      <View
        v-if="data.checked"
        class="la-thematic-area-entry"
        :class="{ 'la-thematic-area-entry-compact': compact }"
      >
        <Checkmark :size="8" style="margin-top: 2pt" />
        <Text class="la-thematic-area-entry-text">{{ translation(key) }}</Text>
      </View>
    </template>
  </View>
</template>
<script>
import PdfComponent from '@/pdf/PdfComponent.js'
import InstanceName from '../InstanceName.vue'
import Checkmark from '../../Checkmark.vue'
import { mdiShape } from '@mdi/js'

export default {
  name: 'LAThematicArea',
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
      return this.$tc(`contentNode.laThematicArea.entity.option.${key}.name`)
    },
  },
}
</script>
<style lang="react-pdf">
.la-thematic-area-entry {
  display: flex;
  flex-direction: row;
  align-items: flex-start;
  padding: 1pt 0;
  gap: 4pt;
}
.la-thematic-area-entry-compact {
  padding: 0;
}
.la-thematic-area-entry-text {
  flex-basis: 0;
  flex-grow: 1;
}
</style>
