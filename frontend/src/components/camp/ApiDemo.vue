<!--
Displays details on a single camp and allows to edit them.
-->

<template>
  <content-group title="Api Demo">
    <v-skeleton-loader v-if="camp()._meta.loading" type="article" />

    <div v-else>
      <e-text-field
        name="Name"
        readonly
        :value="camp().name" />

      <api-form :entity="camp()">
        <api-text-field
          fieldname="title"
          name="Title"
          vee-rules="required|min:3" />

        <api-textarea
          fieldname="motto"
          name="Motto"
          label="Motto (roter Faden)"
          :auto-save="false"
          vee-rules="required|min:3" />

        <api-checkbox
          :value="checkbox"
          fieldname="check"
          name="Checkbox example"
          vee-rules="required" />

        <api-time-picker
          :value="time"
          fieldname="time"
          name="Startzeit"
          :auto-save="false"
          vee-rules="required" />

        <api-color-picker
          :value="color"
          fieldname="color"
          name="Color Example"
          :auto-save="false"
          vee-rules="required" />
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
            name="Starttermin"
            :auto-save="false"
            vee-rules="required" />
        </v-list-item>
      </v-list>
    </div>
  </content-group>
</template>

<i18n>
{
  "en": {
    "validation": {
      "min10": "This field {_field_} must be at least 10 characters long"
    }
  },
  "de": {
    "validation": {
      "min10": "Das Feld {_field_} muss mindest 10 Zeichen lang sein"
    }
  }
}
</i18n>

<script>
import ApiTextField from '../form/api/ApiTextField'
import ApiTextarea from '../form/api/ApiTextarea'
import ApiDatePicker from '../form/api/ApiDatePicker'
import ApiTimePicker from '../form/api/ApiTimePicker'
import ApiCheckbox from '../form/api/ApiCheckbox'
import ApiColorPicker from '../form/api/ApiColorPicker'
import ApiForm from '@/components/form/api/ApiForm'
import ContentGroup from '@/components/layout/ContentGroup'
import { extend } from 'vee-validate'

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
  },
  created () {
    /* Defining a component specific custom rule */
    extend('min10', {
      validate: value => {
        return value.length >= 10
      },
      message: (_, values) => this.$t('validation.min10', values)
    })
  }
}
</script>

<style scoped>
  .v-list-item {
    padding-left: 0;
  }
</style>
