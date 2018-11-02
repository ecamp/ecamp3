<!--
Displays details on a single camp (specified by the url in the prop) and allows to edit them.
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
import axios from 'axios'

export default {
  name: 'CampDetailsPeriods',
  props: { infoUrl: { type: String, required: true } },
  data () {
    return {
      campId: null,
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
    }
  },
  created () {
    this.fetchFromAPI()
  },
  methods: {
    async fetchFromAPI () {
      // TODO: Use an NPM plugin for REST interfaces instead of raw axios?
      try {
        if (!this.campId) {
          this.campId = (await axios.get(this.infoUrl)).data.campId
        }
        this.campDetails = (await axios.get('/api/camp/' + this.campId)).data
      } catch (error) {
        this.messages = [{ type: 'danger', text: 'Could get camp details for id ' + this.campId + '. ' + error }]
      }
    },
    async saveToAPI () {
      try {
        this.campDetails = (await axios.patch('/api/camp/' + this.campId, this.campDetails)).data
        this.messages = [{ type: 'success', text: 'Successfully saved' }]
      } catch (error) {
        this.messages = [{ type: 'danger', text: 'Could not save camp details. ' + error }]
      }
    },
    toggleEdit () {
      if (this.editing) {
        this.saveToAPI()
      }
      this.editing = !this.editing
    }
  }
}
</script>

<style scoped>
  .camp-detail-card {
    margin-bottom: 10px;
  }

  .camp-detail-submit-button {
    float: right;
  }
</style>
