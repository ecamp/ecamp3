<template>
  <v-container fluid>
    <content-card max-width="800" :title="title" toolbar>
      <CampImport :provider="provider" :event-id="eventId" />
    </content-card>
  </v-container>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import CampImport from '@/components/campImport/CampImport.vue'
import { providerNameKey } from '@/plugins/hitobito.js'

export default {
  name: 'CampImportView',
  components: {
    CampImport,
    ContentCard,
  },
  props: {
    provider: { type: String, required: true },
    eventId: { type: String, default: null },
  },
  head() {
    return {
      title: this.title,
    }
  },
  computed: {
    title() {
      return this.$t('views.campImport.title', {
        provider: this.$t(providerNameKey(this.provider)),
      })
    },
  },
}
</script>
