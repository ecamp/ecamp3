<template>
  <ValidationField
    v-slot="{ handleChange, errors: veeErrors }"
    :label="validationLabel"
    :name="veeId ?? path ?? validationLabel"
    :vee-rules="veeRules"
  >
    <v-tiptap-editor
      :class="[inputClass]"
      :error-messages="(veeErrors ?? []).concat(errorMessages)"
      :hide-details="hideDetails"
      :label="labelOrEntityFieldLabel"
      :with-extensions="false"
      v-bind="$attrs"
      :center-affix="false"
      @update:model-value="$emit('update:modelValue', $event) && handleChange($event)"
    >
      <!-- passing through all slots -->
      <template v-for="(_, slot) of $slots" #[slot]="slotData">
        <slot :name="slot" v-bind="slotData || {}"></slot>
      </template>
    </v-tiptap-editor>
  </ValidationField>
</template>

<script>
import { formComponentPropsMixin } from '@/mixins/formComponentPropsMixin.js'
import VTiptapEditor from '@/components/form/tiptap/VTiptapEditor.vue'
import { formComponentValidation } from '@/mixins/formComponentValidation.js'
import ValidationField from './ValidationField.vue'

export default {
  name: 'ETextarea',
  components: {
    ValidationField,
    VTiptapEditor,
  },
  mixins: [formComponentPropsMixin, formComponentValidation],
  emits: ['update:modelValue'],
}
</script>
