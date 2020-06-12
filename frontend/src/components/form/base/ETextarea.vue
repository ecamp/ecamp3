<template>
  <ValidationProvider
    v-slot="{ errors: veeErrors }"
    :name="name"
    :vid="veeId"
    :rules="veeRules">
    <v-textarea
      v-bind="$attrs"
      :filled="filled"
      :hide-details="hideDetails"
      :error-messages="veeErrors.concat(errorMessages)"
      :label="label || name"
      :class="'my-' + my"
      v-on="$listeners">
      <!-- passing through all slots -->
      <slot v-for="(_, name) in $slots" :slot="name" :name="name" />
      <template v-for="(_, name) in $scopedSlots" :slot="name" slot-scope="slotData">
        <slot :name="name" v-bind="slotData" />
      </template>
    </v-textarea>
  </ValidationProvider>
</template>

<script>
import { ValidationProvider } from 'vee-validate'
import { formComponentPropsMixin } from '@/mixins/formComponentPropsMixin'

export default {
  name: 'ETextarea',
  components: { ValidationProvider },
  mixins: [formComponentPropsMixin]
}
</script>
