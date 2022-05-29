<!--
Show all activity schedule entries of a single period.
-->

<template>
  <content-card :title="$tc('views.camp.picasso.title')" toolbar>
    <template #title-actions>
      <period-switcher v-if="$vuetify.breakpoint.xsOnly" :period="period" />
      <v-spacer />
      <v-tooltip :disabled="isContributor" bottom>
        <template #activator="{ on }">
          <v-icon v-if="editMode"
                  small color="grey"
                  v-on="on">
            mdi-lock-open
          </v-icon>
          <v-icon v-else
                  small color="grey"
                  v-on="on">
            mdi-lock
          </v-icon>
        </template>
        <span>{{ $tc('views.camp.picasso.guestsCannotEdit') }}</span>
      </v-tooltip>
      <v-menu offset-y>
        <template #activator="{ on, attrs }">
          <v-btn icon
                 v-bind="attrs"
                 v-on="on">
            <v-icon>mdi-dots-horizontal</v-icon>
          </v-btn>
        </template>
        <v-list class="py-0">
          <v-list-item @click="editMode = !editMode">
            <v-list-item-icon>
              <v-icon v-if="editMode">mdi-lock</v-icon>
              <v-icon v-else>mdi-lock-open</v-icon>
            </v-list-item-icon>
            <v-list-item-title>
              {{ editMode ? 'Sperren' : 'Entsperren' }}
            </v-list-item-title>
          </v-list-item>
          <v-divider />
          <DownloadNuxtPdf :config="printConfig()" @error="showPrintError" />
          <DownloadReactPdf :config="printConfig()" @error="showPrintError" />
        </v-list>
      </v-menu>
    </template>
    <schedule-entries :period="period" :show-button="isContributor">
      <template #default="slotProps">
        <template v-if="slotProps.loading">
          <v-skeleton-loader type="table" />
        </template>
        <template v-else>
          <picasso
            :schedule-entries="slotProps.scheduleEntries"
            :period="period()"
            :start="Date.parse(period().start)"
            :end="Date.parse(period().end)"
            :editable="editMode"
            @newEntry="slotProps.on.newEntry" />
        </template>
      </template>
    </schedule-entries>
    <v-snackbar v-model="showError" app :timeout="10000">
      {{ error ? error.label : null }}
      <template #action="{ attrs }">
        <v-btn color="red"
               text
               v-bind="attrs"
               @click="showError = null">
          {{ $tc('global.button.close') }}
        </v-btn>
      </template>
    </v-snackbar>
  </content-card>
</template>
<script>
import { campRoleMixin } from '@/mixins/campRoleMixin'
import ContentCard from '@/components/layout/ContentCard.vue'
import Picasso from '@/components/program/picasso/Picasso.vue'
import ScheduleEntries from '@/components/program/ScheduleEntries.vue'
import PeriodSwitcher from '@/components/program/PeriodSwitcher.vue'
import DownloadNuxtPdf from '@/components/print/print-nuxt/DownloadNuxtPdfListItem.vue'
import DownloadReactPdf from '@/components/print/print-react/DownloadReactPdfListItem.vue'

export default {
  name: 'CampProgram',
  components: {
    DownloadNuxtPdf,
    DownloadReactPdf,
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
      showError: null,
      error: null,
      nuxtIsPrinting: false,
      reactIsPrinting: false
    }
  },
  computed: {
    camp () {
      return this.period().camp()
    }
  },
  methods: {
    showPrintError (event) {
      this.error = event
      this.showError = true
    },
    printConfig () {
      return {
        camp: this.period().camp()._meta.self,
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
