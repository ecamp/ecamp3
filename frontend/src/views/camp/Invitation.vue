<template>
  <auth-container>
    <div v-if="!ready" class="text-center">
      <v-progress-circular size="64" indeterminate color="primary" />
    </div>
    <div v-else-if="invitationFound === true">
      <h1 class="display-1">
        {{ $tc('components.invitation.title') }} "{{ invitation().campTitle }}"
      </h1>

      <v-spacer />
      <div v-if="authUser">
        <v-btn
          v-if="!invitation().userAlreadyInCamp"
          color="primary"
          x-large
          class="my-4"
          block
          @click="acceptInvitation"
        >
          {{ $tc('components.invitation.acceptCurrentAuth') }}<br />
        </v-btn>
        <div v-else>
          <v-alert type="warning">
            {{ $tc('components.invitation.userAlreadyInCamp') }}
          </v-alert>
          <v-spacer />
          <v-btn color="primary" x-large class="my-4" block :to="campLink">
            {{ $tc('components.invitation.openCamp') }}
          </v-btn>
        </div>
        <v-btn color="primary" x-large class="my-4" block @click="useAnotherAccount">
          {{ $tc('components.invitation.useOtherAuth') }}
        </v-btn>
      </div>
      <div v-else>
        <v-btn color="primary" x-large class="my-4" block :to="loginLink">
          {{ $tc('components.invitation.login') }}
        </v-btn>
        <v-btn color="primary" x-large class="my-4" block :to="{ name: 'register' }">
          {{ $tc('components.invitation.register') }}
        </v-btn>
      </div>
      <v-btn color="red" x-large class="my-4" block @click="rejectInvitation">
        {{ $tc('components.invitation.reject') }}
      </v-btn>
    </div>
    <v-alert v-else-if="invitationFound === false" type="error">
      {{ $tc('components.invitation.notFound') }}
    </v-alert>
    <div class="mb-4 mt-8">
      <router-link color="primary" x-large block :to="{ name: 'home' }">
        {{ $tc('components.invitation.backToHome') }}
      </router-link>
    </div>
  </auth-container>
</template>

<script>
import AuthContainer from '@/components/layout/AuthContainer.vue'
import { loginRoute } from '@/router'
import VueRouter from 'vue-router'

const { isNavigationFailure, NavigationFailureType } = VueRouter
const ignoreNavigationFailure = (e) => {
  if (!isNavigationFailure(e, NavigationFailureType.redirected)) {
    return Promise.reject(e)
  }
}

export default {
  name: 'Invitation',
  components: { AuthContainer },
  props: {
    invitation: { type: Function, required: true },
  },
  data: () => ({
    invitationFound: undefined,
  }),
  computed: {
    campLink() {
      return {
        name: 'camp/program',
        params: { campId: this.invitation().campId },
      }
    },
    loginLink() {
      return loginRoute(this.$route.fullPath)
    },
    ready() {
      return this.invitationFound !== undefined
    },
    userDisplayName() {
      return this.invitation().userDisplayName
    },
    authUser() {
      return this.$store.state.auth.user
    },
  },
  mounted() {
    this.invitationFound = undefined

    // Content of api response depends on authenticated user --> reload every time this component is mounted
    this.invitation()
      .$reload()
      .then(
        () => {
          this.invitationFound = true
        },
        () => {
          this.invitationFound = false
        }
      )
  },
  methods: {
    useAnotherAccount() {
      // Remember the login link for after we are logged out
      const loginLink = this.loginLink
      this.$auth.logout().then((_) => this.$router.push(loginLink))
    },
    acceptInvitation() {
      this.api
        .href(this.api.get(), 'invitations', {
          action: 'accept',
          id: this.$route.params.inviteKey,
        })
        .then((postUrl) => this.api.patch(postUrl, {}))
        .then(
          (_) => {
            this.$router.push(this.campLink).catch(ignoreNavigationFailure)
          },
          () => {
            this.$router
              .push({ name: 'invitationUpdateError' })
              .catch(ignoreNavigationFailure)
          }
        )
    },
    rejectInvitation() {
      this.api
        .href(this.api.get(), 'invitations', {
          action: 'reject',
          id: this.$route.params.inviteKey,
        })
        .then((postUrl) => this.api.patch(postUrl, {}))
        .then(
          (_) => {
            this.$router
              .push({ name: 'invitationRejected' })
              .catch(ignoreNavigationFailure)
          },
          () => {
            this.$router
              .push({ name: 'invitationUpdateError' })
              .catch(ignoreNavigationFailure)
          }
        )
    },
  },
}
</script>

<style scoped></style>
