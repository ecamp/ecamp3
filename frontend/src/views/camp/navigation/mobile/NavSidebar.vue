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
        <h2 class="title text-center">eCamp</h2>
      </div>
      <v-divider class="blue-grey darken-2" />
      <v-list>
        <SidebarListItem
          v-if="user && !user._meta.loading"
          :title="user.displayName"
          :subtitle="
            user.profile().nickname &&
            user.profile().firstname + ' ' + user.profile().surname
          "
          :to="{ name: 'profile', query: { isDetail: true } }"
        >
          <template #pre>
            <v-list-item-avatar>
              <UserAvatar :user="user" :camp-collaboration="currentCampCollaboration" />
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

      <v-list v-if="!camp._meta.loading">
        <SidebarListItem
          :title="camp.title"
          :subtitle="camp.motto"
          two-line
          hide-avatar
          hide-chevron
          title-overflow
        />
        <v-divider inset i />
        <SidebarListItem
          :to="adminRoute(camp, 'info')"
          :title="$tc('views.camp.navigation.mobile.navSidebar.itemInfos')"
          icon="mdi-cogs"
        />
        <v-divider inset />
        <SidebarListItem
          :to="adminRoute(camp, 'activity')"
          :title="$tc('views.camp.navigation.mobile.navSidebar.itemActivity')"
          :subtitle="$tc('views.camp.navigation.mobile.navSidebar.itemActivitySubtitle')"
          icon="mdi-view-dashboard"
        />
        <v-divider inset />
        <SidebarListItem
          :title="$tc('views.camp.navigation.mobile.navSidebar.itemCollaborators')"
          icon="mdi-account-group"
          :to="adminRoute(camp, 'collaborators')"
        />
        <v-divider inset />
        <SidebarListItem
          :to="adminRoute(camp, 'material')"
          :title="$tc('views.camp.navigation.mobile.navSidebar.itemMaterialLists')"
          icon="mdi-package-variant"
        />
        <v-divider inset />
        <SidebarListItem
          :title="$tc('views.camp.navigation.mobile.navSidebar.itemPrinting')"
          icon="mdi-file"
          :to="adminRoute(camp, 'print')"
        />
      </v-list>

      <v-divider />

      <v-list>
        <SidebarListItem
          :title="$tc('global.navigation.help')"
          icon="mdi-help-circle-outline"
          :href="helpLink"
          target="_blank"
          hide-chevron
        />
        <SidebarListItem
          :title="$tc('global.navigation.news')"
          icon="mdi-script-text-outline"
          :href="newsLink"
          target="_blank"
          hide-chevron
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
import { campRoute, adminRoute } from '@/router'
import UserAvatar from '@/components/user/UserAvatar.vue'
import SidebarListItem from '@/components/layout/SidebarListItem.vue'
import { mapGetters } from 'vuex'
import { getEnv } from '@/environment.js'

export default {
  name: 'NavSidebar',
  components: {
    SidebarListItem,
    UserAvatar,
  },
  props: {
    value: { type: Boolean, required: true },
    camp: { type: Object, required: true },
  },
  computed: {
    newsLink() {
      return getEnv().NEWS_LINK
    },
    helpLink() {
      return getEnv().HELP_LINK
    },
    ...mapGetters({
      user: 'getLoggedInUser',
    }),
    currentCampCollaboration() {
      return this.camp
        ?.campCollaborations()
        .items.find(
          (collaboration) =>
            this.user?._meta?.self === collaboration.user?.()?._meta?.self
        )
    },
  },
  methods: {
    adminRoute,
    campRoute,
  },
}
</script>

<style lang="scss" scoped>
.v-application .ec-close-drawer {
  background-color: #{map-get($blue-grey, 'lighten-5')};
  border-top: 1px solid #{map-get($blue-grey, 'lighten-4')};
}
</style>
