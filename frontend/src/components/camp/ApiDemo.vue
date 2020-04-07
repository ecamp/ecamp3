<!--
Displays details on a single camp and allows to edit them.
-->

<template>
  <content-group title="Api Demo">
    <v-skeleton-loader v-if="camp()._meta.loading" type="article" />

    <div v-else>
      <e-text-field
        label="Name"
        readonly
        :value="camp().name" />

      <api-form :entity="camp()">
        <api-text-field
          fieldname="title"
          label="Titel"
          :auto-save="false"
          required />

        <api-textarea
          fieldname="motto"
          label="Motto"
          :auto-save="false"
          required />

        <api-checkbox
          :value="checkbox"
          fieldname="check"
          label="Checkbox example"
          :auto-save="false"
          required />

        <api-time-picker
          :value="time"
          fieldname="time"
          label="Startzeit"
          :auto-save="false"
          required />

        <api-color-picker
          :value="color"
          fieldname="color"
          label="Color Example"
          :auto-save="false"
          required />
      </api-form>

      <v-list>
        <v-label>Perioden</v-label>
        <v-list-item
          v-for="period in periods.items"
          :key="period.id"
          class="px-0">
          <v-list-item-content>
            <v-list-item-title>{{ period.description }}</v-list-item-title>
            <v-list-item-subtitle>{{ period.start }} - {{ period.end }}</v-list-item-subtitle>
          </v-list-item-content>
          <api-date-picker
            :uri="period._meta.self"
            fieldname="start"
            label="Starttermin"
            :auto-save="false"
            required />
        </v-list-item>
      </v-list>
    </div>
  </content-group>
</template>

<script>
import ApiTextField from '../form/api/ApiTextField'
import ApiTextarea from '../form/api/ApiTextarea'
import ApiDatePicker from '../form/api/ApiDatePicker'
import ApiTimePicker from '../form/api/ApiTimePicker'
import ApiCheckbox from '../form/api/ApiCheckbox'
import ApiColorPicker from '../form/api/ApiColorPicker'

import ApiForm from '@/components/form/api/ApiForm'

import ContentGroup from '@/components/layout/ContentGroup'

export default {
  name: 'ApiDemo',
  components: {
    ContentGroup,
    ApiForm,
    ApiTextField,
    ApiTextarea,
    ApiCheckbox,
    ApiDatePicker,
    ApiTimePicker,
    ApiColorPicker
  },
  props: {
    camp: {
      type: Function,
      required: true
    }
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
