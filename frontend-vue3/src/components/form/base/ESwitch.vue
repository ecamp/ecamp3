<template>
  <!--  <ValidationProvider-->
  <!--    v-slot="{ errors: veeErrors }"-->
  <!--    :name="validationLabel"-->
  <!--    :vid="veeId"-->
  <!--    :rules="veeRules"-->
  <!--    :required="required"-->
  <!--  >-->
  <v-switch
    :class="[inputClass]"
    :error-messages="veeErrors.concat(errorMessages)"
    :hide-details="hideDetails"
    :label="labelOrEntityFieldLabel"
    :model-value="value"
    inset
    v-bind="$attrs"
    v-on="$listeners"
    @update:model-value="$emit('input', $event)"
  >
    <!-- passing through all slots -->
    <template v-for="(_, name) in $slots" #[name]>
      <slot :name="name" />
    </template>
    <template v-for="(_, name) in $slots" #[name]="slotData">
      <slot :name="name" v-bind="slotData" />
    </template>
  </v-switch>
  <!--  </ValidationProvider>-->
</template>

<script>
// import { ValidationProvider } from 'vee-validate'
import { formComponentPropsMixin } from '@/mixins/formComponentPropsMixin.js'
import { formComponentMixin } from '@/mixins/formComponentMixin.js'

export default {
  name: 'ESwitch',
  components: {
    // ValidationProvider
  },
  mixins: [formComponentPropsMixin, formComponentMixin],
  props: {
    value: { type: Boolean, required: false },
  },
}
</script>
