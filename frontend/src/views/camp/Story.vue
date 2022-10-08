<!--
Admin screen of a camp: Displays details & periods of a single camp and allows to edit them.
-->

<template>
  <content-card :title="$tc('views.camp.story.title')" toolbar>
    <template #title-actions>
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
          <DownloadNuxtPdf :config="printConfig" @error="showPrintError" />
          <DownloadReactPdf :config="printConfig" @error="showPrintError" />
        </v-list>
      </v-menu>
    </template>
    <v-expansion-panels
      v-if="camp().periods().items.length > 1"
      v-model="openPeriods"
      accordion
      flat
      multiple
    >
      <story-period
        v-for="period in camp().periods().items"
        :key="period._meta.self"
        :editing="editing"
        :period="period"
      />
    </v-expansion-panels>
    <div v-else-if="camp().periods().items.length === 1" class="px-4">
      <story-day
        v-for="day in camp().periods().items[0].days().items"
        :key="day._meta.self"
        :day="day"
        :editing="editing"
        class="my-4"
      />
    </div>

    <v-snackbar v-model="showError" app :timeout="10000">
      {{ error ? error.label : null }}
      <template #action="{ attrs }">
        <v-btn color="red" text v-bind="attrs" @click="showError = null">
          {{ $tc('global.button.close') }}
        </v-btn>
      </template>
    </v-snackbar>
  </content-card>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import StoryPeriod from '@/components/story/StoryPeriod.vue'
import { campRoleMixin } from '@/mixins/campRoleMixin'
import StoryDay from '@/components/story/StoryDay.vue'
import DownloadNuxtPdf from '@/components/print/print-nuxt/DownloadNuxtPdfListItem.vue'
import DownloadReactPdf from '@/components/print/print-react/DownloadReactPdfListItem.vue'
import LockIcon from '@/components/generic/LockIcon.vue'
import LockUnlockListItem from '@/components/generic/LockUnlockListItem.vue'

export default {
  name: 'Story',
  components: {
    StoryDay,
    StoryPeriod,
    ContentCard,
    DownloadReactPdf,
    DownloadNuxtPdf,
    LockIcon,
    LockUnlockListItem,
  },
  mixins: [campRoleMixin],
  props: {
    camp: { type: Function, required: true },
  },
  data() {
    return {
      editing: false,
      openPeriods: [],
      showError: null,
      error: null,
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
  mounted() {
    this.camp()
      .periods()
      ._meta.load.then((periods) => {
        this.openPeriods = periods.items
          .map((period, idx) => (Date.parse(period.end) >= new Date() ? idx : null))
          .filter((idx) => idx !== null)
      })
  },
  methods: {
    showPrintError(event) {
      this.error = event
      this.showError = true
    },
  },
}
</script>

<style lang="scss" scoped>
.ec-story-editable ::v-deep .v-input--selection-controls {
  margin-top: 0;
}
</style>
