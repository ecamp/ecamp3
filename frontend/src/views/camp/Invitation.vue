<template>
  <auth-container>
    <div v-if="invitationFound !== false && campCollaborationOpen !== false">
      <h1 v-if="isLoggedIn" class="display-1">{{ this.$tc('components.invitation.userWelcome') }} {{ userDisplayName }}</h1>
      <h1 class="display-1">{{ this.$tc('components.invitation.title') }} {{ camp ? camp.title : '' }}</h1>

      <v-spacer />
      <div v-if="isLoggedIn">
        <v-btn v-if="userAlreadyInCamp !== true" color="primary"
               x-large
               class="my-4" block
               @click="acceptInvitation">
          {{ this.$tc('components.invitation.acceptCurrentAuth') }}
        </v-btn>
        <div v-else>
          <v-alert type="warning">
            {{ this.$tc('components.invitation.userAlreadyInCamp') }}
          </v-alert>
          <v-spacer />
          <v-btn color="primary"
                 x-large
                 class="my-4" block
                 :to="campLink">
            {{ this.$tc('components.invitation.openCamp') }}
          </v-btn>
        </div>
        <v-btn color="primary"
               x-large
               class="my-4" block
               @click="useAnotherAccount">
          {{ this.$tc('components.invitation.useOtherAuth') }}
        </v-btn>
      </div>
      <div v-else>
        <v-btn color="primary"
               x-large
               class="my-4" block
               :to="loginLink">
          {{ this.$tc('components.invitation.login') }}
        </v-btn>
        <v-btn color="primary"
               x-large
               class="my-4" block
               :to="{ name: 'register' }">
          {{ this.$tc('components.invitation.register') }}
        </v-btn>
      </div>
      <v-btn color="red"
             x-large
             class="my-4" block>
        {{ this.$tc('components.invitation.reject') }}
      </v-btn>
    </div>
    <v-alert v-else type="error">
      {{ this.$tc('components.invitation.notFound') }}
    </v-alert>
  </auth-container>
</template>

<script>
import AuthContainer from '@/components/layout/AuthContainer'
import { campRoute, loginRoute } from '@/router'

export default {
  name: 'Invitation',
  components: { AuthContainer },
  props: {
    campCollaboration: { type: Function, required: true }
  },
  data: () => ({ invitationFound: undefined }),
  computed: {
    camp () {
      if (!this.invitationFound) {
        return undefined
      }
      return this.campCollaboration().camp()
    },
    campLink () {
      return campRoute(this.camp)
    },
    campCollaborationOpen () {
      if (this.invitationFound === undefined) {
        return undefined
      }
      return this.campCollaboration().status === 'invited'
    },
    isLoggedIn () {
      return this.$auth.isLoggedIn()
    },
    loginLink () {
      return loginRoute(this.$route.fullPath)
    },
    profile () {
      if (this.isLoggedIn !== true) {
        return undefined
      }
      return this.api.get().profile()
    },
    userAlreadyInCamp () {
      if (this.isLoggedIn !== true) {
        return undefined
      }
      if (this.camp === undefined) {
        return undefined
      }
      const alreadyExistingCampCollaborations = this.api.get()
        .campCollaborations({ campId: this.camp.id })
        .items
        .filter(campCollaboration => campCollaboration.user)
        .filter(campCollaboration => this.profile.username === campCollaboration.user().username)
      return alreadyExistingCampCollaborations.length > 0
    },
    userDisplayName () {
      if (this.profile === undefined) {
        return undefined
      }
      return this.profile.displayName
    }
  },
  mounted: function () {
    this.campCollaboration()._meta.load.then(
      () => { this.invitationFound = true },
      () => { this.invitationFound = false })
    if (this.$auth.isLoggedIn() === true) {
      this.api.get().profile()
    }
  },
  methods: {
    useAnotherAccount () {
      // Remember the login link for after we are logged out
      const loginLink = this.loginLink
      this.$auth.logout().then(__ => this.$router.push(loginLink))
    },
    acceptInvitation () {
      if (!this.invitationFound) return
      const me = this
      this.api.href(this.api.get()._meta.self, 'invitation', {
        action: 'accept',
        inviteKey: this.$route.params.inviteKey
      }).then(postUrl => me.api.post(postUrl, {}))
        .then(_ => { me.$router.push(me.campLink) },
          e => console.log(e))
    }
  }
}
</script>

<style scoped>

</style>
