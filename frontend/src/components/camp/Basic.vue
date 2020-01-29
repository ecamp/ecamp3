<!--
Displays details on a single camp and allows to edit them.
-->

<template>
  <v-card>
    <v-toolbar dense color="blue-grey lighten-5">
      <v-icon left>
        mdi-cogs
      </v-icon>
      <v-toolbar-title>
        Einstellungen
      </v-toolbar-title>
      <v-spacer />
      <v-btn
        right
        color="primary"
        type="submit">
        {{ buttonText }}
      </v-btn>
    </v-toolbar>
    <v-skeleton-loader v-if="camp.loaded" type="article" />
    <v-card-text v-if="!camp.loaded">
      <v-alert
        v-for="(message, index) in messages"
        :key="index"
        :type="message.type">
        {{ message.text }}
      </v-alert>
      <v-form @submit.prevent="toggleEdit">
        <v-text-field
          :value="camp.name"
          readonly
          label="Name" />
        <v-text-field
          :value="camp.title"
          :readonly="editing"
          label="Titel" />
        <v-text-field
          :value="camp.motto"
          :readonly="editing"
          label="Motto" />
        <v-list>
          <v-label>Perioden</v-label>
          <v-list-item
            v-for="period in periods"
            :key="period.id">
            <v-list-item-content>
              <v-list-item-title>{{ period.description }}</v-list-item-title>
              <v-list-item-subtitle>{{ period.start }} - {{ period.end }}</v-list-item-subtitle>
            </v-list-item-content>
          </v-list-item>
        </v-list>
      </v-form>
    </v-card-text>
  </v-card>
</template>

<script>
export default {
  name: 'Basic',
  props: {
    camp: { type: Object, required: true }
  },
  data () {
    return {
      editing: false,
      messages: []
    }
  },
  computed: {
    periods () {
      return this.camp.periods().items
    },
    buttonText () {
      return this.editing ? 'Speichern' : 'Bearbeiten'
    }
  },
  methods: {
    async saveToAPI () {
      try {
        // TODO replace this with this.api.patch(...) once it's implemented
        await this.axios.patch(this.campUri, this.camp)
        this.messages = [{ type: 'success', text: 'Successfully saved' }]
      } catch (error) {
        this.messages = [{ type: 'error', text: 'Could not save camp details. ' + error }]
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
  .v-list-item {
    padding-left: 0;
  }
</style>
