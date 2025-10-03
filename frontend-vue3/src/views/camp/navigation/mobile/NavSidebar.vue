<template>
  <v-navigation-drawer
    :model-value="value"
    app
    location="right"
    width="300"
    @update:model-value="$emit('input', $event)"
  >
    <div class="d-flex flex-column fill-height">
      <div class="d-flex flex-column align-center bg-blue-grey-darken-4 text-white py-6">
        <v-icon size="x-large">$ecamp</v-icon>
        <h2 class="text-h6 text-center">eCamp</h2>
      </div>
      <v-divider class="bg-blue-grey-darken-2" />
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
          :title="$t('views.camp.navigation.mobile.navSidebar.itemCamps', 2)"
          icon="mdi-format-list-bulleted-triangle"
          :to="{ name: 'camps', query: { isDetail: true } }"
        />
      </v-list>

      <v-divider />

      <v-list v-if="!camp._meta.loading">
        <SidebarListItem
          :title="camp.name"
          :subtitle="camp.motto"
          two-line
          hide-avatar
          hide-chevron
        />
        <v-divider inset i />
        <SidebarListItem
          :to="adminRoute(camp, 'info')"
          :title="$t('views.camp.navigation.mobile.navSidebar.itemInfos')"
          icon="mdi-cogs"
        />
        <v-divider inset />
        <SidebarListItem
          :to="adminRoute(camp, 'activity')"
          :title="$t('views.camp.navigation.mobile.navSidebar.itemActivity')"
          :subtitle="$t('views.camp.navigation.mobile.navSidebar.itemActivitySubtitle')"
          icon="mdi-view-dashboard"
        />
        <v-divider inset />
        <SidebarListItem
          :title="$t('views.camp.navigation.mobile.navSidebar.itemCollaborators')"
          icon="mdi-account-group"
          :to="adminRoute(camp, 'collaborators')"
        />
        <v-divider inset />
        <SidebarListItem
          :to="adminRoute(camp, 'material')"
          :title="$t('views.camp.navigation.mobile.navSidebar.itemMaterialLists')"
          icon="mdi-package-variant"
        />
        <v-divider inset />
        <SidebarListItem
          :title="$t('views.camp.navigation.mobile.navSidebar.itemPrinting')"
          icon="mdi-file"
          :to="adminRoute(camp, 'print')"
        />
      </v-list>

      <v-divider />

      <v-list>
        <SidebarListItem
          :title="$t('global.navigation.help')"
          icon="mdi-help-circle-outline"
          :href="helpLink"
          target="_blank"
          hide-chevron
        />
        <SidebarListItem
          :title="$t('global.navigation.news')"
          icon="mdi-script-text-outline"
          :href="newsLink"
          target="_blank"
          hide-chevron
        />
      </v-list>
      <div class="mt-auto">
        <v-btn
          size="x-large"
          height="56"
          variant="text"
          tile
          block
          class="ec-close-drawer pb-safe"
          @click="$emit('input', false)"
        >
          {{ $t('views.camp.navigation.mobile.navSidebar.itemClose') }}
          <v-icon end>mdi-close</v-icon>
        </v-btn>
      </div>
    </div>
  </v-navigation-drawer>
</template>

<script>
import { adminRoute, campRoute } from '@/router'
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
@use '/src/scss/variables';

.v-bottom-navigation--fixed {
  height: auto !important;
  min-height: 56px;
  padding-bottom: env(safe-area-inset-bottom);
}

.v-application .ec-close-drawer {
  background-color: #{map-get(variables.$blue-grey, 'lighten-5')};
  border-top: 1px solid #{map-get(variables.$blue-grey, 'lighten-4')};
}
</style>
