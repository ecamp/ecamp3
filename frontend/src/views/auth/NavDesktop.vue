<template>
  <v-app-bar v-if="$vuetify.breakpoint.smAndUp" app
             clipped-left
             color="blue-grey darken-4" dark>
    <logo text />
    <v-spacer />
  </v-app-bar>
</template>

<script>

export default {
  name: 'NavDesktop',
  components: {
    Logo: () => import('@/components/base/Logo')
  }
}
</script>

<style lang="scss">

</style>
