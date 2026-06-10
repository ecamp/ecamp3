<template>
  <v-navigation-drawer
    :model-value="modelValue"
    app
    location="right"
    scrim
    width="300"
    @update:model-value="$emit('update:modelValue', $event)"
  >
    <div class="d-flex flex-column min-h-full">
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
            <UserAvatar
              :user="user"
              :camp-collaboration="currentCampCollaboration"
              size="40"
            />
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
          v-if="hasChecklists"
          :to="campRoute(camp, 'overview/checklists')"
          :title="$t('views.camp.navigation.mobile.navSidebar.itemChecklists')"
          icon="mdi-clipboard-list-outline"
        />
        <v-divider v-if="hasChecklists" inset />
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
          size="large"
          height="56"
          variant="text"
          tile
          block
          class="ec-close-drawer pb-safe"
          @click="$emit('update:modelValue', false)"
        >
          {{ $t('views.camp.navigation.mobile.navSidebar.itemClose') }}
          <v-icon end>mdi-close</v-icon>
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
    modelValue: { type: Boolean, required: true },
    camp: { type: Object, required: true },
  },
  emits: ['update:modelValue'],
  computed: {
    hasChecklists() {
      return this.camp.checklists().items.length > 0
    },
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
      if (typeof this.camp?.campCollaborations !== 'function') {
        return undefined
      }
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
@use 'vuetify/settings';
@use 'sass:map';

.v-application .ec-close-drawer {
  background-color: #{map.get(settings.$blue-grey, 'lighten-5')};
  border-top: 1px solid #{map.get(settings.$blue-grey, 'lighten-4')};
}
</style>
