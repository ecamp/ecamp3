<template>
  <ValidationField
    v-slot="{ handleChange, errors: veeErrors }"
    :name="veeId ?? path ?? validationLabel"
    :label="validationLabel"
    :vee-rules="veeRules"
  >
    <v-select
      :class="[inputClass]"
      :error-messages="(veeErrors ?? []).concat(errorMessages)"
      :hide-details="hideDetails"
      :label="labelOrEntityFieldLabel"
      v-bind="$attrs"
      :readonly="readonly"
      :menu-icon="readonly ? null : '$dropdown'"
      item-title="text"
      item-value="value"
      @update:model-value="
        ($event) => {
          handleChange($event)
          $emit('update:modelValue', $event)
          $emit('input', $event)
        }
      "
    >
      <!-- passing through all slots -->
      <template v-for="(_, slot) of $slots" #[slot]="slotData">
        <slot :name="slot" v-bind="slotData || {}"></slot>
      </template>
    </v-select>
  </ValidationField>
</template>

<script>
import { formComponentPropsMixin } from '@/mixins/formComponentPropsMixin.js'
import { formComponentValidation } from '@/mixins/formComponentValidation.js'
import ValidationField from '@/components/form/base/ValidationField.vue'

export default {
  name: 'ESelect',
  components: {
    ValidationField,
  },
  mixins: [formComponentPropsMixin, formComponentValidation],
  props: {
    // TODO: implement immediateValidation
    immediateValidation: { type: Boolean, default: false },
    // TODO: implement skipIfEmpty
    skipIfEmpty: { type: Boolean, default: true },
    readonly: { type: Boolean, default: false },
  },
  emits: ['input', 'update:modelValue'],
}
</script>
