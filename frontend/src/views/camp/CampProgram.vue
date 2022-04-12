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
          <div v-on="on">
            <v-icon v-if="editMode" small color="grey">mdi-lock-open</v-icon>
            <v-icon v-else small color="grey">mdi-lock</v-icon>
          </div>
        </template>
        <span>{{ $tc('views.camp.picasso.guestsCannotEdit') }}</span>
      </v-tooltip>
      <v-menu>
        <template #activator="{ on, attrs }">
          <div
            v-bind="attrs"
            v-on="on">
            <v-btn icon>
              <v-icon>mdi-dots-vertical</v-icon>
            </v-btn>
          </div>
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
          <v-list-item @click="print">
            <v-list-item-icon>
              <v-icon v-if="isPrinting">mdi-timer-sand</v-icon>
              <v-icon v-else>mdi-nuxt</v-icon>
            </v-list-item-icon>
            <v-list-item-title>PDF herunterladen</v-list-item-title>
          </v-list-item>
          <local-pdf-download-button :config="printConfig()" />
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
import LocalPdfDownloadButton from '@/components/print/print-react/LocalPdfDownloadButton.vue'

import axios from 'axios'

const PRINT_SERVER = window.environment.PRINT_SERVER

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
    period: {
      type: Function,
      required: true
    }
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
    },
    async print () {
      console.log('Printing now')

      const title = 'PrintedByBrowserless.pdf'

      this.isPrinting = true

      try {
        const response = await axios({
          method: 'get',
          url: `${PRINT_SERVER}/server/pdf?period=${this.period()._meta.self}`,
          responseType: 'arraybuffer',
          withCredentials: true,
          headers: {
            'Cache-Control': 'no-cache',
            Pragma: 'no-cache',
            Expires: '0'
          }
        })
        this.forceFileDownload(response, title)
      } catch (error) {
        console.log(error)
      } finally {
        this.isPrinting = false
      }
    },
    forceFileDownload (response, title) {
      const url = window.URL.createObjectURL(new Blob([response.data]))
      const link = document.createElement('a')
      link.href = url
      link.setAttribute('download', title)
      document.body.appendChild(link)
      link.click()
    }
  }
}
</script>
