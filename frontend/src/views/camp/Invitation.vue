<template>
  <auth-container>
    <div v-if="campCollaborationFound !== false && campCollaborationOpen !== false">
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
    campCollaborations: { type: Function, required: true }
  },
  computed: {
    camp () {
      if (this.campCollaboration === undefined) {
        return undefined
      }
      return this.campCollaboration.camp()
    },
    campCollaboration () {
      const campCollaborations = this.campCollaborations()
      if (campCollaborations._meta.loading) {
        return undefined
      }
      if (campCollaborations.items.length === 1) {
        return campCollaborations.items[0]
      } else {
        return null
      }
    },
    campCollaborationFound () {
      if (this.campCollaboration === undefined) {
        return undefined
      }
      return this.campCollaboration != null
    },
    campCollaborationOpen () {
      if (!this.campCollaboration) {
        return undefined
      }
      return this.campCollaboration.status === 'invited'
    },
    isLoggedIn () {
      return this.$auth.isLoggedIn()
    },
    loginLink
  },
  methods: {
    useAnotherAccount () {
      this.$auth.logout().then(__ => this.$router.push(loginLink()))
    },
    acceptInvitation () {
      if (this.campCollaboration._meta.loading) return
      const patchedCampCollaboration = {
        status: 'established',
        inviteKey: this.$route.params.inviteKey
      }
      const me = this
      this.api.patch(this.campCollaboration._meta.self, patchedCampCollaboration)
        .then(_ => {
          me.$router.push(campRoute(me.camp))
        },
        e => console.log(e))
    }
  }
}
</script>

<style scoped>

</style>
