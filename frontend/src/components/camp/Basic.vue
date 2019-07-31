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
            :value="campDetails.title"
            :editing="editing"
            fieldname="Titel" />
        </li>
        <li>
          <toggleable-input
            :value="campDetails.motto"
            :editing="editing"
            fieldname="Motto" />
        </li>
        <li>
          Lager-Perioden:
          <ul>
            <li
              v-for="period in periods"
              :key="period().id">
              {{ period().description }} ({{ period().start }} - {{ period().end }})
            </li>
          </ul>
        </li>
      </ul>
    </form>
  </div>
</template>

<script>
export default {
  name: 'Basic',
  components: {
    'ToggleableInput': () => import('@/components/form/ToggleableInput.vue')
  },
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
      return this.api(this.campUri)
    },
    periods () {
      return this.campDetails._meta.loading ? [] : this.campDetails.periods().items
    },
    buttonText () {
      return this.editing ? 'Speichern' : 'Bearbeiten'
    }
  },
  methods: {
    async saveToAPI () {
      try {
        this.campDetails = (await this.axios.patch(this.campUri, this.campDetails)).data
        this.messages = [ { type: 'success', text: 'Successfully saved' } ]
      } catch (error) {
        this.messages = [ { type: 'danger', text: 'Could not save camp details. ' + error } ]
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
  .camp-detail-submit-button {
    float: right;
  }
</style>
