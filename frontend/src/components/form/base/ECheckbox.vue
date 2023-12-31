<template>
  <!--  <ValidationProvider-->
  <!--    v-slot="{ errors: veeErrors }"-->
  <!--    :name="name"-->
  <!--    :vid="veeId"-->
  <!--    :rules="veeRules"-->
  <!--    :required="required"-->
  <!--  >-->
  <v-checkbox
    v-bind="$attrs"
    :id="id"
    :hide-details="hideDetails"
    :error-messages="veeErrors.concat(errorMessages)"
    :label="label || name"
    :class="[inputClass]"
    :input-value="value"
    @change="$emit('input', $event)"
    v-on="$listeners"
  >
    <!-- passing through all slots -->
    <template v-for="(_, name) in $slots" #[name]>
      <slot :name="name" />
    </template>
    <template v-for="(_, name) in $scopedSlots" #[name]="slotData">
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
