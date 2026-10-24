<template>
  <div>
    <slot
      :schedule-entries="filteredScheduleEntries"
      :loading="loading"
      :reload-entries="reloadScheduleEntries"
      :on="eventHandlers"
    />
    <DialogActivityCreate
      ref="dialogActivityCreate"
      :schedule-entry="newScheduleEntry"
      @activity-created="afterCreateActivity($event)"
    />

    <v-btn
      v-if="showButton"
      :absolute="!$vuetify.display.mdAndUp"
      :fixed="$vuetify.display.mdAndUp"
      dark
      fab
      icon
      size="large"
      elevation="6"
      style="z-index: 3"
      location="bottom right"
      class="fab--bottom_nav float-right"
      color="red"
      @click.stop="createNewActivity()"
    >
      <v-icon size="large">mdi-plus</v-icon>
    </v-btn>
  </div>
</template>

<script>
import jsonStringifyReactiveValue from '../print/jsonStringifyReactiveValue.js'
import DialogActivityCreate from './DialogActivityCreate.vue'
import { subscribeToMercureTopic } from '@/plugins/mercure.js'

export default {
  name: 'ScheduleEntries',
  components: {
    DialogActivityCreate,
  },
  props: {
    period: { type: Object, required: true },
    showButton: { type: Boolean, required: true },
    filterFn: { type: Function, required: false, default: () => true },
  },
  data() {
    return {
      eventHandlers: {
        newEntry: this.newEntryFromPicasso,
      },
      newScheduleEntry: {
        period: () => this.period,
        start: null,
        end: null,
      },
      unsubscribe: null,
    }
  },

  computed: {
    scheduleEntries() {
      // TODO for SideBar, add filtering for the current day, now that the API supports it
      return this.period.scheduleEntries()
    },
    filteredScheduleEntries() {
      return this.scheduleEntries.items.map((item) => ({
        ...item,
        filterMatch: this.filterFn(item),
      }))
    },
    loading() {
      return (
        this.scheduleEntries._meta.loading ||
        this.period.camp().activities()._meta.loading ||
        this.period.camp().categories()._meta.loading
      )
    },
  },

  async mounted() {
    this.unsubscribe = subscribeToMercureTopic(
      '/api' + this.period.scheduleEntries()._meta.self,
      (data) => {
        if (data.op === 'remove') {
          // Delete entity locally. This will trigger reload of collections automatically through hal-json-vuex
          this.api.localDel(data.path)
          return
        }

        // for create and update operations, store received HAL data
        // and reload collection
        this.api.storeHalJsonData(JSON.parse(data.value))
        this.api.reload(this.period.scheduleEntries())
      }
    )
  },

  unmounted() {
    this.unsubscribe?.()
  },

  methods: {
    createNewActivity() {
      this.newScheduleEntry.start = this.$date
        .utc(this.period.start)
        .add(8, 'hour')
        .format()
      this.newScheduleEntry.end = this.$date
        .utc(this.period.start)
        .add(9, 'hour')
        .format()
      this.showActivityCreateDialog()
    },
    showActivityCreateDialog() {
      this.$refs.dialogActivityCreate.showDialog = true
    },
    afterCreateActivity() {
      this.api.reload(this.period.scheduleEntries())
    },

    // Event Handler on.newEntry: update position & open create dialog
    newEntryFromPicasso(start, end) {
      this.newScheduleEntry.start = start
      this.newScheduleEntry.end = end
      this.showActivityCreateDialog()
    },

    async reloadScheduleEntries() {
      await this.api.reload(this.scheduleEntries)
    },
  },
}
</script>

<style lang="scss" scoped>
@use 'vuetify/settings';
@use 'sass:map';

.fab--bottom_nav {
  position: fixed;
  right: 16px !important;
  bottom: calc(
    16px + 56px + var(--footer-height) + env(safe-area-inset-bottom)
  ) !important;
  @media #{map.get(settings.$display-breakpoints, 'md-and-up')} {
    bottom: calc(16px + var(--footer-height) + env(safe-area-inset-bottom)) !important;
  }
}
</style>
