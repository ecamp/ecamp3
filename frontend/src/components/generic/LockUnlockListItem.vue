<template>
  <v-list-item :disabled="disabled" v-on="$listeners">
    <v-list-item-icon>
      <v-icon v-if="value" :disabled="disabled">mdi-lock</v-icon>
      <v-icon v-else :disabled="disabled">mdi-lock-open-variant</v-icon>
    </v-list-item-icon>
    <v-list-item-title>
      {{ value ? $tc('global.button.lock') : $tc('global.button.unlock') }}
    </v-list-item-title>
  </v-list-item>
</template>

<script>
export default {
  props: {
    value: {
      type: Boolean,
      required: true,
    },
    disabled: {
      type: Boolean,
      required: false,
      default: false,
    },
  },
}
</script>
