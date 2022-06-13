<template>
  <v-btn
    :text="text"
    class="px-3 px-sm-4"
    min-width="0"
    :color="color"
    :icon="iconOnly"
    v-bind="$attrs"
    v-on="$listeners">
    <v-icon v-if="iconOnly">{{ icon }}</v-icon>
    <v-icon v-else :left="$vuetify.breakpoint.smAndUp" size="150%">{{ icon }}</v-icon>

    <span v-if="!iconOnly" class="d-none d-sm-block">
      <slot>{{ $tc('global.button.delete') }}</slot>
    </span>
  </v-btn>
</template>

<script>
export default {
  name: 'ButtonDelete',
  props: {
    icon: { type: String, default: 'mdi-delete' },
    text: { type: Boolean, default: true },
    color: { type: String, default: 'blue-grey' },
    iconOnly: { type: Boolean, default: false }
  }
}
</script>

<style scoped></style>
