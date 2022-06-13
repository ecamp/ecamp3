<template>
  <v-toolbar-items>
    <v-btn text class="px-2 e-logo"
           min-width="0"
           rounded exact
           :to="{ name: 'home' }">
      <v-toolbar-title>
        <v-icon>$vuetify.icons.ecamp</v-icon><span v-show="text" class="mx-2 e-logo-text">eCamp</span>
      </v-toolbar-title>
    </v-btn>
    <slot />
  </v-toolbar-items>
</template>

<script>
export default {
  name: 'Logo',
  props: {
    text: { type: Boolean, default: false }
  }
}
</script>

<style scoped>
.e-logo:hover .e-logo-text {
  display: inline !important;
}
</style>
