<!--
Listing all event instances of a single camp.
-->

<template>
  <div>
    <div
      v-for="(message, index) in messages"
      :key="index"
      :class="'alert-' + message.type"
      role="alert"
      class="alert">
      {{ message.text }}
    </div>

    <ul>
      <li
        v-for="period in periods"
        :key="period._meta.self">
        {{ period.description }} ({{ period.start }} - {{ period.end }})

        <div v-if="events.loading">
          <b-spinner label="Loading..." />
        </div>

        <!-- wait for all events to be loaded => avoid each eventInstance to load separately -->
        <ul v-if="!events.loading">
          <li
            v-for="eventInstance in period.event_instances().items"
            :key="eventInstance._meta.self">
            <div v-if="eventInstance.event().loading">
              <b-spinner label="Loading..." />
            </div>
            <router-link
              v-if="!eventInstance.event().loading"
              :to="{ name: 'event', params: { eventUri: eventInstance.event()._meta.self } }">
              EventInstance {{ eventInstance.id }} {{ eventInstance.start_time }} {{ eventInstance.event().title }}
            </router-link>
          </li>
        </ul>
      </li>
    </ul>
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
