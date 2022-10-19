<template>
  <content-card :title="$tc('views.camp.story.title')" toolbar>
    <template #title-actions>
      <period-switcher :period="period" :route-name="'camp/period/story'" />
      <v-spacer />
      <LockIcon v-model="editing" :hide-tooltip="isContributor" />
      <v-menu offset-y>
        <template #activator="{ on, attrs }">
          <v-btn class="ml-auto" text icon v-bind="attrs" v-on="on">
            <v-icon>mdi-dots-vertical</v-icon>
          </v-btn>
        </template>
        <v-list class="py-0">
          <LockUnlockListItem
            v-model="editing"
            :disabled="!isContributor"
            @click="editing = !editing"
          />
          <v-divider />
          <DownloadNuxtPdf :config="printConfig" />
          <DownloadReactPdf :config="printConfig" />
        </v-list>
      </v-menu>
    </template>
    <story-period :editing="editing" :period="period()" />
  </content-card>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import StoryPeriod from '@/components/story/StoryPeriod.vue'
import { campRoleMixin } from '@/mixins/campRoleMixin'
import DownloadNuxtPdf from '@/components/print/print-nuxt/DownloadNuxtPdfListItem.vue'
import DownloadReactPdf from '@/components/print/print-react/DownloadReactPdfListItem.vue'
import LockIcon from '@/components/generic/LockIcon.vue'
import LockUnlockListItem from '@/components/generic/LockUnlockListItem.vue'

export default {
  name: 'Story',
  components: {
    StoryPeriod,
    ContentCard,
    DownloadReactPdf,
    DownloadNuxtPdf,
    LockIcon,
    LockUnlockListItem,
  },
  mixins: [campRoleMixin],
  props: {
    period: { type: Function, required: true },
    camp: { type: Function, required: true },
  },
  data() {
    return {
      editing: false,
    }
  },
  computed: {
    printConfig() {
      return {
        camp: this.camp()._meta.self,
        language: this.$store.state.lang.language,
        documentName: this.camp().title + '-StorySummary.pdf',
        contents: [
          {
            type: 'Story',
            options: {
              periods: this.camp()
                .periods()
                .items.map((period) => period._meta.self),
            },
          },
        ],
      }
    },
  },
}
</script>

<style lang="scss" scoped>
.ec-story-editable ::v-deep .v-input--selection-controls {
  margin-top: 0;
}
</style>
