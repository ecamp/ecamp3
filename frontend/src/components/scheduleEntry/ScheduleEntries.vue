<template>
  <div>
    <slot
      :scheduleEntries="scheduleEntries"
      :loading="apiScheduleEntries._meta.loading"
      :showActivityCreateDialog="showActivityCreateDialog"
      :showActivityEditDialog="showActivityEditDialog" />
    <dialog-activity-create
      ref="dialogActivityCreate"
      :camp="period().camp"
      :period="period"
      :schedule-entry="popupEntry"
      @activityCreated="afterCreateActivity($event)"
      @creationCanceled="cancelNewActivity" />
    <dialog-activity-edit
      ref="dialogActivityEdit"
      :schedule-entry="popupEntry" />
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
import DialogActivityEdit from '@/components/dialog/DialogActivityEdit.vue'
import { defineHelpers } from '@/common/helpers/scheduleEntry/dateHelperLocal.js'

export default {
  name: 'ScheduleEntries',
  components: {
    DialogActivityCreate,
    DialogActivityEdit
  },
  props: {
    period: { type: Function, required: true },
    showButton: { type: Boolean, required: true }
  },
  data () {
    return {
      scheduleEntries: [],
      deleteTempEntryCallback: () => {},
      popupEntry: {}
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
        this.scheduleEntries = value.items.map(entry => defineHelpers(entry, true))
      }
    }
  },
  methods: {
    createNewActivity () {
      const entry = defineHelpers({
        number: null,
        period: () => (this.period)(),
        periodOffset: 420,
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
        })
      }, true)
      this.showActivityCreateDialog(entry, () => {})
    },
    showActivityCreateDialog (entry, deleteTempEntryCallback) {
      this.popupEntry = entry
      this.deleteTempEntryCallback = deleteTempEntryCallback
      this.$refs.dialogActivityCreate.showDialog = true
    },
    showActivityEditDialog (entry) {
      this.popupEntry = entry
      this.$refs.dialogActivityEdit.showDialog = true
    },
    afterCreateActivity (data) {
      this.api.reload(this.period().scheduleEntries())
      this.scheduleEntries.push(...data.scheduleEntries().items.map(entry => defineHelpers(entry, true)))
      this.deleteTempEntryCallback()
    },
    cancelNewActivity () {
      this.deleteTempEntryCallback()
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
