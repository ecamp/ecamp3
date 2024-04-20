<template>
  <content-card :title="$tc('views.camp.story.title')" toolbar>
    <template #title-actions>
      <period-switcher :period="period" :route-name="'camp/period/story'" />
      <v-spacer />
      <LockButton
        v-model="editMode"
        :disabled-for-guest="!isContributor"
        @click="editMode = !editMode"
      />
      <v-menu offset-y>
        <template #activator="{ on, attrs }">
          <v-btn class="ml-auto" text icon v-bind="attrs" v-on="on">
            <v-icon>mdi-dots-vertical</v-icon>
          </v-btn>
        </template>
        <v-list class="py-0">
          <LockUnlockListItem
            v-model="editMode"
            :disabled="!isContributor"
            @click="editMode = !editMode"
          />
          <v-divider />
          <DownloadNuxtPdf :config="printConfig" />
          <DownloadClientPdf :config="printConfig" />
        </v-list>
      </v-menu>
    </template>
    <story-period :edit-mode="editMode" :period="period" />
  </content-card>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import StoryPeriod from '@/components/story/StoryPeriod.vue'
import { campRoleMixin } from '@/mixins/campRoleMixin'
import DownloadNuxtPdf from '@/components/print/print-nuxt/DownloadNuxtPdfListItem.vue'
import DownloadClientPdf from '@/components/print/print-client/DownloadClientPdfListItem.vue'
import LockButton from '@/components/generic/LockButton.vue'
import LockUnlockListItem from '@/components/generic/LockUnlockListItem.vue'

export default {
  name: 'Story',
  components: {
    StoryPeriod,
    ContentCard,
    DownloadClientPdf,
    DownloadNuxtPdf,
    LockButton,
    LockUnlockListItem,
  },
  mixins: [campRoleMixin],
  props: {
    period: { type: Object, required: true },
    camp: { type: Object, required: true },
  },
  computed: {
    printConfig() {
      return {
        camp: this.camp._meta.self,
        language: this.$store.state.lang.language,
        documentName: this.camp.title + '-StorySummary.pdf',
        contents: [
          {
            type: 'Story',
            options: {
              periods: [this.period._meta.self],
            },
          },
        ],
      }
    },
    editMode: {
      get() {
        return this.$store.getters.getStoryContextEditMode(this.camp._meta.self)
      },
      set(value) {
        this.$store.commit('setStoryContextEditMode', {
          campUri: this.camp._meta.self,
          editMode: value,
        })
      },
    },
  },
}
</script>

<style lang="scss" scoped>
.ec-story-editable:deep(.v-input--selection-controls) {
  margin-top: 0;
}
</style>
