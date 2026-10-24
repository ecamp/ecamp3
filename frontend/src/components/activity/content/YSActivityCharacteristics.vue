<template>
  <MultiSelect
    class="ec-ys-activity-characteristics"
    v-bind="$props"
    :items="items"
    :placeholder="$t('components.activity.content.ysActivityCharacteristics.placeholder')"
  />
</template>

<script>
import MultiSelect from './MultiSelect.vue'
import { contentNodeMixin } from '@/mixins/contentNodeMixin.js'

export default {
  name: 'YSActivityCharacteristics',
  components: { MultiSelect },
  mixins: [contentNodeMixin],
  computed: {
    items() {
      return Object.keys(this.contentNode.data.options).map((key) => ({
        text: this.$t(`contentNode.ysActivityCharacteristics.entity.option.${key}.name`),
        value: key,
      }))
    },
  },
}
</script>
