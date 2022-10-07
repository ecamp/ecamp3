<template>
  <ValidationProvider
    v-slot="{ errors: veeErrors }"
    :name="name"
    :vid="veeId"
    :rules="veeRules"
    :required="required"
  >
    <v-switch
      inset
      v-bind="$attrs"
      :hide-details="hideDetails"
      :error-messages="veeErrors.concat(errorMessages)"
      :label="label || name"
      :class="[inputClass]"
      :input-value="value"
      @change="$emit('input', $event)"
      v-on="$listeners"
    >
      <!-- passing through all slots -->
      <slot v-for="(_, name) in $slots" :slot="name" :name="name" />
      <template v-for="(_, name) in $scopedSlots" :slot="name" slot-scope="slotData">
        <slot :name="name" v-bind="slotData" />
      </template>
    </v-switch>
  </ValidationProvider>
</template>

<script>
import { ValidationProvider } from 'vee-validate'
import { formComponentPropsMixin } from '@/mixins/formComponentPropsMixin.js'
import { formComponentMixin } from '@/mixins/formComponentMixin.js'

export default {
  name: 'ESwitch',
  components: { ValidationProvider },
  mixins: [formComponentPropsMixin, formComponentMixin],
  props: {
    value: { type: Boolean, required: false },
  },
}
</script>
