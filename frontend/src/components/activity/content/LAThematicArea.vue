<template>
  <MultiSelect
    class="ec-la-thematic-area"
    v-bind="$props"
    :items="items"
    :placeholder="$t('components.activity.content.lAThematicArea.placeholder')"
  />
</template>

<script>
import MultiSelect from './MultiSelect.vue'
import { contentNodeMixin } from '@/mixins/contentNodeMixin.js'

export default {
  name: 'LAThematicArea',
  components: { MultiSelect },
  mixins: [contentNodeMixin],
  computed: {
    items() {
      return Object.keys(this.contentNode.data.options).map((key) => ({
        text: this.$t(`contentNode.laThematicArea.entity.option.${key}.name`),
        description: this.$t(
          `contentNode.laThematicArea.entity.option.${key}.description`
        ),
        value: key,
      }))
    },
  },
}
</script>
