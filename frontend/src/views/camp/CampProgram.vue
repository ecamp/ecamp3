<!--
Show all activity schedule entries of a single period.
-->

<template>
  <content-card :title="$tc('views.camp.picasso.title')" toolbar>
    <template #title-actions>
      <period-switcher :period="period" />
      <v-spacer />
      <LockIcon
        v-model="editMode"
        :hide-tooltip="isContributor"
        :message="$tc('views.camp.picasso.guestsCannotEdit')"
      />
      <v-menu offset-y>
        <template #activator="{ on, attrs }">
          <v-btn icon v-bind="attrs" v-on="on">
            <v-icon>mdi-dots-horizontal</v-icon>
          </v-btn>
        </template>
        <v-list class="py-0">
          <LockUnlockListItem
            v-model="editMode"
            :disabled="!isContributor"
            @click="editMode = !editMode"
          />
          <v-divider />
          <DownloadNuxtPdf :config="printConfig" @error="showPrintError" />
          <DownloadReactPdf :config="printConfig" @error="showPrintError" />
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
            :start="period().start"
            :end="period().end"
            :editable="editMode"
            @newEntry="slotProps.on.newEntry"
          />
        </template>
      </template>
    </schedule-entries>
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
import { campRoleMixin } from '@/mixins/campRoleMixin'
import ContentCard from '@/components/layout/ContentCard.vue'
import Picasso from '@/components/program/picasso/Picasso.vue'
import ScheduleEntries from '@/components/program/ScheduleEntries.vue'
import PeriodSwitcher from '@/components/program/PeriodSwitcher.vue'
import DownloadNuxtPdf from '@/components/print/print-nuxt/DownloadNuxtPdfListItem.vue'
import DownloadReactPdf from '@/components/print/print-react/DownloadReactPdfListItem.vue'
import LockIcon from '@/components/generic/LockIcon.vue'
import LockUnlockListItem from '@/components/generic/LockUnlockListItem.vue'

export default {
  name: 'CampProgram',
  components: {
    DownloadNuxtPdf,
    DownloadReactPdf,
    PeriodSwitcher,
    ContentCard,
    Picasso,
    ScheduleEntries,
    LockIcon,
    LockUnlockListItem,
  },
  mixins: [campRoleMixin],
  props: {
    period: { type: Function, required: true },
  },
  data() {
    return {
      editMode: false,
      showError: null,
      error: null,
    }
  },
  computed: {
    camp() {
      return this.period().camp()
    },
    printConfig() {
      return {
        camp: this.period().camp()._meta.self,
        language: this.$store.state.lang.language,
        documentName: this.camp.title + '-picasso.pdf',
        contents: [
          {
            type: 'Picasso',
            options: {
              periods: [this.period()._meta.self],
              orientation: 'L',
            },
          },
        ],
      }
    },
  },
  methods: {
    showPrintError(event) {
      this.error = event
      this.showError = true
    },
  },
}
</script>
