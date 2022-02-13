<!--
Show all activity schedule entries of a single period.
-->

<template>
  <content-card :title="$tc('views.camp.picasso.title')" toolbar>
    <template #title-actions>
      <period-switcher v-if="$vuetify.breakpoint.xsOnly" :period="period" />
      <v-tooltip :disabled="isContributor" bottom>
        <template #activator="{ on, attrs }">
          <div
            v-bind="attrs"
            v-on="on">
            <e-switch
              v-model="editMode"
              :disabled="!isContributor"
              :label="$tc('views.camp.picasso.editMode')" />
          </div>
        </template>
        <span>{{ $tc('views.camp.picasso.guestsCannotEdit') }}</span>
      </v-tooltip>
      <local-pdf-download-button :config="printConfig" />
    </template>
    <schedule-entries :period="period" :show-button="isContributor">
      <template #default="slotProps">
        <template v-if="slotProps.loading">
          <v-skeleton-loader type="table" />
        </template>
        <template v-else>
          <picasso
            class="mx-2 ma-sm-0 pa-sm-2"
            :schedule-entries="slotProps.scheduleEntries"
            :period="period()"
            :start="Date.parse(period().start)"
            :end="Date.parse(period().end)"
            :editable="editMode"
            @changePlaceholder="slotProps.on.changePlaceholder"
            @newEntry="slotProps.on.newEntry" />
        </template>
      </template>
    </schedule-entries>
  </content-card>
</template>
<script>
import { campRoleMixin } from '@/mixins/campRoleMixin'
import ContentCard from '@/components/layout/ContentCard.vue'
import Picasso from '@/components/camp/picasso/Picasso.vue'
import ScheduleEntries from '@/components/scheduleEntry/ScheduleEntries.vue'
import PeriodSwitcher from '@/components/camp/PeriodSwitcher.vue'
import LocalPdfDownloadButton from '../../components/print/LocalPdfDownloadButton.vue'

export default {
  name: 'CampProgram',
  components: {
    LocalPdfDownloadButton,
    PeriodSwitcher,
    ContentCard,
    Picasso,
    ScheduleEntries
  },
  mixins: [campRoleMixin],
  props: {
    period: { type: Function, required: true }
  },
  data () {
    return {
      editMode: false,
      printConfig: {
        showFrontpage: false,
        showToc: false,
        showPicasso: true,
        showDailySummary: false,
        showStoryline: false,
        showActivities: false,
        camp: this.period().camp.bind(this)
      }
    }
  },
  computed: {
    camp () {
      return this.period().camp()
    }
  }
}
</script>
