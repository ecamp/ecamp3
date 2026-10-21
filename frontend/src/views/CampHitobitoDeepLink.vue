<template>
  <v-container fluid class="fill-height justify-center">
    <v-progress-circular indeterminate color="primary" size="64" />
  </v-container>
</template>

<script>
import { campRoute } from '@/router.js'
import {
  HITOBITO_ERROR_TYPES,
  clearAuthorizationAttempt,
  hasAttemptedAuthorization,
  hitobitoEventCampUri,
  isAccessTokenInvalidError,
  redirectToHitobitoAuthorization,
} from '@/plugins/hitobito.js'

export default {
  name: 'CampHitobitoDeepLink',
  props: {
    provider: { type: String, required: true },
    eventId: { type: String, required: true },
  },
  head() {
    return {
      title: this.$t('views.campHitobitoDeepLink.title'),
    }
  },
  async mounted() {
    await this.resolveCamp()
  },
  methods: {
    async resolveCamp() {
      try {
        const deepLink = await this.api.reload(
          hitobitoEventCampUri(this.provider, this.eventId)
        )
        const camp = await deepLink.camp()._meta.load
        clearAuthorizationAttempt(this.provider)
        await this.$router.replace(campRoute(camp))
      } catch (error) {
        await this.handleError(error)
      }
    },
    async handleError(error) {
      if (isAccessTokenInvalidError(error) && !hasAttemptedAuthorization(this.provider)) {
        redirectToHitobitoAuthorization(this.provider, this.$route.fullPath)
        return
      }

      switch (error?.response?.data?.type) {
        case HITOBITO_ERROR_TYPES.campNotFound:
          await this.$router.replace({
            name: 'camps/import',
            params: { provider: this.provider },
            query: { eventId: this.eventId },
          })
          return
        case HITOBITO_ERROR_TYPES.eventNotFound:
          await this.$router.replace({
            name: 'PageNotFound',
            params: [this.$route.fullPath, ''],
          })
          return
        case HITOBITO_ERROR_TYPES.campForbidden:
          await this.redirectToCamps('campForbidden')
          return
        case HITOBITO_ERROR_TYPES.eventForbidden:
          await this.redirectToCamps('eventForbidden')
          return
        default:
          await this.redirectToCamps('generic')
      }
    },
    async redirectToCamps(errorKey) {
      this.$store.commit(
        'addSnackbarMessage',
        this.$t(`views.campHitobitoDeepLink.errors.${errorKey}`)
      )
      await this.$router.replace({ name: 'camps' })
    },
  },
}
</script>
