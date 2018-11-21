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
    <div class="card camp-detail-card">
      <div class="card-body">
        Vue.js Infos zu den Perioden genau eines Lagers
        <ul>
          <li
            v-for="period in periods"
            :key="period.id">
            {{ period.description }} ({{ period.start }} - {{ period.end }})
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: 'Periods',
  props: {
    campId: { type: String, required: true }
  },
  data () {
    return {
      editing: false,
      campDetails: { title: '', motto: '', _embedded: { owner: {} } },
      messages: []
    }
  },
  computed: {
    periods () {
      if (this.campDetails._embedded == null) return []
      return this.campDetails._embedded.periods
    },
    buttonText () {
      return this.editing ? 'Speichern' : 'Bearbeiten'
    },
    apiUrl () {
      return process.env.VUE_APP_ROOT_API + '/camp/' + this.campId
    }
  },
  created () {
    this.fetchFromAPI()
  },
  methods: {
    async fetchFromAPI () {
      try {
        this.campDetails = (await this.axios.get(this.apiUrl)).data
      } catch (error) {
        this.messages = [{ type: 'danger', text: 'Could not get camp details for id ' + this.campId + '. ' + error }]
      }
    }
  }
}
</script>
<style scoped>
  .camp-detail-card {
    margin-bottom: 10px;
  }
</style>
