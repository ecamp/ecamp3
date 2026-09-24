<template>
  <v-stepper :model-value="step" flat>
    <v-stepper-header class="elevation-0">
      <v-spacer v-if="$vuetify.display.smAndUp" class="w-100" />
      <v-stepper-item :value="1" :complete="step > 1" class="px-4" color="primary">
        {{ $t('components.campImport.campImport.steps.event') }}
      </v-stepper-item>
      <v-divider class="mx-n2" />
      <v-stepper-item :value="2" :complete="step > 2" class="px-4" color="primary">
        {{ $t('components.campImport.campImport.steps.template') }}
      </v-stepper-item>
      <v-spacer v-if="$vuetify.display.smAndUp" class="w-100" />
    </v-stepper-header>
    <v-divider />
    <v-stepper-window class="ma-0">
      <v-stepper-window-item :value="1">
        <v-skeleton-loader v-if="isLoadingEvents" type="article" class="pa-4" />
        <CampImportStep1
          v-else
          v-model="selectedEventUri"
          :camp="camp"
          :events="events"
          :conflict-error="conflictError"
          :hide-search="hasFixedEvent"
          :is-loading-event="isLoadingEvent"
          :is-saving="isSaving"
          @next-step="step++"
        />
      </v-stepper-window-item>
      <v-stepper-window-item :value="2">
        <CampCreateStep2
          v-if="camp"
          :camp="camp"
          :is-saving="isSaving"
          :server-error="serverError"
          @create-camp="createCamp"
          @previous-step="step--"
        />
      </v-stepper-window-item>
    </v-stepper-window>
  </v-stepper>
</template>

<script>
import CampCreateStep2 from '@/components/campCreate/CampCreateStep2.vue'
import CampImportStep1 from '@/components/campImport/CampImportStep1.vue'
import { eventToCamp } from '@/components/campImport/eventToCamp.js'
import { campRoute } from '@/router.js'
import {
  clearAuthorizationAttempt,
  hasAttemptedAuthorization,
  hitobitoEventsUri,
  isAccessTokenInvalidError,
  providerNameKey,
  redirectToHitobitoAuthorization,
} from '@/plugins/hitobito.js'

export default {
  name: 'CampImport',
  components: { CampCreateStep2, CampImportStep1 },
  props: {
    provider: { type: String, required: true },
    eventId: { type: String, default: null },
  },
  data() {
    return {
      step: 1,
      events: [],
      selectedEventUri: null,
      camp: null,
      isLoadingEvents: false,
      isLoadingEvent: false,
      isSaving: false,
      serverError: null,
      conflictError: null,
    }
  },
  computed: {
    hasFixedEvent() {
      return this.eventId !== null
    },
    campsUrl() {
      return this.api.get().camps()._meta.self
    },
  },
  watch: {
    selectedEventUri(uri) {
      this.serverError = null
      this.conflictError = null
      if (uri) this.loadEvent(uri)
      else this.camp = null
    },
  },
  async mounted() {
    if (this.hasFixedEvent) {
      await this.loadEvent(hitobitoEventsUri(this.provider, this.eventId))
    } else {
      await this.loadEvents()
    }
  },
  methods: {
    async loadEvents() {
      this.isLoadingEvents = true
      try {
        const collection = await this.api.reload(hitobitoEventsUri(this.provider))
        this.events = collection.items
        clearAuthorizationAttempt(this.provider)
      } catch (error) {
        this.handleLoadError(error)
      } finally {
        this.isLoadingEvents = false
      }
    },
    async loadEvent(uri) {
      this.isLoadingEvent = true
      this.camp = null
      try {
        // reload is required: items of the collection (/events) are cached under the same URI, but
        // carry only id and name. /events/<id> additionally returns motto, location and dates
        const event = await this.api.reload(uri)
        this.camp = eventToCamp(event, this.provider)
        clearAuthorizationAttempt(this.provider)
      } catch (error) {
        this.handleLoadError(error)
      } finally {
        this.isLoadingEvent = false
      }
    },
    handleLoadError(error) {
      if (isAccessTokenInvalidError(error) && !hasAttemptedAuthorization(this.provider)) {
        redirectToHitobitoAuthorization(this.provider, this.$route.fullPath)
        return
      }

      const status = error?.response?.status
      let message
      switch (status) {
        case 403:
          message = this.$t('components.campImport.errors.noAccess')
          break
        case 404:
          message = this.$t('components.campImport.errors.notFound')
          break
        default:
          message = this.$t('components.campImport.errors.generic')
      }

      this.$store.commit('addSnackbarMessage', message)
      this.$router.push({ name: 'camps' })
    },
    async createCamp() {
      this.isSaving = true
      this.serverError = null
      this.conflictError = null

      try {
        const camp = await this.api.post(this.campsUrl, this.camp)
        await this.$router.push(campRoute(camp, 'admin/info'))
        this.api.reload(this.campsUrl)
      } catch (error) {
        if (error?.response?.status === 409) {
          const message = this.$t('components.campImport.errors.alreadyExists', {
            provider: this.$t(providerNameKey(this.provider)),
          })
          if (this.hasFixedEvent) {
            this.$store.commit('addSnackbarMessage', message)
          } else {
            this.conflictError = message
            this.step = 1
          }
        } else {
          this.serverError = error
        }
      }

      this.isSaving = false
    },
  },
}
</script>
