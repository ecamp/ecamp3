<template>
  <div>
    <v-card-text v-if="invitations.items.length === 0">
      <p>
        {{
          $tc('components.personalInvitations.personalInvitations.noOpenInvitations', 0, {
            email: authUser.profile().email,
          })
        }}
      </p>
    </v-card-text>
    <template v-if="$vuetify.breakpoint.mdAndUp">
      <v-list-item v-for="invitation in invitations.items" :key="invitation._meta.self">
        <v-list-item-content>
          <v-list-item-title>{{ invitation.campTitle }}</v-list-item-title>
        </v-list-item-content>
        <v-list-item-action>
          <DialogPersonalInvitationReject
            :entity="invitation"
            :camp-title="invitation.campTitle"
            @submit="rejectInvitation(invitation)"
          >
            <template #activator="{ on }">
              <v-btn class="px-4" text v-on="on">
                {{ $tc('components.personalInvitations.personalInvitations.reject') }}
              </v-btn>
            </template>
          </DialogPersonalInvitationReject>
        </v-list-item-action>
        <v-list-item-action>
          <v-btn color="primary" @click="acceptInvitation(invitation)">
            {{ $tc('components.personalInvitations.personalInvitations.accept') }}<br />
          </v-btn>
        </v-list-item-action>
      </v-list-item>
    </template>
    <template v-else>
      <v-list-group v-for="invitation in invitations.items" :key="invitation._meta.self">
        <template #activator>
          <v-list-item-content>
            <v-list-item-title>{{ invitation.campTitle }}</v-list-item-title>
          </v-list-item-content>
        </template>
        <v-list-item>
          <v-list-item-action>
            <DialogPersonalInvitationReject
              :entity="invitation"
              :camp-title="invitation.campTitle"
              @submit="rejectInvitation(invitation)"
            >
              <template #activator="{ on }">
                <v-btn class="px-4" text v-on="on">
                  {{ $tc('components.personalInvitations.personalInvitations.reject') }}
                </v-btn>
              </template>
            </DialogPersonalInvitationReject>
          </v-list-item-action>
          <v-spacer />
          <v-list-item-action>
            <v-btn color="primary" @click="acceptInvitation(invitation)">
              {{ $tc('components.personalInvitations.personalInvitations.accept') }}<br />
            </v-btn>
          </v-list-item-action>
        </v-list-item>
      </v-list-group>
    </template>
  </div>
</template>
<script>
import { errorToMultiLineToast } from '../toast/toasts.js'
import { isNavigationFailure, NavigationFailureType } from 'vue-router'
import DialogPersonalInvitationReject from './DialogPersonalInvitationReject.vue'
import { mapGetters } from 'vuex'

const ignoreNavigationFailure = (e) => {
  if (!isNavigationFailure(e, NavigationFailureType.redirected)) {
    return Promise.reject(e)
  }
}

export default {
  name: 'PersonalInvitations',
  components: { DialogPersonalInvitationReject },
  computed: {
    invitations() {
      return this.api.get().personalInvitations()
    },
    ...mapGetters({
      authUser: 'getLoggedInUser',
    }),
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
