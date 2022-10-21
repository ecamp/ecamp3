<template>
  <v-app-bar app clipped-left color="blue-grey darken-4" dark>
    <logo text />

    <v-toolbar-items>
      <v-btn :to="campRoute(camp())" text>
        <v-icon :left="$vuetify.breakpoint.mdAndUp">mdi-tent</v-icon>
        <span class="sr-only-sm-and-down">{{
          camp().title
            | loading($tc('views.camp.navigation.desktop.navTopbar.campIsLoading'))
        }}</span>
      </v-btn>
      <v-btn :to="campRoute(camp(), 'program')" text>
        <v-icon :left="$vuetify.breakpoint.mdAndUp">mdi-view-dashboard</v-icon>
        <span class="sr-only-sm-and-down">{{
          $tc('views.camp.navigation.desktop.navTopbar.program')
        }}</span>
      </v-btn>
      <v-btn :to="campRoute(camp(), 'story')" text>
        <v-icon :left="$vuetify.breakpoint.mdAndUp"> mdi-book-open-variant </v-icon>
        <span class="sr-only-sm-and-down">{{
          $tc('views.camp.navigation.desktop.navTopbar.story')
        }}</span>
      </v-btn>
      <v-btn :to="campRoute(camp(), 'collaborators')" text>
        <v-icon :left="$vuetify.breakpoint.mdAndUp">mdi-account-group</v-icon>
        <span class="sr-only-sm-and-down">{{
          $tc('views.camp.navigation.desktop.navTopbar.team')
        }}</span>
      </v-btn>
      <v-btn :to="campRoute(camp(), 'material')" text>
        <v-icon :left="$vuetify.breakpoint.mdAndUp"> mdi-package-variant </v-icon>
        <span class="sr-only-sm-and-down">{{
          $tc('views.camp.navigation.desktop.navTopbar.material')
        }}</span>
      </v-btn>
      <v-btn :to="campRoute(camp(), 'print')" text>
        <v-icon :left="$vuetify.breakpoint.mdAndUp">mdi-printer</v-icon>
        <span class="sr-only-sm-and-down">{{
          $tc('views.camp.navigation.desktop.navTopbar.print')
        }}</span>
      </v-btn>
      <v-btn :to="campRoute(camp(), 'admin')" text>
        <v-icon :left="$vuetify.breakpoint.mdAndUp">mdi-cogs</v-icon>
        <span class="sr-only-sm-and-down">{{
          $tc('views.camp.navigation.desktop.navTopbar.admin')
        }}</span>
      </v-btn>
    </v-toolbar-items>
    <v-spacer />
    <user-meta />
  </v-app-bar>
</template>

<script>
import UserMeta from '@/components/navigation/UserMeta.vue'
import Logo from '@/components/navigation/Logo.vue'
import { campRoute } from '@/router.js'

export default {
  name: 'NavTopbar',
  components: {
    UserMeta,
    Logo,
  },
  props: {
    camp: { type: Function, required: true },
  },
  data() {
    return {
      open: false,
    }
  },
  computed: {
    user() {
      return this.$store.state.auth.user
    },
  },
  methods: {
    campRoute,
  },
}
</script>

<style lang="scss" scoped>
.camp--name:deep(.v-btn__content) {
  width: 100%;
}
</style>
