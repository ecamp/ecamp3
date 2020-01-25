<!--
Listing all event instances of a single camp.
-->

<template>
  <div>
    <v-card v-for="period in periods"
            :key="period.id">
      <v-alert
        v-for="(message, index) in messages"
        :key="index"
        :type="message.type">
        {{ message.text }}
      </v-alert>
      <template v-if="events.loading">
        <v-skeleton-loader
          v-for="n in 3"
          :key="n"
          type="list-item-avatar-two-line"/>
      </template>
      <template v-else>
        <v-calendar
          ref="calendar"
          :events="[
            {
              name: 'Event LS',
              start: '2019-12-24 09:00',
              end: '2019-12-24 09:15',
              color: 'red',
            },
            {
              name: 'Event LA',
              start: '2019-12-25 12:30',
              end: '2019-12-26 15:30',
              color: 'green',
            },
          ]"
          interval-height="42"
          now="2019-12-24 00:00:00"
          :start="periods[0].start + ' 00:00:00'"
          :end="periods[0].end + ' 00:00:00'"
          locale="de-ch"
          type="week"
          :weekdays="[1, 2, 3, 4, 5, 6, 0]"
          color="primary"/>
      </template>
    </v-card>
  </div>
</template>
<script>
export default {
  name: 'Picassso',
  props: {
    campUri: { type: String, required: true }
  },
  data () {
    return {
      editing: false,
      messages: []
    }
  },
  computed: {
    campDetails () {
      return this.api.get(this.campUri)
    },
    periods () {
      return this.campDetails.periods().items
    },
    buttonText () {
      return this.editing ? 'Speichern' : 'Bearbeiten'
    },
    events () {
      return this.campDetails.events()
    }
  },

  created: function () {
    // force reloading of all events
    if (this.campDetails.events()._meta.self) {
      this.api.reload(this.campDetails.events()._meta.self)
    }
  }
}
</script>
<style>
  .v-calendar-daily_head-day-label .v-btn--fab {
    height: 36px;
    min-width: 36px;
  }
</style>
