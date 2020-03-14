<!--
Displays details on a single camp and allows to edit them.
-->

<template>
  <content-card icon="mdi-cogs" title="Api Demo">
    <v-skeleton-loader v-if="camp()._meta.loading" type="article" />
    <v-form v-else>
      <v-text-field
        label="Name"
        readonly
        :value="camp().name"
        class="mr-2 ml-2" />
      <api-text-field
        :value="camp().title"
        :uri="camp()._meta.self"
        fieldname="title"
        label="Titel"
        :auto-save="false"
        readonly
        required />
      <api-textarea
        :value="camp().motto"
        :uri="camp()._meta.self"
        fieldname="motto"
        label="Motto"
        :auto-save="false"
        disabled
        required />

      <api-checkbox
        :value="checkbox"
        :uri="camp()._meta.self"
        fieldname="check"
        label="Checkbox example"
        readonly
        required />

      <api-time-picker
        :value="time"
        :uri="camp()._meta.self"
        fieldname="time"
        label="Startzeit"
        disabled
        required />

      <api-color-picker
        :value="color"
        :uri="camp()._meta.self"
        fieldname="color"
        label="Color Example"
        readonly
        required />

      <v-list>
        <v-label>Perioden</v-label>
        <v-list-item
          v-for="period in periods.items"
          :key="period.id">
          <v-list-item-content>
            <v-list-item-title>{{ period.description }}</v-list-item-title>
            <v-list-item-subtitle>{{ period.start }} - {{ period.end }}</v-list-item-subtitle>

            <api-date-picker
              :value="period.start"
              :uri="period._meta.self"
              fieldname="start"
              label="Starttermin"
              disabled
              required />
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-form>
  </content-card>
</template>

<script>
import ApiTextField from '../form/ApiTextField'
import ApiTextarea from '../form/ApiTextarea'
import ApiDatePicker from '../form/ApiDatePicker'
import ApiTimePicker from '../form/ApiTimePicker'
import ApiCheckbox from '../form/ApiCheckbox'
import ApiColorPicker from '../form/ApiColorPicker'

import ContentCard from '@/components/base/ContentCard'

export default {
  name: 'ApiDemo',
  components: { ContentCard, ApiTextField, ApiTextarea, ApiCheckbox, ApiDatePicker, ApiTimePicker, ApiColorPicker },
  props: {
    camp: { type: Function, required: true }
  },
  data () {
    return {
      time: '00:15',
      checkbox: true,
      color: '#56789F'
    }
  },
  computed: {
    periods () {
      return this.camp().periods()
    }
  }
}
</script>

<style scoped>
  .v-list-item {
    padding-left: 0;
  }
</style>
