<template>
  <v-menu
    v-if="authUser"
    v-model="open"
    dark
    location="bottom end"
    rounded
    :content-class="['py-4 rounded-lg', !$vuetify.display.xs ? 'mt-2' : ''].join(' ')"
    transition="slide-y-transition"
    :close-on-content-click="false"
  >
    <template #activator="{ props, isActive }">
      <v-toolbar-items v-if="!avatarOnly">
        <v-btn
          start
          variant="text"
          v-bind="props"
          :class="[btnClasses, { 'v-btn--open': isActive }]"
        >
          <template v-if="authUser">
            <v-badge v-if="invitationCount > 0" color="#f00" dot overlap bordered>
              <UserAvatar
                :user="authUser"
                :camp-collaboration="currentCampCollaboration"
                :size="40"
              />
            </v-badge>
            <UserAvatar
              v-else
              :user="authUser"
              :camp-collaboration="currentCampCollaboration"
              :size="40"
            />
            <span class="sr-only-sm-and-down mx-3">
              {{ authUser.displayName }}
            </span>
          </template>
        </v-btn>
      </v-toolbar-items>
      <v-btn
        v-else
        icon
        variant="text"
        v-bind="props"
        :class="[btnClasses, { 'v-btn--open': isActive }]"
      >
        <v-badge v-if="invitationCount > 0" color="#f00" dot overlap bordered>
          <UserAvatar
            :user="authUser"
            :camp-collaboration="currentCampCollaboration"
            :size="40"
          />
        </v-badge>
        <UserAvatar
          v-else
          :user="authUser"
          :camp-collaboration="currentCampCollaboration"
          :size="40"
        />
        <span class="sr-only-sm-and-down mx-3">
          {{ authUser.displayName }}
        </span>
      </v-btn>
    </template>
    <v-list class="user-nav py-0" tag="ul" light>
      <v-list-item tag="li" block :to="{ name: 'profile' }" @click="open = false">
        <v-icon start icon="mdi-account" />
        <span>{{ $t('components.navigation.userMeta.profile') }}</span>
      </v-list-item>
      <v-list-item block tag="li" exact :to="{ name: 'camps' }" @click="open = false">
        <v-icon start icon="mdi-format-list-bulleted-triangle" />
        <span>{{ $t('components.navigation.userMeta.myCamps') }}</span>
      </v-list-item>
      <v-list-item
        block
        tag="li"
        exact
        :to="{ name: 'invitations' }"
        @click="open = false"
      >
        <v-icon start>mdi-email</v-icon>
        <span>{{ $t('components.navigation.userMeta.invitations') }}</span>
        <template #append>
          <v-badge
            v-if="invitationCount > 0"
            inline
            bordered
            color="#f00"
            :content="invitationCount"
          />
        </template>
      </v-list-item>
      <v-list-item
        v-if="isAdmin"
        block
        tag="li"
        exact
        :to="{ name: 'admin/debug' }"
        @click="open = false"
      >
        <v-icon start icon="mdi-coffee" />
        <span>{{ $t('components.navigation.userMeta.admin') }}</span>
      </v-list-item>
      <v-list-item
        v-if="!$vuetify.display.lgAndUp"
        block
        :href="helpLink"
        target="_blank"
      >
        <v-icon start icon="mdi-help-circle" />
        <span>{{ $t('global.navigation.help') }}</span>
        <template #append>
          <v-icon size="x-small" end icon="mdi-open-in-new" />
        </template>
      </v-list-item>
      <v-list-item block :href="newsLink" target="_blank">
        <v-icon start>mdi-script-text-outline</v-icon>
        <span>{{ $t('global.navigation.news') }}</span>
        <template #append>
          <v-icon size="x-small" end icon="mdi-open-in-new" />
        </template>
      </v-list-item>
      <v-divider />
      <v-list-item block tag="li" @click="logout">
        <v-progress-circular
          v-if="logoutInProgress"
          indeterminate
          size="18"
          class="mr-2"
        />
        <v-icon v-else start icon="mdi-logout" />

        <span>{{ $t('components.navigation.userMeta.logOut') }}</span>
      </v-list-item>
    </v-list>
  </v-menu>
</template>

<script>
import UserAvatar from '../user/UserAvatar.vue'
import { mapGetters } from 'vuex'
import { getEnv } from '@/environment.js'
import { isAdmin } from '@/plugins/auth'

export default {
  name: 'UserMeta',
  components: { UserAvatar },
  props: {
    avatarOnly: {
      type: Boolean,
      default: false,
    },
    btnClasses: {
      type: String,
      required: false,
      default: '',
    },
    camp: {
      type: Object,
      required: false,
      default: null,
    },
  },
  data() {
    return {
      open: false,
      logoutInProgress: false,
      isAdmin: false,
      deferredLoad: true,
    }
  },
  computed: {
    invitationCount() {
      if (this.deferredLoad) {
        return 0
      }
      return this.api.get().personalInvitations().totalItems
    },
    newsLink() {
      return getEnv().NEWS_LINK
    },
    helpLink() {
      return getEnv().HELP_LINK
    },
    ...mapGetters({
      authUser: 'getLoggedInUser',
    }),
    currentCampCollaboration() {
      if (
        typeof this.camp?.campCollaborations !== 'function' ||
        this.camp.campCollaborations()._meta.loading ||
        !this.authUser
      ) {
        return undefined
      }
      return this.camp
        ?.campCollaborations()
        .items.find(
          (collaboration) =>
            this.authUser._meta?.self === collaboration.user?.()?._meta?.self
        )
    },
  },
  mounted() {
    this.isAdmin = isAdmin()
    setTimeout(() => {
      this.deferredLoad = false
    }, 400)
  },
  methods: {
    async logout() {
      this.logoutInProgress = true
      await this.$auth.logout()
      this.logoutInProgress = false
    },
  },
}
</script>

<style scoped>
.v-badge:deep(.v-badge__badge::after) {
  border-color: red;
}
</style>
