<!--
Displays details on a single camp and allows to edit them.
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
            class="btn btn-sm camp-detail-submit-button">
            {{ buttonText }}
          </button>
          Vue.js Infos zu genau einem Lager
          <ul>
            <li>Name: {{ campDetails.name }}</li>
            <li>
              <toggleable-input
                v-model="campDetails.title"
                :editing="editing"
                fieldname="Titel" />
            </li>
            <li>
              <toggleable-input
                v-model="campDetails.motto"
                :editing="editing"
                fieldname="Motto" />
            </li>
            <li>
              <toggleable-group-input
                v-if="campDetails._embedded"
                v-model="campDetails._embedded.owner"
                :editing="editing"
                fieldname="Besitzer" />
            </li>
            <li>
              Lager-Perioden:
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
export default {
  name: 'Basic',
  components: {
    'ToggleableInput': () => import('@/components/form/ToggleableInput.vue'),
    'ToggleableGroupInput': () => import('@/components/form/ToggleableGroupInput.vue')
  },
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
      // TODO: Abstract the API calls instead of working with axios directly in the component?
      try {
        this.campDetails = (await this.axios.get(this.apiUrl)).data
      } catch (error) {
        this.messages = [{ type: 'danger', text: 'Could not get camp details for id ' + this.campId + '. ' + error }]
      }
    },
    async saveToAPI () {
      try {
        this.campDetails = (await this.axios.patch(this.apiUrl, this.campDetails)).data
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
