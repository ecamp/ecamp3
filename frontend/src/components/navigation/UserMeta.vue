<template>
  <v-menu v-model="open"
          offset-y dark
          right content-class="ec-usermenu"
          transition="slide-y-transition"
          :close-on-content-click="false"
          z-index="5">
    <template v-slot:activator="{ on,value,attrs }">
      <v-toolbar-items>
        <v-btn right text
               v-bind="attrs"
               :class="{ 'v-btn--open': value }" v-on="on">
          <span class="sr-only-sm-and-down">
            {{ username }}
          </span>
          <v-icon class="ma-2">mdi-account</v-icon>
        </v-btn>
      </v-toolbar-items>
    </template>
    <v-list dense class="user-nav"
            tag="ul"
            light color="blue-grey lighten-5">
      <v-list-item tag="li" block
                   :to="{ name: 'profile' }"
                   @click="open = false">
        <v-icon left>mdi-account</v-icon>
        <span>{{ $tc('components.navigationCamp.userMeta.profile') }}</span>
      </v-list-item>
      <v-list-item block tag="li"
                   exact :to="{ name: 'camps' }"
                   @click="open = false">
        <v-icon left>mdi-format-list-bulleted-triangle</v-icon>
        <span>{{ $tc('components.navigationCamp.userMeta.myCamps', api.get().camps().items.length) }}</span>
      </v-list-item>
      <v-list-item block tag="li"
                   @click="logout">
        <v-icon v-if="logoutIcon" left>{{ logoutIcon }}</v-icon>
        <v-progress-circular v-else indeterminate
                             size="18" class="mr-2" />
        <span>{{ $tc('components.navigationCamp.userMeta.logOut') }}</span>
      </v-list-item>
    </v-list>
  </v-menu>
</template>

<script>

export default {
  name: 'UserMeta',
  data () {
    return {
      logoutIcon: 'mdi-logout',
      open: false
    }
  },
  computed: {
    username () {
      return this.api.get().profile().username
    }
  },
  methods: {
    logout () {
      this.logoutIcon = ''
      this.$auth.logout()
    },
    prevent (event) {
      event.stopImmediatePropagation()
      event.preventDefault()
      event.cancelBubble = true
      return null
    }
  }
}
</script>

<style scoped>

</style>
