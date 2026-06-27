<template>
  <e-text-field
    :model-value="modelValue"
    :type="showPassword ? 'text' : 'password'"
    v-bind="$attrs"
    @update:model-value="$emit('update:modelValue', $event)"
    @blur="$emit('blur', $event)"
  >
    <!-- forward every slot the consumer provides, except append-inner -->
    <template
      v-for="slotName in forwardedSlotNames"
      #[slotName]="slotData"
      :key="slotName"
    >
      <slot :name="slotName" v-bind="slotData || {}"></slot>
    </template>

    <template #append-inner>
      <v-btn
        :aria-label="
          showPassword
            ? $t('global.button.hidePassword')
            : $t('global.button.showPassword')
        "
        :icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
        density="compact"
        variant="text"
        @click="showPassword = !showPassword"
      />
    </template>
  </e-text-field>
</template>

<script>
export default {
  name: 'EPasswordField',
  inheritAttrs: false,
  props: {
    modelValue: { type: String, required: false, default: null },
  },
  emits: ['update:modelValue', 'blur'],
  data() {
    return {
      showPassword: false,
    }
  },
  computed: {
    forwardedSlotNames() {
      return Object.keys(this.$slots).filter((name) => name !== 'append-inner')
    },
  },
}
</script>
