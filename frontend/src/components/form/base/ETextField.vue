<template>
  <ValidationProvider
    v-slot="{ errors: veeErrors }"
    tag="div"
    :name="name"
    :vid="veeId"
    :rules="veeRules"
    class="e-form-container">
    <v-text-field
      ref="textField"
      v-bind="$attrs"
      :filled="filled"
      :hide-details="hideDetails"
      :error-messages="veeErrors.concat(errorMessages)"
      :label="label || name"
      :class="[inputClass]"
      v-on="$listeners">
      <!-- passing through all slots -->
      <slot v-for="(_, name) in $slots" :slot="name" :name="name" />
      <template v-for="(_, name) in $scopedSlots" :slot="name" slot-scope="slotData">
        <slot :name="name" v-bind="slotData" />
      </template>
    </v-text-field>
  </ValidationProvider>
</template>

<script>
import { ValidationProvider } from 'vee-validate'
import { formComponentPropsMixin } from '@/mixins/formComponentPropsMixin'

export default {
  name: 'ETextField',
  components: { ValidationProvider },
  mixins: [formComponentPropsMixin],
  methods: {
    focus () {
      this.$refs.textField.focus()
    }
  }
}
</script>
