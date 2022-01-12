<template>
  <div class="e-form-container">
    <v-card outlined
            color="grey lighten-3" class="period mb-2 rounded-b-0">
      <v-row no-gutters>
        <v-col class="header mb-3">
          <legend class="pa-2 float-left">
            {{ $tc('components.activity.createScheduleEntries.name') }}
          </legend>

          <button-add color="secondary"
                      text
                      class="ma-1 float-right"
                      @click="addScheduleEntry" />
        </v-col>
      </v-row>
      <transition-group name="transition-list" tag="div" class="row no-gutters">
        <form-schedule-entry-item v-for="(scheduleEntry, index) in scheduleEntries"
                                  :key="scheduleEntry.key"
                                  class="transition-list-item pa-0 mb-4"
                                  :schedule-entry="scheduleEntry"
                                  :periods="periods"
                                  :is-last-item="scheduleEntries.length === 1"
                                  @delete="deleteEntry(index)" />
      </transition-group>
      <v-row>
        <v-col cols="12" class="text-center" />
      </v-row>
    </v-card>
  </div>
</template>
<script>
import FormScheduleEntryItem from './FormScheduleEntryItem.vue'
import ButtonAdd from '@/components/buttons/ButtonAdd.vue'
import { uniqueId } from 'lodash'

export default {
  name: 'FormScheduleEntryList',
  components: { FormScheduleEntryItem, ButtonAdd },
  props: {
    scheduleEntries: {
      type: Array,
      required: true
    },

    // all available periods
    periods: {
      type: Array,
      required: true
    },

    // currently visible period
    period: {
      type: Function,
      required: true
    }
  },
  data () {
    return {
      localScheduleEntries: this.scheduleEntries
    }
  },
  methods: {
    addScheduleEntry () {
      this.localScheduleEntries.push({
        period: () => (this.period)(),
        periodOffset: 420, // 7am
        length: 60, // 1 hours
        key: uniqueId()
      })
    },
    deleteEntry (index) {
      this.localScheduleEntries.splice(index, 1)
    }
  }
}
</script>
<style scoped lang="scss">
.period.period {
  border-bottom-width: 1px !important;
  border-bottom-style: solid !important;
  border-bottom-color: rgba(0, 0, 0, 0.42) !important;
}

.header {
  border-bottom: 1px solid map-get($blue-grey, 'lighten-4');

}
.transition-list-item {
  transition: all 0.5s;
  display: inline-block;
}
.transition-list-enter, .transition-list-leave-to {
  opacity: 0;
}

</style>
