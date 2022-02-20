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

      <v-btn
        class="ml-5"
        color="primary"
        :loading="isPrinting"
        outlined
        @click="print">
        <v-icon>mdi-printer</v-icon>
      </v-btn>
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

import axios from 'axios'

const PRINT_SERVER = window.environment?.PRINT_SERVER

export default {
  name: 'CampProgram',
  components: {
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
    async print () {
      console.log('Printing now')

      const title = 'PrintedByBrowserless.pdf'

      this.isPrinting = true

      // this strips hostname from the self-link
      // TODO: can be removed after implementation of https://github.com/ecamp/hal-json-vuex/issues/245
      const periodURI = (new URL(this.period()._meta.self)).pathname

      try {
        const response = await axios({
          method: 'get',
          url: `${PRINT_SERVER}/pdf/test4?period=${periodURI}`,
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
