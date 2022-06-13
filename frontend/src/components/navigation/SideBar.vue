<template>
  <v-navigation-drawer
    v-if="$vuetify.breakpoint.smAndUp"
    v-model="drawer"
    app
    clipped
    permanent
    :temporary="!mini && !$vuetify.breakpoint.mdAndUp"
    :mini-variant.sync="mini"
    mini-variant-width="40"
    color="blue-grey lighten-4">
    <v-btn
      v-if="mini"
      icon
      class="ec-drawer-open mr-1"
      @click.stop="overrideExpanded = true">
      <v-icon>mdi-format-list-bulleted-type</v-icon>
    </v-btn>
    <v-spacer />
    <v-btn
      v-if="!mini"
      icon
      fixed
      class="ec-drawer-collapse mr-1"
      style="z-index: 10"
      right
      @click.stop="overrideExpanded = false">
      <v-icon>mdi-chevron-left</v-icon>
    </v-btn>

    <v-divider />

    <slot v-if="!mini" />
  </v-navigation-drawer>
</template>

<script>
export default {
  name: 'SideBar',
  data () {
    return {
      drawer: false,
      overrideExpanded: null
    }
  },
  computed: {
    mini () {
      return this.overrideExpanded !== null
        ? !this.overrideExpanded
        : !this.$vuetify.breakpoint.mdAndUp
    }
  }
}
</script>

<style scoped></style>
