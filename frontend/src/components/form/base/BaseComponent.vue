<template>
  <ValidationProvider
    v-slot="{ errors: veeErrors }"
    :name="name"
    :vid="veeId"
    :rules="veeRules"
  >
    <component
      :is="inputComponent"
      v-bind="$attrs"
      :filled="filled"
      :hide-details="hideDetails"
      :error-messages="veeErrors.concat(errorMessages)"
      :label.prop="label || name"
      :class="[inputClass]"
      v-on="$listeners"
    >
      <!-- passing through all slots -->
      <slot v-for="(_, name) in $slots" :slot="name" :name="name" />
      <template v-for="(_, name) in $scopedSlots" :slot="name" slot-scope="slotData">
        <slot :name="name" v-bind="slotData" />
      </template>
    </component>
  </ValidationProvider>
</template>

<script>
import { ValidationProvider } from 'vee-validate'
import { formComponentPropsMixin } from '@/mixins/formComponentPropsMixin.js'
import { VTextField } from 'vuetify/lib'

export default {
  name: 'BaseComponent',
  components: { ValidationProvider, VTextField },
  mixins: [formComponentPropsMixin],
  props: {
    inputComponent: {
      type: String,
      required: true,
    },
  },
}
</script>
