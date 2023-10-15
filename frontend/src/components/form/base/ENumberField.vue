<template>
  <ValidationProvider
    v-slot="{ errors: veeErrors }"
    ref="validationProvider"
    tag="div"
    :name="name"
    :vid="veeId"
    :rules="veeRules"
    :required="required"
    :mode="eagerIfChanged"
    class="e-form-container"
  >
    <v-text-field
      ref="textField"
      v-model="formattedValue"
      v-bind="$attrs"
      :filled="filled"
      :required="required"
      :hide-details="hideDetails"
      :error-messages="veeErrors.concat(errorMessages)"
      :label="label || name"
      type="text"
      :class="[inputClass]"
      :hide-spin-buttons="true"
      v-on="{ ...$listeners, input: undefined }"
    >
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
import { formComponentPropsMixin } from '@/mixins/formComponentPropsMixin.js'
import { formComponentMixin } from '@/mixins/formComponentMixin.js'
import { eagerIfChanged } from '@/helpers/veeValidateCustomInteractionMode'
import { mapGetters } from 'vuex'

export default {
  name: 'ENumberField',
  components: { ValidationProvider },
  mixins: [formComponentPropsMixin, formComponentMixin],
  props: {
    value: {
      type: Number,
      required: true,
    },
  },
  emits: ['input'],
  computed: {
    formattedValue: {
      get: function () {
        return this.$n(this.value, 'decimal')
      },
      set: function (newNumber) {
        let newValueAsNumber = newNumber
        if (typeof newNumber !== 'number') {
          newValueAsNumber = parseFloat(
            newNumber
              .replace(' ', '')
              .replace(',', '.')
              .replace(/[^\d,.-]/g, '')
          )
        }

        this.$emit('input', newValueAsNumber)
      },
    },
  },
  mounted() {},
  methods: {
    eagerIfChanged,
    focus() {
      this.$refs.textField.focus()
    },
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
