<!--
Displays details on a single camp (with id specified as prop campId) and allows to edit them.
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
        <form @submit.prevent="toggleEdit">
          <button
            :class="{ 'btn-primary': editing, 'btn-outline-primary': !editing }"
            type="submit"
            class="btn btn-sm camp-detail-submit-button">{{ buttonText }}
          </button>
          Vue.js Infos zu genau einem Lager, id {{ campId }}
          <ul>
            <li>Name: {{ campDetails.name }}</li>
            <li>
              <toggleable-input
                :editing="editing"
                v-model="campDetails.title"
                fieldname="Titel"/>
            </li>
            <li>
              <toggleable-input
                :editing="editing"
                v-model="campDetails.motto"
                fieldname="Motto"/>
            </li>
            <li>
              <toggleable-group-input
                v-if="campDetails._embedded"
                :editing="editing"
                v-model="campDetails._embedded.owner"
                fieldname="Besitzer"/>
            </li>
            <li>Lager-Perioden:
              <ul>
                <li
                  v-for="period in periods"
                  :key="period.id">
                  {{ period.description }} ({{ period.start }} - {{ period.end }})
                </li>
              </ul>
            </li>
          </ul>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'CampDetails',
  components: {
    'ToggleableInput': () => import('@/components/toggleable-input.vue'),
    'ToggleableGroupInput': () => import('@/components/toggleable-group-input.vue')
  },
  props: { campId: { type: String, required: true } },
  data () {
    return {
      editing: false,
      campDetails: { title: '', motto: '', _embedded: { owner: '' } },
      messages: this.fetchFromAPI()
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
  methods: {
    fetchFromAPI () {
      // TODO: Use an NPM plugin for REST interfaces instead of raw axios?
      axios.get('/api/camp/' + this.campId)
        .then((response) => {
          this.campDetails = response.data
        })
        .catch((error) => {
          this.messages = [{ type: 'danger', text: 'Could get camp details. ' + error }]
        })
    },
    saveToAPI () {
      axios.patch('/api/camp/' + this.campId, this.campDetails)
        .then((response) => {
          this.messages = [{ type: 'success', text: 'Successfully saved' }]
          this.campDetails = response.data
        })
        .catch((error) => {
          this.messages = [{ type: 'danger', text: 'Could not save camp details. ' + error }]
        })
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
