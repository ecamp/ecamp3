<template>
  <!--  <ValidationProvider-->
  <!--    v-slot="{ errors: veeErrors }"-->
  <!--    tag="div"-->
  <!--    :name="name"-->
  <!--    :vid="veeId"-->
  <!--    :rules="veeRules"-->
  <!--    :skip-if-empty="skipIfEmpty"-->
  <!--    :required="required"-->
  <!--    :immediate="immediateValidation"-->
  <!--    class="e-form-container"-->
  <!--  >-->
  <v-select
    v-bind="$attrs"
    :filled="filled"
    :hide-details="hideDetails"
    :error-messages="veeErrors.concat(errorMessages)"
    :label="label || name"
    :class="[inputClass]"
    v-on="$listeners"
  >
    <!-- passing through all slots -->
    <template v-for="(_, name) in $slots" #[name]>
      <slot :name="name" />
    </template>
    <template v-for="(_, name) in $scopedSlots" #[name]="slotData">
      <slot :name="name" v-bind="slotData" />
    </template>
  </v-select>
  <!--  </ValidationProvider>-->
</template>

<script>
// import { ValidationProvider } from 'vee-validate'
import { formComponentPropsMixin } from '@/mixins/formComponentPropsMixin.js'
import { formComponentMixin } from '@/mixins/formComponentMixin.js'

export default {
  name: 'ESelect',
  components: {
    // ValidationProvider
  },
  mixins: [formComponentPropsMixin, formComponentMixin],
  props: {
    immediateValidation: { type: Boolean, default: false },
    skipIfEmpty: { type: Boolean, default: true },
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
