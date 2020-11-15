<!--
Show all activity schedule entries of a single period.
-->

<template>
  <content-card>
    <v-sheet>
      <search-mobile v-if="$vuetify.breakpoint.xs" />
      <v-btn
        :fixed="$vuetify.breakpoint.xs"
        :absolute="!$vuetify.breakpoint.xs"
        light class="fab--top_nav"
        fab small
        style="z-index: 3"
        top right
        :class="{'float-right':!$vuetify.breakpoint.xs}"
        color="white"
        :to="{ query: { ...$route.query, list: !listFormat || undefined } }">
        <v-icon v-if="listFormat">mdi-calendar-month</v-icon>
        <v-icon v-else>mdi-menu</v-icon>
      </v-btn>
      <template v-if="!(period() && !period()._meta.loading)">
        <v-skeleton-loader v-if="listFormat" type="list-item-avatar-two-line@2" class="py-2" />
        <v-skeleton-loader v-else type="table" />
      </template>
      <template v-if="firstPeriodLoaded">
        <picasso
          v-show="!listFormat"
          class="mx-2 ma-sm-0 pa-sm-2"
          :events="scheduleEntries"
          :period="period"
          :start="Date.parse(period().start)"
          :end="Date.parse(period().end)"
          :dialog-activity-create="showActivityCreateDialog"
          :dialog-activity-edit="showActivityEditDialog" />
        <activity-list
          v-show="listFormat"
          :events="scheduleEntries"
          :period="period" />
      </template>
      <dialog-activity-create
        ref="dialogActivityCreate"
        :schedule-entry="popupEntry"
        @activityCreated="afterCreateActivity($event)"
        @creationCanceled="cancelNewActivity" />
      <dialog-activity-edit
        ref="dialogActivityEdit"
        :schedule-entry="popupEntry" />
      <v-btn
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
    </v-sheet>
  </content-card>
</template>
<script>
import ContentCard from '@/components/layout/ContentCard'
import SearchMobile from '@/components/navigation/SearchMobile'
import Picasso from '@/components/camp/Picasso'
import ActivityList from '@/components/camp/ActivityList'
import DialogActivityCreate from '@/components/dialog/DialogActivityCreate'
import DialogActivityEdit from '@/components/dialog/DialogActivityEdit'
import { defineHelpers } from '@/components/scheduleEntry/dateHelperLocal'

export default {
  name: 'CampProgram',
  components: {
    ContentCard,
    SearchMobile,
    Picasso,
    ActivityList,
    DialogActivityCreate,
    DialogActivityEdit
  },
  props: {
    period: { type: Function, required: true }
  },
  data () {
    return {
      scheduleEntries: [],
      deleteTempEntryCallback: () => {},
      popupEntry: {}
    }
  },
  computed: {
    listFormat () {
      return this.$route.query.list
    },
    camp () {
      return this.period().camp()
    },
    firstPeriodLoaded () {
      return this.period() && !this.period()._meta.loading
    },
    apiScheduleEntries () {
      return this.period().scheduleEntries()
    }
  },
  watch: {
    apiScheduleEntries (value) {
      this.scheduleEntries = value.items.map(entry => defineHelpers(entry, true))
    }
  },
  beforeMount () {
    this.scheduleEntries = this.apiScheduleEntries.items.map(entry => defineHelpers(entry, true))
  },
  methods: {
    createNewActivity () {
      const entry = defineHelpers({
        number: null,
        period: () => (this.period)(),
        periodOffset: 0,
        length: 0,
        activity: () => ({
          title: this.$tc('entity.activity.new'),
          location: '',
          camp: (this.period)().camp,
          activityCategory: () => ({
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

  .fab--top_nav {
    position: fixed;
    top: 16px + 105px !important;
    @media #{map-get($display-breakpoints, 'sm-and-up')}{
      top: 16px + 65px !important;
    }
  }
</style>

<style lang="scss" scoped>
  ::v-deep .v-skeleton-loader__list-item-avatar-two-line {
    height: 60px;
  }
</style>
