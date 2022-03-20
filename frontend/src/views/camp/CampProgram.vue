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

      <pdf-download-button-nuxt :config="printConfig()" />

      <pdf-download-button-react :config="printConfig()" />
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
            @newEntry="slotProps.on.newEntry" />
        </template>
      </template>
    </schedule-entries>
  </content-card>
</template>
<script>
import { campRoleMixin } from '@/mixins/campRoleMixin'
import ContentCard from '@/components/layout/ContentCard.vue'
import Picasso from '@/components/program/picasso/Picasso.vue'
import ScheduleEntries from '@/components/program/ScheduleEntries.vue'
import PeriodSwitcher from '@/components/program/PeriodSwitcher.vue'
import PdfDownloadButtonReact from '@/components/print/print-react/PdfDownloadButtonReact.vue'
import PdfDownloadButtonNuxt from '@/components/print/print-nuxt/PdfDownloadButtonNuxt.vue'

export default {
  name: 'CampProgram',
  components: {
    PdfDownloadButtonReact,
    PdfDownloadButtonNuxt,
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
      isPrinting: false
    }
  },
  computed: {
    camp () {
      return this.period().camp()
    }
  },
  methods: {
    printConfig () {
      return {
        camp: () => this.period().camp(),
        language: this.$store.state.lang.language,
        documentName: this.camp.title + '-picasso.pdf',
        contents: [
          {
            type: 'Picasso',
            options: {
              periods: [
                this.period()._meta.self
              ],
              orientation: 'L'
            }
          }
        ]
      }
    }
  }
}
</script>
