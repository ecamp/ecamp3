<template>
  <v-navigation-drawer v-if="$vuetify.breakpoint.smAndUp"
                       v-model="drawer" app
                       clipped permanent
                       :temporary="!mini && !this.$vuetify.breakpoint.mdAndUp"
                       :mini-variant.sync="mini"
                       mini-variant-width="40"
                       color="blue-grey lighten-4">
    <v-btn v-if="mini" icon @click.stop="mini = !mini">
      <v-icon>mdi-format-list-bulleted-triangle</v-icon>
    </v-btn>
    <v-spacer />
    <v-btn v-if="!mini" icon
           fixed
           class="ec-drawer-collapse ma-2"
           style="z-index: 10;"
           right @click.stop="mini = !mini">
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
      dirty: false,
      minimized: false
    }
  },
  computed: {
    mini: {
      get: function () {
        if (this.dirty) {
          return this.minimized
        } else {
          return !this.$vuetify.breakpoint.mdAndUp
        }
      },
      set: function (value) {
        this.dirty = true
        this.minimized = value
      }
    }
  }
}
</script>

<style scoped>

</style>
