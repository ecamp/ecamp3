<template>
  <div>
    <slot
      :scheduleEntries="scheduleEntries"
      :loading="apiScheduleEntries._meta.loading"
      :on="eventHandlers" />
    <dialog-activity-create
      ref="dialogActivityCreate"
      :camp="period().camp"
      :schedule-entry="newEntryPlaceholder"
      @activityCreated="afterCreateActivity($event)"
      @creationCanceled="cancelNewActivity" />

    <v-btn
      v-if="showButton"
      :fixed="$vuetify.breakpoint.xs"
      :absolute="!$vuetify.breakpoint.xs"
      dark
      fab
      style="z-index: 3"
      bottom
      right
      class="fab--bottom_nav float-right"
      color="red"
      @click.stop="createNewActivity()">
      <v-icon>mdi-plus</v-icon>
    </v-btn>
  </div>
</template>

<script>
import DialogActivityCreate from '@/components/dialog/DialogActivityCreate.vue'

import { defineHelpers } from '@/common/helpers/scheduleEntry/dateHelperLocal.js'
import { scheduleEntryRoute } from '@/router.js'

export default {
  name: 'ScheduleEntries',
  components: {
    DialogActivityCreate
  },
  props: {
    period: { type: Function, required: true },
    showButton: { type: Boolean, required: true }
  },
  data () {
    return {
      scheduleEntries: [],
      eventHandlers: {
        openEntry: this.openEntry,
        changePlaceholder: this.changePlaceholder,
        newEntry: this.newEntry
      },
      newEntryPlaceholder: defineHelpers({
        number: null,
        period: () => (this.period)(),
        periodOffset: -100, // hidden from view
        length: 60,
        activity: () => ({
          title: this.$tc('entity.activity.new'),
          location: '',
          camp: (this.period)().camp,
          category: () => ({
            id: null,
            short: null,
            color: 'grey elevation-4 v-event--temporary'
          })
        }),
        tmpEvent: true
      }, true)
    }
  },
  computed: {
    apiScheduleEntries () {
      // TODO for SideBar, add filtering for the current day when backend supports it
      return this.period().scheduleEntries()
    }
  },
  watch: {
    apiScheduleEntries: {
      immediate: true,
      handler (value) {
        this.scheduleEntries = value.items.map(entry => defineHelpers(entry, true)).concat(this.newEntryPlaceholder)
      }
    }
  },
  mounted () {
    this.period().scheduleEntries().$reload()
    this.period().camp().activities().$reload()
    this.period().camp().categories().$reload()
  },

  methods: {
    resetPlaceholder () {
      this.newEntryPlaceholder.periodOffset = -100
      this.newEntryPlaceholder.length = 60
    },

    createNewActivity () {
      this.resetPlaceholder()
      this.newEntryPlaceholder.periodOffset = 8 * 60
      this.showActivityCreateDialog()
    },
    showActivityCreateDialog () {
      this.$refs.dialogActivityCreate.showDialog = true
    },
    afterCreateActivity (data) {
      this.resetPlaceholder()
      this.api.reload(this.period().scheduleEntries())
    },
    cancelNewActivity () {
      this.resetPlaceholder()
    },

    // Event Handler on.openEntry: navigate to scheduleEntry `entry` (opens in new tab if newTab=true)
    openEntry (entry, newTab = false) {
      if (entry.tmpEvent) {
        return
      }

      if (newTab) {
        const routeData = this.$router.resolve(scheduleEntryRoute(entry))
        window.open(routeData.href, '_blank')
      } else {
        this.$router.push(scheduleEntryRoute(entry)).catch(() => {})
      }
    },

    // Event Handler on.changePlaceholder: change position of the current placeholder
    changePlaceholder (start, end) {
      this.newEntryPlaceholder.startTime = start
      this.newEntryPlaceholder.endTime = end
    },

    // Event Handler on.newEntry: update position of placeholder & open create dialog
    newEntry (start, end) {
      this.changePlaceholder(start, end)
      this.showActivityCreateDialog()
    },
    defineHelpers
  }
}
</script>

<style lang="scss">
.fab--bottom_nav {
  position: fixed;
  bottom: 16px + 56px !important;
  @media #{map-get($display-breakpoints, 'sm-and-up')}{
    bottom: 16px + 36px !important;
  }
}
</style>
