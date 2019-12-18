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

        <ul>
          <li
            v-for="eventInstance in period.eventInstances().items"
            :key="eventInstance._meta.self">
            <router-link
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
    }
  }
}
</script>
