<template>
  <v-btn
    :text="text"
    class="ec-btn-delete px-3 px-sm-4"
    min-width="0"
    :color="color"
    :icon="btnIcon && iconOnly"
    v-bind="$attrs"
    v-on="$listeners"
  >
    <v-icon v-if="iconOnly">{{ icon }}</v-icon>
    <v-icon v-else-if="!text" :left="$vuetify.breakpoint.smAndUp">{{ icon }}</v-icon>
    <v-icon v-if="!iconOnly && text" :left="$vuetify.breakpoint.smAndUp">{{
      icon
    }}</v-icon>

    <span v-if="!iconOnly" class="d-none d-sm-block">
      <slot>{{ $tc('global.button.delete') }}</slot>
    </span>
    <span class="d-sr-only" :class="{ 'd-sm-none': !iconOnly }">
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
    iconOnly: { type: Boolean, default: false },
    btnIcon: { type: Boolean, default: true },
  },
}
</script>

<style scoped>
.ec-btn-delete:hover {
  color: #f44336 !important;
}
</style>
