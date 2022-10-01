<template>
  <div>
    <slot
      :scheduleEntries="scheduleEntries.items"
      :loading="scheduleEntries._meta.loading"
      :on="eventHandlers"
    />
    <dialog-activity-create
      ref="dialogActivityCreate"
      :period="period"
      :schedule-entry="newScheduleEntry"
      @activityCreated="afterCreateActivity($event)"
    />

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
      @click.stop="createNewActivity()"
    >
      <v-icon>mdi-plus</v-icon>
    </v-btn>
  </div>
</template>

<script>
import DialogActivityCreate from './DialogActivityCreate.vue'

export default {
  name: 'ScheduleEntries',
  components: {
    DialogActivityCreate,
  },
  props: {
    period: { type: Function, required: true },
    showButton: { type: Boolean, required: true },
    day: {
      type: Function,
      required: false,
    },
  },
  data() {
    return {
      eventHandlers: {
        newEntry: this.newEntryFromPicasso,
      },
      newScheduleEntry: {
        period: () => this.period(),
        start: null,
        end: null,
      },
    }
  },
  computed: {
    scheduleEntries() {
      if (this.date) {
        return this.day().scheduleEntries()
      } else {
        return this.period().scheduleEntries()
      }
    },
  },
  mounted() {
    if (this.date) {
      this.day().scheduleEntries().$reload()
      // TODO which elements need a reload here and how?
    } else {
      this.period().scheduleEntries().$reload()
      this.period().camp().activities().$reload()
      this.period().days().$reload()
    }
    this.period().camp().categories().$reload()
  },

  methods: {
    createNewActivity() {
      this.newScheduleEntry.start = this.$date
        .utc(this.period().start)
        .add(8, 'hour')
        .format()
      this.newScheduleEntry.end = this.$date
        .utc(this.period().start)
        .add(9, 'hour')
        .format()
      this.showActivityCreateDialog()
    },
    showActivityCreateDialog() {
      this.$refs.dialogActivityCreate.showDialog = true
    },
    afterCreateActivity() {
      this.api.reload(this.period().scheduleEntries())
    },

    // Event Handler on.newEntry: update position & open create dialog
    newEntryFromPicasso(start, end) {
      this.newScheduleEntry.start = start
      this.newScheduleEntry.end = end
      this.showActivityCreateDialog()
    },
  },
}
</script>

<style lang="scss">
.fab--bottom_nav {
  position: fixed;
  bottom: calc(16px + 56px + env(safe-area-inset-bottom)) !important;
  @media #{map-get($display-breakpoints, 'sm-and-up')} {
    bottom: calc(16px + 36px + env(safe-area-inset-bottom)) !important;
  }
}
</style>
