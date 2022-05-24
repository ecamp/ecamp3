<template>
  <v-app-bar
    v-if="$vuetify.breakpoint.smAndUp"
    app
    clipped-left
    color="blue-grey darken-4"
    dark>
    <logo text />
    <v-spacer />
  </v-app-bar>
</template>

<script>
import Logo from '@/components/navigation/Logo.vue'

export default {
  name: 'NavigationAuth',
  components: {
    Logo
  }
}
</script>

<style lang="scss"></style>
