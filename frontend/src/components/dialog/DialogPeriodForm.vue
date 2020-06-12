<template>
  <div>
    <e-text-field
      v-model="period.description"
      :name="$t('fields.description.name')"
      :label="$t('fields.description.label')"
      vee-rules="required" />

    <e-date-picker
      v-model="period.start"
      :name="$t('fields.start')"
      vee-id="start"
      vee-rules="required" />

    <e-date-picker
      v-model="period.end"
      :name="$t('fields.end')"
      vee-rules="required|minDate:@start" />
  </div>
</template>

<i18n>
{
  "en": {
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
export default {
  name: 'DialogPeriodForm',
  props: {
    period: { type: Object, required: true }
  }
}
</script>
