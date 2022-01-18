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
      <print-button-react-pdf v-if="!dataLoading"
                              ref="printPreview"
                              :tc="boundTc"
                              :camp="camp" />
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
import PrintButtonReactPdf from '@/components/print/PrintButtonReactPdf.js'

export default {
  name: 'CampProgram',
  components: {
    PeriodSwitcher,
    ContentCard,
    Picasso,
    ScheduleEntries,
    PrintButtonReactPdf
  },
  mixins: [campRoleMixin],
  props: {
    period: { type: Function, required: true }
  },
  data () {
    return {
      editMode: false
    }
  },
  computed: {
    camp () {
      return this.period().camp()
    },
    dataLoading () {
      return this.camp._meta.loading ||
        this.camp.periods()._meta.loading ||
        this.camp.periods().items.some(period => {
          return period._meta.loading ||
            period.scheduleEntries()._meta.loading ||
            period.scheduleEntries().items.some(scheduleEntry => {
              return scheduleEntry._meta.loading ||
                scheduleEntry.activity()._meta.loading ||
                scheduleEntry.activity().category()._meta.loading ||
                scheduleEntry.activity().campCollaborations()._meta.loading ||
                scheduleEntry.activity().campCollaborations().items.some(responsible => {
                  return responsible._meta.loading ||
                    (responsible.user() !== null && responsible.user()._meta.loading)
                })
            })
        })
    },
    boundTc () {
      return this.$tc.bind(this)
    }
  }
}
</script>
