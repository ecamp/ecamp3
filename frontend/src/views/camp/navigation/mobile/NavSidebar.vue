<template>
  <v-navigation-drawer
    :value="value"
    app
    right
    width="300"
    @input="$emit('input', $event)"
  >
    <div class="d-flex flex-column fill-height">
      <div class="d-flex flex-column align-center blue-grey darken-4 white--text py-6">
        <v-icon x-large>$vuetify.icons.ecamp</v-icon>
        <h2 class="text-h6 text-center">eCamp</h2>
      </div>
      <v-divider class="blue-grey darken-2" />
      <v-list>
        <SidebarListItem
          :title="user.displayName"
          :subtitle="user.profile().firstname + ' ' + user.profile().surname"
          :to="{ name: 'profile', query: { isDetail: true } }"
        >
          <template #pre>
            <v-list-item-avatar>
              <user-avatar :user="$auth.user()" />
            </v-list-item-avatar>
          </template>
        </SidebarListItem>
        <SidebarListItem
          :title="$tc('views.camp.navigation.mobile.navSidebar.itemCamps', 2)"
          icon="mdi-format-list-bulleted-triangle"
          :to="{ name: 'camps', query: { isDetail: true } }"
        />
      </v-list>

      <v-divider />

      <v-list>
        <SidebarListItem
          :title="camp().name"
          :subtitle="camp().motto"
          two-line
          hide-avatar
          hide-chevron
        />
        <v-divider inset i />
        <SidebarListItem
          :title="$tc('views.camp.navigation.mobile.navSidebar.itemCampAdmin')"
          icon="mdi-cogs"
          :to="campRoute(camp(), 'admin', { isDetail: true })"
        />
        <v-divider inset />
        <SidebarListItem
          :title="$tc('views.camp.navigation.mobile.navSidebar.itemCollaborators')"
          icon="mdi-account-group"
          :to="campRoute(camp(), 'collaborators', { isDetail: true })"
        />
        <v-divider inset />
        <SidebarListItem
          :title="$tc('views.camp.navigation.mobile.navSidebar.itemPrinting')"
          icon="mdi-file-outline"
          :to="campRoute(camp(), 'print', { isDetail: true })"
        />
      </v-list>
      <div class="mt-auto">
        <v-btn
          x-large
          height="56"
          text
          tile
          block
          class="ec-close-drawer pb-safe"
          @click="$emit('input', false)"
        >
          {{ $tc('views.camp.navigation.mobile.navSidebar.itemClose') }}
          <v-icon right>mdi-close</v-icon>
        </v-btn>
      </div>
    </div>
  </v-navigation-drawer>
</template>

<script>
import { campRoute } from '@/router'
import UserAvatar from '@/components/user/UserAvatar.vue'
import SidebarListItem from '@/components/layout/SidebarListItem.vue'

export default {
  name: 'navSidebar',
  components: {
    SidebarListItem,
    UserAvatar,
  },
  props: {
    value: { type: Boolean, required: true },
    camp: { type: Function, required: true },
  },
  computed: {
    user() {
      return this.$auth.user()
    },
  },
  methods: {
    campRoute,
  },
}
</script>

<style lang="scss" scoped>
.v-bottom-navigation--fixed {
  height: auto !important;
  min-height: 56px;
  padding-bottom: env(safe-area-inset-bottom);
}

.v-application .ec-close-drawer {
  background-color: #{map-get($blue-grey, 'lighten-5')};
  border-top: 1px solid #{map-get($blue-grey, 'lighten-4')};
}
</style>
