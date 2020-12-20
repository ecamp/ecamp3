<template>
  <auth-container>
    <div v-if="invitationFound !== false && campCollaborationOpen !== false">
      <h1 class="display-1">{{ this.$tc('components.invitation.title') }} {{ camp ? camp.title : '' }}</h1>

      <v-spacer />
      <div v-if="isLoggedIn">
        <v-btn color="primary"
               x-large
               class="my-4" block
               @click="acceptInvitation">
          {{ this.$tc('components.invitation.acceptCurrentAuth') }}
        </v-btn>
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

function loginLink () {
  return loginRoute(this.$route.fullPath)
}

export default {
  name: 'Invitation',
  components: { AuthContainer },
  props: {
    campCollaboration: { type: Function, required: true }
  },
  data: () => ({ invitationFound: undefined }),
  computed: {
    camp () {
      if (this.invitationFound === undefined) {
        return undefined
      }
      return this.campCollaboration().camp()
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
    loginLink
  },
  mounted: function () {
    this.campCollaboration()._meta.load.then(
      // eslint-disable-next-line no-return-assign
      () => this.invitationFound = true,
      // eslint-disable-next-line no-return-assign
      () => this.invitationFound = false)
  },
  methods: {
    useAnotherAccount () {
      this.$auth.logout().then(__ => this.$router.push(loginLink()))
    },
    acceptInvitation () {
      if (!this.invitationFound) return
      const me = this
      this.api.href(this.api.get()._meta.self, 'invitation', {
        action: 'accept',
        inviteKey: this.$route.params.inviteKey
      }).then(postUrl => me.api.post(postUrl, {}))
        .then(_ => { me.$router.push(campRoute(me.camp)) },
          e => console.log(e))
    }
  }
}
</script>

<style scoped>

</style>
