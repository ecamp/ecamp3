<template>
  <content-card :title="$t('views.camp.story.title')" toolbar>
    <template #title-actions>
      <PeriodSwitcher :period="period" :route-name="'camp/period/story'" />
      <v-spacer />
      <LockButton
        v-if="!isOutsider"
        v-model="editMode"
        :disabled-for-guest="!isContributor"
        @click="editMode = !editMode"
      />
      <v-menu offset-y>
        <template #activator="{ props }">
          <v-btn class="ml-auto" icon v-bind="props" variant="text">
            <v-icon>mdi-dots-vertical</v-icon>
          </v-btn>
        </template>
        <v-list class="py-0">
          <LockUnlockListItem
            v-if="!isOutsider"
            v-model="editMode"
            :disabled="!isContributor"
            @click="editMode = !editMode"
          />
          <v-divider />
          <DownloadPdfListItem :config="printConfig" />
        </v-list>
      </v-menu>
    </template>
    <StoryPeriod :edit-mode="editMode" :period="period" />
  </content-card>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import StoryPeriod from '@/components/story/StoryPeriod.vue'
import { campRoleMixin } from '@/mixins/campRoleMixin'
import DownloadPdfListItem from '@/components/print/generator/DownloadPdfListItem.vue'
import LockButton from '@/components/generic/LockButton.vue'
import LockUnlockListItem from '@/components/generic/LockUnlockListItem.vue'
import PeriodSwitcher from '@/components/program/PeriodSwitcher.vue'
import campShortTitle from '@/helpers/campShortTitle.js'

export default {
  name: 'Story',
  components: {
    PeriodSwitcher,
    StoryPeriod,
    ContentCard,
    DownloadPdfListItem,
    LockButton,
    LockUnlockListItem,
  },
  mixins: [campRoleMixin],
  props: {
    period: { type: Object, required: true },
    camp: { type: Object, required: true },
  },
  head() {
    return {
      title: this.$t('views.camp.story.title'),
    }
  },
  computed: {
    printConfig() {
      return {
        camp: this.camp._meta.self,
        language: this.$store.state.lang.language,
        documentName: campShortTitle(this.camp) + '-' + this.$t('views.camp.story.title'),
        options: { pageNumbers: false },
        contents: [
          {
            type: 'Story',
            options: {
              contentType: 'Storycontext',
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
