<template>
  <ValidationField
    v-slot="{ errors: veeErrors }"
    :label="validationLabel"
    :name="veeId ?? path ?? validationLabel"
    :vee-rules="veeRules"
  >
    <v-autocomplete
      v-model:search="search"
      :class="[inputClass]"
      :custom-filter="tokensFilter"
      :error-messages="(veeErrors ?? []).concat(errorMessages)"
      :hide-details="hideDetails"
      :label="labelOrEntityFieldLabel"
      :menu-icon="readonly ? null : '$dropdown'"
      item-title="text"
      item-value="value"
      :readonly="readonly"
      v-bind="$attrs"
      @update:model-value="$emit('update:modelValue', $event)"
    >
      <template #item="{ item, props }">
        <v-list-item v-bind="{ ...props, title: undefined }">
          <v-list-item-title>
            <span v-for="(part, idx) in renderHighlighted(item)" :key="idx">
              <mark v-if="part.h">{{ part.text }}</mark>
              <span v-else>{{ part.text }}</span>
            </span>
          </v-list-item-title>
          <template v-if="$slots['item-append']" #append>
            <slot name="item-append" :item="item.raw" />
          </template>
        </v-list-item>
      </template>

      <!-- passing through all slots this component does not render itself -->
      <template v-for="slot of passthroughSlots" :key="slot" #[slot]="slotData">
        <slot :name="slot" v-bind="slotData || {}"></slot>
      </template>
    </v-autocomplete>
  </ValidationField>
</template>

<script>
import { formComponentPropsMixin } from '@/mixins/formComponentPropsMixin.js'
import { formComponentValidation } from '@/mixins/formComponentValidation.js'
import uFuzzy from '@leeoniya/ufuzzy'
import ValidationField from './ValidationField.vue'

export default {
  name: 'EAutocomplete',
  components: { ValidationField },
  mixins: [formComponentPropsMixin, formComponentValidation],
  props: {
    immediateValidation: { type: Boolean, default: false },
    skipIfEmpty: { type: Boolean, default: true },
    readonly: { type: Boolean, default: false },
  },
  emits: ['update:modelValue'],
  data() {
    return {
      fuzzy: new uFuzzy({ intraMode: 1 }),
      search: null,
      searchInfos: new Map(),
    }
  },
  computed: {
    passthroughSlots() {
      return Object.keys(this.$slots).filter((slot) => slot !== 'item-append')
    },
  },
  methods: {
    tokensFilter(value, queryText) {
      const [idxs, info] = this.fuzzy.search([value], queryText, true, 1e3)
      this.searchInfos.set(value, info)
      return idxs && idxs.length > 0
    },

    renderHighlighted(item) {
      if (this.search && this.searchInfos.size > 0) {
        if (this.searchInfos.has(item.title)) {
          const info = this.searchInfos.get(item.title)
          if (info && info.ranges) {
            return uFuzzy.highlight(
              item.title,
              info.ranges[0],
              (p, m) => ({ h: m, text: p }),
              [],
              (a, p) => {
                a.push(p)
              }
            )
          }
        }
      }
      return [{ h: false, text: item.title }]
    },
  },
}
</script>
