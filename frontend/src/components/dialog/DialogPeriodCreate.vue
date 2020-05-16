<template>
  <dialog-form
    v-model="showDialog"
    icon="mdi-calendar-plus"
    title="Create Period"
    max-width="600px"
    :submit-action="createPeriod"
    submit-color="success"
    :cancel-action="close">
    <template v-slot:activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>

    <e-text-field
      v-model="entityData.description"
      :name="$t('fields.description.name')"
      :label="$t('fields.description.label')"
      vee-rules="required" />

    <e-date-picker
      v-model="entityData.start"
      :name="$t('fields.start')"
      vee-id="start"
      vee-rules="required" />

    <e-date-picker
      v-model="entityData.end"
      :name="$t('fields.end')"
      vee-rules="required|minDate:@start" />
  </dialog-form>
</template>

<i18n>
{
  "en": {
    "validation": {
      "minDate": "The {_field_} must be equal or larger than {min}"
    },
    "fields": {
      "description": {
        "name": "description",
        "label": "description"
      },
      "start": "start date",
      "end": "end date"
    }
  },
  "de": {
    "validation": {
      "minDate": "Das {_field_} muss gleich oder grösser sein als {min}"
    },
    "fields": {
      "description": {
        "name": "Beschreibung",
        "label": "Beschreibung (Name des Teillagers)"
      },
      "start": "Startdatum",
      "end": "Enddatum"
    }
  }
}
</i18n>

<script>
import DialogForm from './DialogForm'
import DialogBase from './DialogBase'
import { extend } from 'vee-validate'

export default {
  name: 'DialogPeriodCreate',
  components: { DialogForm },
  extends: DialogBase,
  props: {
    camp: { type: Object, required: true }
  },
  data () {
    return {
      entityProperties: [
        'campId',
        'description',
        'start',
        'end'
      ],
      entityUri: '/periods'
    }
  },
  watch: {
    showDialog: function (showDialog) {
      if (showDialog) {
        this.setEntityData({
          campId: this.camp.id,
          description: '',
          start: '',
          end: ''
        })
      } else {
        // clear form on exit
        this.clearEntityData()
      }
    }
  },
  created () {
    /* Defining a component specific custom rule */
    extend('minDate', {
      params: ['min'],
      validate: (value, { min }) => {
        return new Date(value) >= new Date(min)
      },
      message: (field, values) => this.$t('validation.minDate', values)
    })
  },
  methods: {
    createPeriod () {
      return this.create().then(() => {
        this.api.reload(this.camp)
      })
    }
  }
}
</script>

<style scoped>

</style>
