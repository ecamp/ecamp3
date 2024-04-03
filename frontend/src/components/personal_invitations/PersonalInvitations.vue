<template>
  <v-list class="py-0">
    <v-list-item
      v-for="invitation in invitations.items"
      :key="invitation._meta.self"
      two-line
    >
      <v-list-item-content>
        <v-list-item-title>{{ invitation.campTitle }}</v-list-item-title>
      </v-list-item-content>
      <v-list-item-action>
        <PromptPersonalInvitationReject
          :entity="invitation"
          :camp-title="invitation.campTitle"
          @submit="rejectInvitation(invitation)"
        >
          <template #activator="{ on }">
            <v-btn class="px-4" text v-on="on">
              {{ $tc('components.personalInvitations.personalInvitations.reject') }}
            </v-btn>
          </template>
        </PromptPersonalInvitationReject>
      </v-list-item-action>
      <v-list-item-action>
        <v-btn color="primary" @click="acceptInvitation(invitation)">
          {{ $tc('components.personalInvitations.personalInvitations.accept') }}<br />
        </v-btn>
      </v-list-item-action>
    </v-list-item>
  </v-list>
</template>
<script>
import { errorToMultiLineToast } from '../toast/toasts.js'
import { isNavigationFailure, NavigationFailureType } from 'vue-router'
import PromptPersonalInvitationReject from './PromptPersonalInvitationReject.vue'

const ignoreNavigationFailure = (e) => {
  if (!isNavigationFailure(e, NavigationFailureType.redirected)) {
    return Promise.reject(e)
  }
}

export default {
  name: 'PersonalInvitations',
  components: { PromptPersonalInvitationReject },
  computed: {
    invitations() {
      return this.api.get().personalInvitations()
    },
  },
  methods: {
    acceptInvitation(invitation) {
      this.api
        .href(this.api.get(), 'personalInvitations', {
          action: 'accept',
          id: invitation.id,
        })
        .then((postUrl) => this.api.patch(postUrl, {}))
        .then(
          (_) => {
            this.$router.push(this.campLink(invitation)).catch(ignoreNavigationFailure)
          },
          () => {
            this.$router
              .push({ name: 'invitationUpdateError' })
              .catch(ignoreNavigationFailure)
          }
        )
        .then(() => {
          this.invitations.$reload()
        })
        .catch((e) => this.$toast.error(errorToMultiLineToast(e)))
    },
    rejectInvitation(invitation) {
      this.api
        .href(this.api.get(), 'personalInvitations', {
          action: 'reject',
          id: invitation.id,
        })
        .then((postUrl) => {
          return this.api.patch(postUrl, {})
        })
        .then(() => {
          this.invitations.$reload()
        })
        .catch((e) => this.$toast.error(errorToMultiLineToast(e)))
    },
    campLink(invitation) {
      return {
        name: 'camp/dashboard',
        params: { campId: invitation.campId },
      }
    },
  },
}
</script>
