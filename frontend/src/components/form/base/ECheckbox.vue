<template>
  <ValidationProvider v-slot="{ errors }" :name="name"
                      :vid="veeId"
                      :rules="veeRules">
    <v-checkbox v-bind="$attrs"
                :hide-details="hideDetails"
                :error-messages="errors"
                :label="label || name"
                :class="'my-' + my"
                v-on="$listeners" />
  </ValidationProvider>
</template>

<script>
import { ValidationProvider } from 'vee-validate'
import { formComponentPropsMixin } from '@/mixins/formComponentPropsMixin'

export default {
  name: 'ECheckbox',
  components: { ValidationProvider },
  mixins: [formComponentPropsMixin]
}
</script>
