<template>
  <v-menu
    v-if="authUser"
    v-model="open"
    dark
    location="bottom right"
    :content-class="
      ['ec-usermenu', $vuetify.display.xsOnly && 'rounded-lg mt-2'].join(' ')
    "
    transition="slide-y-transition"
    :close-on-content-click="false"
    z-index="5"
  >
    <template #activator="{ props }">
      <v-toolbar-items v-if="!avatarOnly">
        <v-btn
          location="right"
          variant="text"
          v-bind="props"
          :class="[btnClasses, { 'v-btn--open': value }]"
        >
          <user-avatar v-if="authUser" :user="authUser" :size="40" />
          <span class="sr-only-sm-and-down mx-3">
            {{ authUser.displayName }}
          </span>
        </v-btn>
      </v-toolbar-items>
      <v-btn
        v-else
        fab
        variant="text"
        v-bind="props"
        :class="[btnClasses, { 'v-btn--open': value }]"
      >
        <user-avatar v-if="authUser" :user="authUser" :size="40" />
        <span class="sr-only-sm-and-down mx-3">
          {{ authUser.displayName }}
        </span>
      </v-btn>
    </template>
    <v-list density="compact" class="user-nav" tag="ul" light color="blue-grey-lighten-5">
      <v-list-item
        tag="li"
        block
        :to="{ name: 'profile', query: { isDetail: true } }"
        @click="open = false"
      >
        <v-icon start>mdi-account</v-icon>
        <span>{{ $tc('components.navigation.userMeta.profile') }}</span>
      </v-list-item>
      <v-list-item block tag="li" exact :to="{ name: 'camps' }" @click="open = false">
        <v-icon start>mdi-format-list-bulleted-triangle</v-icon>
        <span>{{ $tc('components.navigation.userMeta.myCamps') }}</span>
      </v-list-item>
      <v-list-item block tag="li" @click="logout">
        <v-progress-circular
          v-if="logoutInProgress"
          indeterminate
          size="18"
          class="mr-2"
        />
        <v-icon v-else start>mdi-logout</v-icon>

        <span>{{ $tc('components.navigation.userMeta.logOut') }}</span>
      </v-list-item>
    </v-list>
  </v-menu>
</template>

<script>
import UserAvatar from '../user/UserAvatar.vue'
import { mapGetters } from 'vuex'

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
  },
  data() {
    return {
      open: false,
      logoutInProgress: false,
    }
  },
  computed: {
    ...mapGetters({
      authUser: 'getLoggedInUser',
    }),
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
