<template>
  <v-app-bar v-if="$vuetify.breakpoint.smAndUp" app
             clipped-left
             color="blue-grey darken-4" dark>
    <logo text/>
    <v-spacer />
  </v-app-bar>
</template>

<script>

export default {
  name: 'TopBar',
  components: {
    Logo: () => import('@/components/global/Logo')
  }
}
</script>

<style lang="scss">

</style>
