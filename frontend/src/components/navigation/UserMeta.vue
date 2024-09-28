<template>
  <v-menu
    v-if="authUser"
    v-model="open"
    offset-y
    dark
    right
    rounded
    :content-class="
      ['ec-usermenu my-4', $vuetify.breakpoint.xsOnly && 'rounded-lg mt-2'].join(' ')
    "
    transition="slide-y-transition"
    :close-on-content-click="false"
    z-index="5"
  >
    <template #activator="{ on, value, attrs }">
      <v-toolbar-items v-if="!avatarOnly">
        <v-btn
          right
          text
          v-bind="attrs"
          :class="[btnClasses, { 'v-btn--open': value }]"
          v-on="on"
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
          </template>
          <span class="sr-only-sm-and-down mx-3">
            {{ authUser.displayName }}
          </span>
        </v-btn>
      </v-toolbar-items>
      <v-btn
        v-else
        fab
        text
        v-bind="attrs"
        :class="[btnClasses, { 'v-btn--open': value }]"
        v-on="on"
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
        </template>
        <span class="sr-only-sm-and-down mx-3">
          {{ authUser.displayName }}
        </span>
      </v-btn>
    </template>
    <v-list class="user-nav py-0" tag="ul" light>
      <v-list-item
        tag="li"
        block
        :to="{ name: 'profile', query: { isDetail: true } }"
        @click="open = false"
      >
        <v-icon left>mdi-account</v-icon>
        <span>{{ $tc('components.navigation.userMeta.profile') }}</span>
      </v-list-item>
      <v-list-item block tag="li" exact :to="{ name: 'camps' }" @click="open = false">
        <v-icon left>mdi-format-list-bulleted-triangle</v-icon>
        <span>{{ $tc('components.navigation.userMeta.myCamps') }}</span>
      </v-list-item>
      <v-list-item
        block
        tag="li"
        exact
        :to="{ name: 'invitations' }"
        @click="open = false"
      >
        <v-icon left>mdi-email</v-icon>
        <span>{{ $tc('components.navigation.userMeta.invitations') }}</span>
        <v-list-item-action-text v-if="invitationCount > 0">
          <v-badge inline bordered color="#f00" :content="invitationCount" />
        </v-list-item-action-text>
      </v-list-item>
      <v-list-item
        v-if="isAdmin"
        block
        tag="li"
        exact
        :to="{ name: 'admin/debug' }"
        @click="open = false"
      >
        <v-icon left>mdi-coffee</v-icon>
        <span>{{ $tc('components.navigation.userMeta.admin') }}</span>
      </v-list-item>
      <v-list-item
        v-if="!$vuetify.breakpoint.lgAndUp"
        block
        :href="helpLink"
        target="_blank"
      >
        <v-icon left>mdi-help-circle</v-icon>
        <span>{{ $tc('global.navigation.help') }}</span>
        <v-spacer />
        <v-icon small right>mdi-open-in-new</v-icon>
      </v-list-item>
      <v-list-item block :href="newsLink" target="_blank">
        <v-icon left>mdi-script-text-outline</v-icon>
        <span>{{ $tc('global.navigation.news') }}</span>
        <v-spacer />
        <v-icon small right>mdi-open-in-new</v-icon>
      </v-list-item>
      <v-divider />
      <v-list-item block tag="li" @click="logout">
        <v-progress-circular
          v-if="logoutInProgress"
          indeterminate
          size="18"
          class="mr-2"
        />
        <v-icon v-else left>mdi-logout</v-icon>

        <span>{{ $tc('components.navigation.userMeta.logOut') }}</span>
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
    }
  },
  computed: {
    invitationCount() {
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
      return this.camp
        ?.campCollaborations()
        .items.find(
          (collaboration) =>
            this.authUser?._meta?.self === collaboration.user?.()?._meta?.self
        )
    },
  },
  mounted() {
    this.isAdmin = isAdmin()
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
