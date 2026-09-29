<template>
  <v-container fluid>
    <content-card :title="title" max-width="800" toolbar>
      <template v-if="loading">
        <v-card-text>
          <v-skeleton-loader type="text, list-item-avatar-two-line@3" class="px-0" />
        </v-card-text>
        <v-divider />
        <ContentActions>
          <ButtonCancel class="ml-auto" :to="collaboratorsRoute" />
        </ContentActions>
      </template>

      <template v-else-if="loadError">
        <v-card-text>
          <v-alert type="error" variant="tonal" data-testid="hitobito-invite-error">
            {{ loadError }}
          </v-alert>
        </v-card-text>
        <v-divider />
        <ContentActions>
          <ButtonBack visible-label class="mr-auto" />
        </ContentActions>
      </template>

      <template v-else-if="newParticipants.length === 0">
        <v-card-text class="text-center py-8" data-testid="hitobito-invite-nothing-to-do">
          <p>{{ $t('views.camp.hitobitoInvite.allInvited', { provider }) }}</p>
        </v-card-text>
        <v-divider />
        <ContentActions>
          <ButtonBack visible-label class="mr-auto" />
        </ContentActions>
      </template>

      <template v-else>
        <v-card-text>
          <v-alert
            v-if="inviteError"
            type="error"
            variant="tonal"
            class="mb-4"
            data-testid="hitobito-invite-error"
          >
            {{ inviteError }}
          </v-alert>

          <p class="mb-4">{{ $t('views.camp.hitobitoInvite.intro', { provider }) }}</p>

          <h3 class="mb-1">{{ $t('views.camp.hitobitoInvite.newParticipants') }}</h3>
          <p class="text-body-2 text-medium-emphasis mb-2">
            {{ $t('views.camp.hitobitoInvite.newParticipantsDescription') }}
          </p>
          <HitobitoParticipantList
            :participants="newParticipants"
            avatar-color="blue-lighten-4"
            icon-color="blue-darken-2"
            icon="mdi-account-plus"
            class="mb-4"
            data-testid="hitobito-invite-new-participants"
          />

          <template v-if="existingParticipants.length > 0">
            <h3 class="mb-1">
              {{ $t('views.camp.hitobitoInvite.existingParticipants') }}
            </h3>
            <p class="text-body-2 text-medium-emphasis mb-2">
              {{ $t('views.camp.hitobitoInvite.existingParticipantsDescription') }}
            </p>
            <HitobitoParticipantList
              :participants="existingParticipants"
              avatar-color="grey-lighten-3"
              icon-color="grey-darken-1"
              icon="mdi-account-check"
              data-testid="hitobito-invite-existing-participants"
            />
          </template>
        </v-card-text>
        <v-divider />
        <ContentActions>
          <ButtonCancel class="ml-auto" :disabled="isSaving" :to="collaboratorsRoute" />
          <v-btn
            color="success"
            variant="elevated"
            prepend-icon="mdi-email-fast"
            :loading="isSaving"
            data-testid="invite-button"
            @click="invite"
          >
            {{ $t('views.camp.hitobitoInvite.invite') }}
          </v-btn>
        </ContentActions>
      </template>
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
