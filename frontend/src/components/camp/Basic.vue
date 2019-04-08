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

    <b-spinner
      v-if="loading"
      variant="primary"
      label="Loading" />

    <div
      v-if="!loading"
      class="card camp-detail-card">
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
            <li>Name: {{ camp.name }}</li>
            <li>
              <toggleable-input
                v-model="camp.title"
                :editing="editing"
                fieldname="Titel" />
            </li>
            <li>
              <toggleable-input
                v-model="camp.motto"
                :editing="editing"
                fieldname="Motto" />
            </li>
            <li>Owner: {{ owner.username }}</li>
            <!--
            <li>
              <toggleable-group-input
                v-if="camp._embedded"
                v-model="camp._embedded.owner"
                :editing="editing"
                fieldname="Besitzer" />
            </li> -->
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
import { mapState, mapActions } from 'vuex'

export default {
  name: 'Basic',
  components: {
    'ToggleableInput': () => import('@/components/form/ToggleableInput.vue') /* ,
    'ToggleableGroupInput': () => import('@/components/form/ToggleableGroupInput.vue') */
  },
  props: {
    campId: { type: String, required: true }
  },
  data () {
    return {
      editing: false,
      messages: []
    }
  },
  // make states available
  computed: {
    ...mapState({
      loading: state => state.shared.loading
    }),
    camp () {
      return this.$store.state.camp.camps[this.campId]
    },
    owner () {
      return this.camp.embeddedResource('owner')
    },
    periods () {
      return this.camp.embeddedArray('periods')
      /*
      if (this.camp._embedded == null) return []
      return this.camp._embedded.periods */
    },
    buttonText () {
      return this.editing ? 'Speichern' : 'Bearbeiten'
    }
  },
  created () {
    this.fetchById({ id: this.campId }) /* , forceReload: true */
  },
  methods: {
    ...mapActions('camp', [
      'fetchById'
    ]),
    /*
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
        this.campDetails = (await this.axios.patch(this.apiUrl, this.camp)).data
        this.messages = [{ type: 'success', text: 'Successfully saved' }]
      } catch (error) {
        this.messages = [{ type: 'danger', text: 'Could not save camp details. ' + error }]
      }
    }, */
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
