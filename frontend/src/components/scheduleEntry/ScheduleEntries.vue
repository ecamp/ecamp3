<template>
  <div>
    <slot
      :scheduleEntries="scheduleEntries"
      :loading="apiScheduleEntries._meta.loading"
      :on="eventHandlers" />
    <dialog-activity-create
      ref="dialogActivityCreate"
      :period="period"
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
import DialogActivityCreate from '@/components/scheduleEntry/DialogActivityCreate.vue'

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
        changePlaceholder: this.changePlaceholder,
        newEntry: this.newEntry
      },
      newEntryPlaceholder: {
        number: null,
        period: () => (this.period)(),
        start: null,
        end: null,
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
      }
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
        this.scheduleEntries = value.items.concat(this.newEntryPlaceholder)
      }
    }
  },
  mounted () {
    this.resetPlaceholder()

    this.period().scheduleEntries().$reload()
    this.period().camp().activities().$reload()
    this.period().camp().categories().$reload()
    this.period().days().$reload()
  },

  methods: {
    resetPlaceholder () {
      // start and end before period start (placeholder not visible)
      this.newEntryPlaceholder.start = this.$date.utc(this.period().start).subtract(4, 'hour').format()
      this.newEntryPlaceholder.end = this.$date.utc(this.period().start).subtract(3, 'hour').format()
    },

    createNewActivity () {
      this.resetPlaceholder()
      this.newEntryPlaceholder.start = this.$date.utc(this.period().start).add(8, 'hour').format()
      this.newEntryPlaceholder.end = this.$date.utc(this.period().start).add(9, 'hour').format()
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

    // Event Handler on.changePlaceholder: change position of the current placeholder
    changePlaceholder (start, end) {
      this.newEntryPlaceholder.start = start
      this.newEntryPlaceholder.end = end
    },

    // Event Handler on.newEntry: update position of placeholder & open create dialog
    newEntry (start, end) {
      this.changePlaceholder(start, end)
      this.showActivityCreateDialog()
    }
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
