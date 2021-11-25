<template>
  <v-row no-gutters>
    <v-col cols="12">
      <error v-if="$fetchState.error">{{ $fetchState.error.message }}</error>
      <div v-else-if="!$fetchState.pending" class="event">
        <h2 :id="'activity_' + activity.id">
          {{ scheduleEntry.number }}
          <v-chip dark :color="category.color">{{ category.short }}</v-chip>
          {{ activity.title }}
        </h2>

        <!-- Header -->
        <v-row dense class="activity-header">
          <v-col class="col col-6">
            <v-row dense>
              <v-col cols="2">
                {{ $tc('entity.scheduleEntry.fields.nr') }}
              </v-col>
              <v-col cols="10">
                {{ $tc('entity.scheduleEntry.fields.time') }}
                {{ scheduleEntries.length }}
              </v-col>
            </v-row>
            <v-row
              v-for="scheduleEntryItem in scheduleEntries"
              :key="scheduleEntryItem._meta.self"
              dense
            >
              <v-col cols="2"> ({{ scheduleEntryItem.number }}) </v-col>
              <v-col cols="10">
                {{
                  $date
                    .utc(scheduleEntryItem.startTime)
                    .format($tc('global.datetime.dateShort'))
                }}
                <b>
                  {{
                    $date
                      .utc(scheduleEntryItem.startTime)
                      .format($tc('global.datetime.hourShort'))
                  }}
                </b>
                -
                {{
                  $date
                    .utc(scheduleEntryItem.startTime)
                    .format($tc('global.datetime.dateShort')) ==
                  $date
                    .utc(scheduleEntryItem.endTime)
                    .format($tc('global.datetime.dateShort'))
                    ? ''
                    : $date
                        .utc(scheduleEntryItem.endTime)
                        .format($tc('global.datetime.dateShort'))
                }}
                <b>
                  {{
                    $date
                      .utc(scheduleEntryItem.endTime)
                      .format($tc('global.datetime.hourShort'))
                  }}
                </b>
              </v-col>
            </v-row>
          </v-col>
          <v-col class="col col-6">
            <v-row dense>
              <v-col>
                <span class="font-weight-bold"
                  >{{ $tc('entity.activity.fields.location') }}:
                </span>
                {{ activity.location }}
              </v-col>
            </v-row>
            <v-row dense>
              <v-col>
                <span class="font-weight-bold">{{
                  $tc('entity.activity.fields.responsible')
                }}</span>
                <span>Smiley</span>
              </v-col>
            </v-row>
          </v-col>
        </v-row>
      </div>
    </v-col>
  </v-row>
</template>

<script>
import { defineHelpers } from '@/../common/helpers/scheduleEntry/dateHelperUTC.js'

export default {
  props: {
    scheduleEntry: { type: Object, required: true },
  },
  data() {
    return {
      activity: null,
      category: null,
    }
  },
  async fetch() {
    this.activity = await this.scheduleEntry.activity()._meta.load
    this.category = await this.activity.category()._meta.load

    /** TODO: something is not yet working here with loading scheduleEntries() from hal-json-vuex. Needs some further debugging */
    // await this.activity.scheduleEntries()._meta.load
  },
  computed: {
    scheduleEntries() {
      return this.activity
        .scheduleEntries()
        .items.map((entry) => defineHelpers(entry))
    },
  },
}
</script>

<style lang="scss" scoped>
@media print {
  .event {
    page-break-after: always;
  }
}
</style>
