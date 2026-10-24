<template>
  <div>
    <v-card-text v-if="invitations.items.length === 0">
      <p v-if="authUser">
        {{
          $t('components.personalInvitations.personalInvitations.noOpenInvitations', {
            email: authUser.profile().email,
          })
        }}
      </p>
    </v-card-text>
    <template v-if="$vuetify.display.mdAndUp">
      <v-list-item
        v-for="invitation in invitations.items"
        :key="invitation._meta.self"
        lines="two"
      >
        <v-list-item-title>{{ invitation.campTitle }}</v-list-item-title>
        <template #append>
          <v-list-item-action class="gap-4">
            <DialogPersonalInvitationReject
              :entity="invitation"
              :camp-title="invitation.campTitle"
              @submit="rejectInvitation(invitation)"
            >
              <template #activator="{ props }">
                <v-btn class="px-4" variant="text" v-bind="props">
                  {{ $t('components.personalInvitations.personalInvitations.reject') }}
                </v-btn>
              </template>
            </DialogPersonalInvitationReject>
            <v-btn color="primary" @click="acceptInvitation(invitation)">
              {{ $t('components.personalInvitations.personalInvitations.accept') }}<br />
            </v-btn>
          </v-list-item-action>
        </template>
      </v-list-item>
    </template>
    <template v-else>
      <template v-for="invitation in invitations.items" :key="invitation._meta.self">
        <v-list-item lines="two">
          <v-list-item-title>{{ invitation.campTitle }}</v-list-item-title>
          <v-list-item-action class="mt-4">
            <DialogPersonalInvitationReject
              :entity="invitation"
              :camp-title="invitation.campTitle"
              @submit="rejectInvitation(invitation)"
            >
              <template #activator="{ props }">
                <v-btn class="px-4" variant="text" v-bind="props">
                  {{ $t('components.personalInvitations.personalInvitations.reject') }}
                </v-btn>
              </template>
            </DialogPersonalInvitationReject>
            <v-spacer />
            <v-btn color="primary" @click="acceptInvitation(invitation)">
              {{ $t('components.personalInvitations.personalInvitations.accept') }}<br />
            </v-btn>
          </v-list-item-action>
        </v-list-item>
        <v-divider></v-divider>
      </template>
    </template>
  </div>
</template>
<script>
import { errorToMultiLineToast } from '../toast/toasts.js'
import { isNavigationFailure, NavigationFailureType } from 'vue-router'
import DialogPersonalInvitationReject from './DialogPersonalInvitationReject.vue'
import { mapGetters } from 'vuex'
import { useToast } from '@/components/toast/useToast.js'

const ignoreNavigationFailure = (e) => {
  if (!isNavigationFailure(e, NavigationFailureType.redirected)) {
    return Promise.reject(e)
  }
}

export default {
  name: 'PersonalInvitations',
  components: { DialogPersonalInvitationReject },
  setup() {
    const toast = useToast()
    return { toast }
  },
  computed: {
    invitations() {
      return this.api.get().personalInvitations()
    },
    ...mapGetters({
      authUser: 'getLoggedInUser',
    }),
  },
  async mounted() {
    const user = await this.$auth.loadUser()
    await user.profile()._meta.load
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
        .catch((e) => this.toast.error(errorToMultiLineToast(e)))
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
        .catch((e) => this.toast.error(errorToMultiLineToast(e)))
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
