<template>
  <MultiSelect
    v-bind="$props"
    :items="items"
    :placeholder="$t(`components.activity.content.${contentType}.placeholder`)"
  />
</template>

<script>
import MultiSelect from './MultiSelect.vue'
import { contentNodeMixin } from '@/mixins/contentNodeMixin.js'
import camelCase from 'lodash-es/camelCase.js'

export default {
  name: 'LocalizedMultiSelect',
  components: { MultiSelect },
  mixins: [contentNodeMixin],
  computed: {
    contentType() {
      return camelCase(this.contentNode.contentTypeName)
    },
    items() {
      const optionPath = `contentNode.${this.contentType}.entity.option`
      return Object.keys(this.contentNode.data.options).map((value) => ({
        text: this.$t(`${optionPath}.${value}.name`),
        description: this.$t(`${optionPath}.${value}.description`),
        value,
      }))
    },
  },
}
</script>
