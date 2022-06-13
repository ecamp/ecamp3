<template>
  <v-app-bar
    v-if="$vuetify.breakpoint.smAndUp"
    app
    clipped-left
    color="blue-grey darken-4"
    dark>
    <logo>
      <v-btn
        :to="campRoute(camp(), 'dashboard')"
        class="justify-start px-2 camp--name"
        text
        width="216">
        <v-toolbar-title>
          {{ camp().title | loading($tc('views.camp.navigationCamp.campIsLoading')) }}
        </v-toolbar-title>
      </v-btn>
    </logo>

    <v-toolbar-items>
      <v-btn :to="campRoute(camp(), 'program')" text>
        <v-icon :left="$vuetify.breakpoint.mdAndUp">mdi-view-dashboard</v-icon>
        <span class="sr-only-sm-and-down">{{
          $tc('views.camp.navigationCamp.program')
        }}</span>
      </v-btn>
      <v-btn :to="campRoute(camp(), 'story')" text>
        <v-icon :left="$vuetify.breakpoint.mdAndUp">mdi-book-open-variant</v-icon>
        <span class="sr-only-sm-and-down">{{
          $tc('views.camp.navigationCamp.story')
        }}</span>
      </v-btn>
      <v-btn :to="campRoute(camp(), 'collaborators')" text>
        <v-icon :left="$vuetify.breakpoint.mdAndUp">mdi-account-group</v-icon>
        <span class="sr-only-sm-and-down">{{
          $tc('views.camp.navigationCamp.team')
        }}</span>
      </v-btn>
      <v-btn :to="campRoute(camp(), 'material')" text>
        <v-icon :left="$vuetify.breakpoint.mdAndUp">mdi-package-variant</v-icon>
        <span class="sr-only-sm-and-down">{{
          $tc('views.camp.navigationCamp.material')
        }}</span>
      </v-btn>
      <v-btn :to="campRoute(camp(), 'print')" text>
        <v-icon :left="$vuetify.breakpoint.mdAndUp">mdi-printer</v-icon>
        <span class="sr-only-sm-and-down">{{
          $tc('views.camp.navigationCamp.print')
        }}</span>
      </v-btn>
      <v-btn :to="campRoute(camp(), 'admin')" text>
        <v-icon :left="$vuetify.breakpoint.mdAndUp">mdi-cogs</v-icon>
        <span class="sr-only-sm-and-down">{{
          $tc('views.camp.navigationCamp.admin')
        }}</span>
      </v-btn>
    </v-toolbar-items>
    <v-spacer />
    <user-meta />
  </v-app-bar>
  <v-bottom-navigation v-else grow
                       app
                       background-color="blue-grey darken-4" dark>
    <v-btn :to="campRoute(camp(), 'program')">
      <span>{{ $tc('views.camp.navigationCamp.program') }}</span>
      <v-icon>mdi-view-dashboard</v-icon>
    </v-btn>
    <v-btn :to="campRoute(camp(), 'story')">
      <span>{{ $tc('views.camp.navigationCamp.story') }}</span>
      <v-icon>mdi-book-open-variant</v-icon>
    </v-btn>
    <v-btn :to="campRoute(camp(), 'dashboard')">
      <span>{{ camp().name }}</span>
      <v-icon large>mdi-tent</v-icon>
    </v-btn>
    <v-btn :to="campRoute(camp(), 'material')">
      <span>{{ $tc('views.camp.navigationCamp.material') }}</span>
      <v-icon>mdi-package-variant</v-icon>
    </v-btn>
    <v-btn :to="campRoute(camp(), 'admin')">
      <span>{{ $tc('views.camp.navigationCamp.admin') }}</span>
      <v-icon>mdi-account-cog</v-icon>
    </v-btn>
  </v-bottom-navigation>
</template>

<script>
import { campRoute } from '@/router.js'
import UserMeta from '@/components/navigation/UserMeta.vue'
import Logo from '@/components/navigation/Logo.vue'

export default {
  name: 'NavigationCamp',
  components: {
    UserMeta,
    Logo
  },
  props: {
    camp: { type: Function, required: true }
  },
  methods: {
    campRoute
  }
}
</script>

<style lang="scss" scoped>
.camp--name::v-deep .v-btn__content {
  width: 100%;
}

.v-bottom-navigation--fixed {
  height: auto !important;
  min-height: 56px;
  padding-bottom: env(safe-area-inset-bottom);
}
</style>
