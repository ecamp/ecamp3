<template>
  <v-app-bar
    v-if="$vuetify.breakpoint.smAndUp"
    app
    clipped-left
    color="blue-grey darken-4"
    dark
  >
    <logo>
      <v-btn
        :to="campRoute(camp(), 'dashboard')"
        class="justify-start px-2 camp--name"
        text
        width="216"
      >
        <v-toolbar-title>
          {{
            camp().title
              | loading($tc("views.camp.navigationCamp.campIsLoading"))
          }}
        </v-toolbar-title>
      </v-btn>
    </logo>

    <v-toolbar-items>
      <v-btn :to="campRoute(camp(), 'program')" text>
        <v-icon :left="$vuetify.breakpoint.mdAndUp">mdi-view-dashboard</v-icon>
        <span class="sr-only-sm-and-down">{{
          $tc("views.camp.navigationCamp.program")
        }}</span>
      </v-btn>
      <v-btn :to="campRoute(camp(), 'story')" text>
        <v-icon :left="$vuetify.breakpoint.mdAndUp">
          mdi-book-open-variant
        </v-icon>
        <span class="sr-only-sm-and-down">{{
          $tc("views.camp.navigationCamp.story")
        }}</span>
      </v-btn>
      <v-btn :to="campRoute(camp(), 'collaborators')" text>
        <v-icon :left="$vuetify.breakpoint.mdAndUp">mdi-account-group</v-icon>
        <span class="sr-only-sm-and-down">{{
          $tc("views.camp.navigationCamp.team")
        }}</span>
      </v-btn>
      <v-btn :to="campRoute(camp(), 'material')" text>
        <v-icon :left="$vuetify.breakpoint.mdAndUp">
          mdi-package-variant
        </v-icon>
        <span class="sr-only-sm-and-down">{{
          $tc("views.camp.navigationCamp.material")
        }}</span>
      </v-btn>
      <v-btn :to="campRoute(camp(), 'print')" text>
        <v-icon :left="$vuetify.breakpoint.mdAndUp">mdi-printer</v-icon>
        <span class="sr-only-sm-and-down">{{
          $tc("views.camp.navigationCamp.print")
        }}</span>
      </v-btn>
      <v-btn :to="campRoute(camp(), 'admin')" text>
        <v-icon :left="$vuetify.breakpoint.mdAndUp">mdi-cogs</v-icon>
        <span class="sr-only-sm-and-down">{{
          $tc("views.camp.navigationCamp.admin")
        }}</span>
      </v-btn>
    </v-toolbar-items>
    <v-spacer />
    <user-meta />
  </v-app-bar>
  <div v-else>
    <v-bottom-navigation grow app
                         background-color="blue-grey darken-4"
                         dark>
      <v-btn :to="campRoute(camp(), 'program')">
        <span>{{ $tc("views.camp.navigationCamp.program") }}</span>
        <v-icon>mdi-view-dashboard</v-icon>
      </v-btn>
      <v-btn :to="campRoute(camp(), 'story')">
        <span>{{ $tc("views.camp.navigationCamp.story") }}</span>
        <v-icon>mdi-book-open-variant</v-icon>
      </v-btn>
      <v-btn :to="campRoute(camp(), 'dashboard')">
        <span>{{ camp().name }}</span>
        <v-icon large>mdi-tent</v-icon>
      </v-btn>
      <v-btn :to="campRoute(camp(), 'material')">
        <span>{{ $tc("views.camp.navigationCamp.material") }}</span>
        <v-icon>mdi-package-variant</v-icon>
      </v-btn>
      <v-btn @click="open = true">
        <span>{{ $tc("views.camp.navigationCamp.more") }}</span>
        <v-icon>mdi-menu</v-icon>
      </v-btn>
    </v-bottom-navigation>
    <v-navigation-drawer v-model="open" app
                         right
                         width="300">
      <div class="d-flex flex-column fill-height">
        <h2
          class="text-h6 text-center d-flex flex-column align-center blue-grey darken-4 white--text py-6">
          <v-icon x-large>$vuetify.icons.ecamp</v-icon>
          <span> eCamp </span>
        </h2>
        <v-divider class="blue-grey darken-2" />
        <v-list>
          <v-list-item :to="{ name: 'profile', query: { isDetail: true } }">
            <v-list-item-avatar>
              <user-avatar :user="$auth.user()" />
            </v-list-item-avatar>
            <v-list-item-content>
              <v-list-item-title> {{ user.displayName }}</v-list-item-title>
              <v-list-item-subtitle>
                {{ user.profile().firstname + " " + user.profile().surname }}
              </v-list-item-subtitle>
            </v-list-item-content>
            <v-list-item-action>
              <v-icon>mdi-chevron-right</v-icon>
            </v-list-item-action>
          </v-list-item>
          <SidebarListItem
            title="Meine Lager"
            icon="mdi-format-list-bulleted-triangle"
            :to="{ name: 'camps', query: { isDetail: true } }" />
        </v-list>

        <v-divider />

        <v-list>
          <v-list-item two-line>
            <v-list-item-content>
              <v-list-item-title>{{ camp().name }}</v-list-item-title>
              <v-list-item-subtitle>{{ camp().motto }}</v-list-item-subtitle>
            </v-list-item-content>
          </v-list-item>
          <v-divider inset i />
          <SidebarListItem
            title="Campeinstellungen"
            icon="mdi-cogs"
            :to="campRoute(camp(), 'admin')" />
          <v-divider inset />
          <SidebarListItem
            title="Team"
            icon="mdi-account-group"
            :to="campRoute(camp(), 'collaborators')" />
          <v-divider inset />
          <SidebarListItem
            title="PDF / Drucken"
            icon="mdi-file-outline"
            :to="campRoute(camp(), 'print')" />
        </v-list>
        <div class="mt-auto">
          <v-btn
            x-large
            height="56"
            text
            tile
            block
            class="ec-close-drawer pb-safe"
            @click="open = false">
            Menu schliessen
            <v-icon right>mdi-close</v-icon>
          </v-btn>
        </div>
      </div>
    </v-navigation-drawer>
  </div>
</template>

<script>
import { campRoute } from '@/router.js'
import UserMeta from '@/components/navigation/UserMeta.vue'
import Logo from '@/components/navigation/Logo.vue'
import UserAvatar from '@/components/user/UserAvatar.vue'
import SidebarListItem from '@/components/layout/SidebarListItem.vue'

export default {
  name: 'NavigationCamp',
  components: {
    SidebarListItem,
    UserAvatar,
    UserMeta,
    Logo,
  },
  props: {
    camp: { type: Function, required: true },
  },
  data () {
    return {
      open: false
    }
  },
  computed: {
    user () {
      return this.$auth.user()
    }
  },
  methods: {
    campRoute,
  },
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

.v-application .ec-close-drawer {
  background-color: #{map-get($blue-grey, "lighten-5")};
  border-top: 1px solid #{map-get($blue-grey, "lighten-4")};
}
</style>
