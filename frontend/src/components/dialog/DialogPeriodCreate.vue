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

    <ValidationProvider v-slot="{ errors }" :name="$t('fields.description')" rules="required">
      <e-text-field
        v-model="entityData.description" hide-details="auto"
        :label="$t('fields.description')"
        :error-messages="errors"
        required />
    </ValidationProvider>

    <ValidationProvider v-slot="{ errors }" :name="$t('fields.start')"
                        vid="start"
                        rules="required">
      <e-date-picker
        v-model="entityData.start"
        hide-details="auto"
        :error-messages="errors"
        :label="$t('fields.start')"
        required />
    </ValidationProvider>

    <ValidationProvider v-slot="{ errors }" :name="$t('fields.end')" rules="required|minDate:@start">
      <e-date-picker
        v-model="entityData.end"
        hide-details="auto"
        :error-messages="errors"
        :label="$t('fields.end')"
        required />
    </ValidationProvider>
  </dialog-form>
</template>

<i18n>
{
  "en": {
    "validation": {
      "minDate": "The {_field_} must be equal or larger than {min}"
    },
    "fields": {
      "description": "description",
      "start": "start date",
      "end": "end date"
    }
  },
  "de": {
    "validation": {
      "minDate": "Das {_field_} muss gleich oder grösser sein als {min}"
    },
    "fields": {
      "description": "Beschreibung",
      "start": "Startdatum",
      "end": "Enddatum"
    }
  }
}
</i18n>

<script>
import DialogForm from './DialogForm'
import DialogBase from './DialogBase'
import { extend, ValidationProvider } from 'vee-validate'

export default {
  name: 'DialogPeriodCreate',
  components: { DialogForm, ValidationProvider },
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
