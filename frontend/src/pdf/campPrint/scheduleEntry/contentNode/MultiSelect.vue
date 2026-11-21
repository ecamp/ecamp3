<template>
  <View class="content-node" :class="{ 'content-node-compact': compact }">
    <InstanceName :content-node="contentNode" :icon="icon" />
    <template v-for="(data, key) in contentNode.data.options">
      <View
        v-if="data.checked"
        class="multi-select-entry"
        :class="{ 'multi-select-entry-compact': compact }"
      >
        <Checkmark :size="8" style="margin-top: 2pt" />
        <Text class="multi-select-entry-text">{{ translation(key) }}</Text>
      </View>
    </template>
  </View>
</template>
<script>
import PdfComponent from '@/pdf/PdfComponent.js'
import InstanceName from '../InstanceName.vue'
import Checkmark from '../../Checkmark.vue'
import { mdiAccountMultiple, mdiPuzzleOutline, mdiShape } from '@mdi/js'
import camelCase from 'lodash-es/camelCase.js'

const contentTypeIcons = {
  laThematicArea: mdiShape,
  pbsMethods: mdiPuzzleOutline,
  pbsRelations: mdiAccountMultiple,
}

export default {
  name: 'MultiSelect',
  components: { InstanceName, Checkmark },
  extends: PdfComponent,
  isEmpty(contentNode) {
    return !Object.values(contentNode.data.options).some((option) => option.checked)
  },
  props: {
    contentNode: { type: Object, required: true },
  },
  computed: {
    contentType() {
      return camelCase(this.contentNode.contentTypeName)
    },
    icon() {
      return contentTypeIcons[this.contentType]
    },
  },
  methods: {
    translation(key) {
      return this.$tc(`contentNode.${this.contentType}.entity.option.${key}.name`)
    },
  },
}
</script>
<style lang="react-pdf">
.multi-select-entry {
  display: flex;
  flex-direction: row;
  align-items: flex-start;
  padding: 1pt 0;
  gap: 4pt;
}
.multi-select-entry-compact {
  padding: 0;
}
.multi-select-entry-text {
  flex-basis: 0;
  flex-grow: 1;
}
</style>
