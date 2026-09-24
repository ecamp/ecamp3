<template>
  <v-container fluid>
    <content-card :title="title" max-width="800" toolbar>
      <v-card-text v-if="loading">
        <v-skeleton-loader type="table" class="px-0" />
      </v-card-text>

      <v-card-text v-else>
        <v-alert
          v-if="error"
          type="error"
          variant="tonal"
          class="mb-4"
          data-testid="hitobito-sync-error"
        >
          {{ error }}
        </v-alert>

        <template v-if="!loadError">
          <p
            v-if="!sync.hasChanges"
            class="text-center py-8"
            data-testid="hitobito-sync-nothing-to-do"
          >
            {{ $t('views.camp.hitobitoSync.noChanges') }}
          </p>

          <template v-else>
            <p class="mb-4">{{ $t('views.camp.hitobitoSync.intro', { provider }) }}</p>

            <HitobitoSyncDiffTable
              :rows="campRows"
              :provider="provider"
              data-testid="hitobito-sync-camp"
            />
          </template>
        </template>
      </v-card-text>

      <v-divider />
      <ContentActions>
        <template v-if="canSynchronize">
          <ButtonCancel class="ml-auto" :disabled="isSaving" :to="infoRoute" />
          <v-btn
            color="success"
            variant="elevated"
            prepend-icon="mdi-sync"
            :loading="isSaving"
            data-testid="sync-button"
            @click="synchronize"
          >
            {{ $t('views.camp.hitobitoSync.sync') }}
          </v-btn>
        </template>
        <ButtonBack v-else visible-label class="mr-auto" />
      </ContentActions>
    </content-card>
  </v-container>
</template>

<script>
import ButtonBack from '@/components/buttons/ButtonBack.vue'
import ButtonCancel from '@/components/buttons/ButtonCancel.vue'
import ContentActions from '@/components/layout/ContentActions.vue'
import ContentCard from '@/components/layout/ContentCard.vue'
import HitobitoSyncDiffTable from '@/components/campHitobitoSync/HitobitoSyncDiffTable.vue'
import { buildCampSync, campPatch } from '@/components/campHitobitoSync/campSync.js'
import { adminRoute, campRoute } from '@/router.js'
import {
  clearAuthorizationAttempt,
  hasAttemptedAuthorization,
  hitobitoEventsUri,
  isAccessTokenInvalidError,
  providerNameKey,
  redirectToHitobitoAuthorization,
} from '@/plugins/hitobito.js'

const FIELD_LABELS = {
  title: 'components.campImport.hitobitoEventSummary.fields.title',
  motto: 'components.campImport.hitobitoEventSummary.fields.motto',
  addressName: 'components.campImport.hitobitoEventSummary.fields.address',
}

export default {
  name: 'CampHitobitoSync',
  components: {
    ButtonBack,
    ButtonCancel,
    ContentActions,
    ContentCard,
    HitobitoSyncDiffTable,
  },
  props: {
    camp: { type: Object, required: true },
  },
  data() {
    return {
      loading: true,
      isSaving: false,
      loadError: null,
      syncError: null,
      sync: { campRows: [], hasChanges: false },
    }
  },
  head() {
    return { title: this.title }
  },
  computed: {
    title() {
      return this.$t('views.camp.hitobitoSync.title', { provider: this.provider })
    },
    provider() {
      return this.$t(providerNameKey(this.camp.hitobitoProvider))
    },
    error() {
      return this.loadError ?? this.syncError
    },
    canSynchronize() {
      return !this.loading && !this.loadError && this.sync.hasChanges
    },
    infoRoute() {
      return adminRoute(this.camp, 'info')
    },
    campRows() {
      return this.sync.campRows.map((row) => ({
        ...row,
        label: this.$t(FIELD_LABELS[row.field]),
      }))
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
        const event = await this.api.reload(
          hitobitoEventsUri(this.camp.hitobitoProvider, this.camp.hitobitoEventId)
        )
        this.sync = buildCampSync(this.camp, event, this.camp.hitobitoProvider)
        clearAuthorizationAttempt(this.camp.hitobitoProvider)
      } catch (error) {
        if (this.redirectToAuthorization(error)) {
          return
        }
        this.handleLoadError(error)
      }
      this.loading = false
    },

    redirectToAuthorization(error) {
      const provider = this.camp.hitobitoProvider
      if (isAccessTokenInvalidError(error) && !hasAttemptedAuthorization(provider)) {
        const callback = this.$router.resolve(
          campRoute(this.camp, 'hitobitoSync')
        ).fullPath
        redirectToHitobitoAuthorization(provider, callback)
        return true
      }
      return false
    },

    handleLoadError(error) {
      switch (error?.response?.status) {
        case 403:
          this.loadError = this.$t('views.camp.hitobitoSync.errors.noAccess', {
            provider: this.provider,
          })
          break
        case 404:
          this.loadError = this.$t('views.camp.hitobitoSync.errors.notFound', {
            provider: this.provider,
          })
          break
        default:
          this.loadError = this.$t('views.camp.hitobitoSync.errors.loading', {
            provider: this.provider,
          })
      }
    },

    async synchronize() {
      this.isSaving = true
      this.syncError = null

      try {
        await this.api.patch(this.camp._meta.self, campPatch(this.sync.campRows))
        await this.$router.push(this.infoRoute)
      } catch (error) {
        this.syncError =
          error?.response?.status === 403
            ? this.$t('views.camp.hitobitoSync.errors.forbidden')
            : this.$t('views.camp.hitobitoSync.errors.sync')
        await this.load()
      } finally {
        this.isSaving = false
      }
    },
  },
}
</script>
