<template>
  <v-container fluid>
    <content-card :title="title" max-width="800" toolbar>
      <v-skeleton-loader
        v-if="loading"
        type="heading, list-item-avatar-two-line"
        class="pa-4"
      />

      <v-card-text v-else-if="loadError" class="text-body-1">
        <v-alert type="error" variant="tonal" :text="loadError" />
      </v-card-text>

      <template v-else>
        <v-alert
          v-if="inviteError"
          class="mt-4 mx-4"
          type="error"
          variant="tonal"
          :text="inviteError"
        />

        <v-card-text v-if="newParticipants.length > 0" class="text-body-1 pb-0">
          <p>{{ $t('views.camp.hitobitoInvite.intro', { provider }) }}</p>
        </v-card-text>
        <HitobitoParticipantList
          v-if="newParticipants.length > 0"
          :title="$t('views.camp.hitobitoInvite.newParticipants')"
          :participants="newParticipants"
          type="invites"
        />

        <HitobitoParticipantList
          v-if="existingParticipants.length > 0 || newParticipants.length === 0"
          :title="$t('views.camp.hitobitoInvite.existingParticipants')"
          :participants="existingParticipants"
          type="existing"
        />

        <v-alert
          v-if="newParticipants.length === 0"
          type="info"
          class="mx-4 mb-4"
          variant="tonal"
          color="blue-darken-2"
          :text="$t('views.camp.hitobitoInvite.allInvited', { provider })"
        />
      </template>

      <v-divider />
      <ContentActions>
        <ButtonBack
          visible-label
          class="mr-auto"
          :disabled="isSaving"
          :to="collaboratorsRoute"
        />
        <ButtonCancel v-if="canInvite" :disabled="isSaving" :to="collaboratorsRoute" />
        <ButtonAdd
          v-if="canInvite"
          icon="mdi-email-fast"
          :loading="isSaving"
          @click="invite"
        >
          {{ $t('views.camp.hitobitoInvite.invite', newParticipants.length) }}
        </ButtonAdd>
      </ContentActions>
    </content-card>
  </v-container>
</template>

<script>
import ButtonBack from '@/components/buttons/ButtonBack.vue'
import ButtonCancel from '@/components/buttons/ButtonCancel.vue'
import ContentActions from '@/components/layout/ContentActions.vue'
import ContentCard from '@/components/layout/ContentCard.vue'
import HitobitoParticipantList from '@/components/campHitobitoInvite/HitobitoParticipantList.vue'
import { partitionParticipants } from '@/components/campHitobitoInvite/participants.js'
import { adminRoute, campRoute } from '@/router.js'
import {
  clearAuthorizationAttempt,
  hasAttemptedAuthorization,
  hitobitoEventParticipantsUri,
  isAccessTokenInvalidError,
  providerNameKey,
  redirectToHitobitoAuthorization,
} from '@/plugins/hitobito.js'

export default {
  name: 'CampHitobitoInvite',
  components: {
    ButtonBack,
    ButtonCancel,
    ContentActions,
    ContentCard,
    HitobitoParticipantList,
  },
  props: {
    camp: { type: Object, required: true },
  },
  data() {
    return {
      loading: true,
      isSaving: false,
      loadError: null,
      inviteError: null,
      participants: [],
      campEmails: [],
    }
  },
  head() {
    return { title: this.title }
  },
  computed: {
    title() {
      return this.$t('views.camp.hitobitoInvite.title', { provider: this.provider })
    },
    provider() {
      return this.$t(providerNameKey(this.camp.hitobitoProvider))
    },
    partitionedParticipants() {
      return partitionParticipants(this.participants, this.campEmails)
    },
    newParticipants() {
      return this.partitionedParticipants.newParticipants
    },
    existingParticipants() {
      return this.partitionedParticipants.existingParticipants
    },
    canInvite() {
      return !this.loading && !this.loadError && this.newParticipants.length > 0
    },
    collaboratorsRoute() {
      return adminRoute(this.camp, 'collaborators')
    },
  },
  async mounted() {
    await this.load()
  },
  methods: {
    async load() {
      this.loading = true
      this.loadError = null
      try {
        const [participants, campEmails] = await Promise.all([
          this.loadHitobitoEventParticipants(),
          this.loadCampEmails(),
        ])
        this.participants = participants
        this.campEmails = campEmails
        clearAuthorizationAttempt(this.camp.hitobitoProvider)
      } catch (error) {
        if (this.redirectToAuthorization(error)) {
          return
        }
        this.handleLoadError(error)
      }
      this.loading = false
    },

    async loadHitobitoEventParticipants() {
      const collection = await this.api.reload(
        hitobitoEventParticipantsUri(
          this.camp.hitobitoProvider,
          this.camp.hitobitoEventId
        )
      )
      return collection.items
    },

    /**
     * Retrieves all email addresses of existing camp collaborators by
     * - fetching camp collaborators (invited collaborators through inviteEmail)
     * - fetching profiles by user.collaborations.camp (established collaborators)
     */
    async loadCampEmails() {
      const [collaborations, profiles] = await Promise.all([
        this.api.reload(this.camp.campCollaborations()),
        this.api.get().profiles({ 'user.collaborations.camp': this.camp._meta.self })
          ._meta.load,
      ])

      return [
        ...collaborations.items.map((collaboration) => collaboration.inviteEmail),
        ...profiles.items.map((profile) => profile.email),
      ].filter(Boolean)
    },

    redirectToAuthorization(error) {
      const provider = this.camp.hitobitoProvider
      if (isAccessTokenInvalidError(error) && !hasAttemptedAuthorization(provider)) {
        const callback = this.$router.resolve(
          campRoute(this.camp, 'hitobitoInvite')
        ).fullPath
        redirectToHitobitoAuthorization(provider, callback)
        return true
      }
      return false
    },

    handleLoadError(error) {
      switch (error?.response?.status) {
        case 403:
          this.loadError = this.$t('views.camp.hitobitoInvite.errors.noAccess', {
            provider: this.provider,
          })
          break
        case 404:
          this.loadError = this.$t('views.camp.hitobitoInvite.errors.notFound', {
            provider: this.provider,
          })
          break
        default:
          this.loadError = this.$t('views.camp.hitobitoInvite.errors.loading')
      }
    },

    async invite() {
      this.isSaving = true
      this.inviteError = null

      const emails = this.newParticipants.map((participant) => participant.email)
      try {
        const campCollaborationsUri = await this.api.href(
          this.api.get(),
          'campCollaborations'
        )
        const results = await Promise.allSettled(
          emails.map((email) =>
            this.api.post(campCollaborationsUri, {
              camp: this.camp._meta.self,
              inviteEmail: email,
              role: 'member',
            })
          )
        )

        const failed = emails.filter(
          (email, index) => results[index].status === 'rejected'
        )
        if (failed.length > 0) {
          this.campEmails = await this.loadCampEmails()
          this.inviteError = this.$t('views.camp.hitobitoInvite.errors.invite', {
            emails: failed.join(', '),
          })
          return
        }

        await this.api.reload(this.camp.campCollaborations())
        await this.$router.push(this.collaboratorsRoute)
      } catch {
        this.inviteError = this.$t('views.camp.hitobitoInvite.errors.invite', {
          emails: emails.join(', '),
        })
      } finally {
        this.isSaving = false
      }
    },
  },
}
</script>
