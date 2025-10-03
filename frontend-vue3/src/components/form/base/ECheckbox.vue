<template>
  <!--  <ValidationProvider-->
  <!--    v-slot="{ errors: veeErrors }"-->
  <!--    :name="validationLabel"-->
  <!--    :vid="veeId"-->
  <!--    :rules="veeRules"-->
  <!--    :required="required"-->
  <!--  >-->
  <v-checkbox
    :id="id"
    :class="[inputClass]"
    :error-messages="veeErrors.concat(errorMessages)"
    :hide-details="hideDetails"
    :label="labelOrEntityFieldLabel"
    :model-value="value"
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
  </v-checkbox>
  <!--  </ValidationProvider>-->
</template>

<script>
import { formComponentPropsMixin } from '@/mixins/formComponentPropsMixin.js'
import { formComponentMixin } from '@/mixins/formComponentMixin.js'
// import { ValidationProvider } from 'vee-validate'

export default {
  name: 'ECheckbox',
  components: {
    // ValidationProvider
  },
  mixins: [formComponentPropsMixin, formComponentMixin],
  props: {
    value: { type: Boolean, required: false },
  },
}
</script>

<style scoped>
[required]:deep(label::after) {
  content: '\a0*';
  font-size: 12px;
  color: #d32f2f;
}

[required]:deep(.v-input--is-label-active label::after) {
  color: gray;
}
</style>
