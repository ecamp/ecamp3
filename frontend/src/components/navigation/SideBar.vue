<template>
  <v-navigation-drawer
    v-if="$vuetify.breakpoint.smAndUp"
    v-model="drawer"
    :mini-variant.sync="mini"
    app
    clipped
    permanent
    :temporary="!mini && !$vuetify.breakpoint.mdAndUp"
    mini-variant-width="40"
    :width="$vuetify.breakpoint.xl || (!mini && !$vuetify.breakpoint.mdAndUp) ? 350 : 256"
    :color="!title || mini ? 'blue-grey lighten-4' : null"
  >
    <v-list class="py-0">
      <v-list-item v-if="mini" class="py-1" @click.stop="overrideExpanded = true">
        <v-icon>{{ icon }}</v-icon>
      </v-list-item>
      <v-list-item v-else class="py-1 pr-2">
        <v-list-item-title class="subtitle-1 font-weight-bold">
          {{ title }}
        </v-list-item-title>
        <v-btn icon @click.stop="overrideExpanded = false">
          <v-icon>mdi-chevron-left</v-icon>
        </v-btn>
      </v-list-item>
    </v-list>
    <v-divider />
    <slot v-if="!mini" />
  </v-navigation-drawer>
</template>

<script>
export default {
  name: 'SideBar',
  props: {
    title: { type: String, required: true },
    icon: { type: String, default: 'mdi-format-list-bulleted-type' },
  },
  data() {
    return {
      drawer: false,
      overrideExpanded: null,
    }
  },
  computed: {
    mini() {
      return this.overrideExpanded !== null
        ? !this.overrideExpanded
        : !this.$vuetify.breakpoint.mdAndUp
    },
  },
}
</script>

<style scoped></style>
