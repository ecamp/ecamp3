<!--
Displays periods of a single camp.
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
    Infos zu den Perioden eines Lagers der Organisation {{ organizationName }}
    <ul>
      <li
        v-for="period in periods"
        :key="period.id">
        {{ period.description }} ({{ period.start }} - {{ period.end }})
      </li>
    </ul>
  </div>
</template>
<script>
export default {
  name: 'Periods',
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
    organizationName () {
      return this.campDetails.campType().organization().name
    },
    buttonText () {
      return this.editing ? 'Speichern' : 'Bearbeiten'
    }
  }
}
</script>
