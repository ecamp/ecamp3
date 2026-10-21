<template>
  <v-table density="compact" class="hitobito-table mb-4">
    <caption class="text-left ml-4">
      <h2>{{ $t('components.campImport.hitobitoEventSummary.title') }}</h2>
    </caption>
    <thead>
      <tr>
        <th class="text-left w-0" scope="col">
          {{ $t('components.campImport.hitobitoEventSummary.field') }}
        </th>
        <th class="text-left" scope="col">
          {{ $t('components.campImport.hitobitoEventSummary.value') }}
        </th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="row in campRows" :key="row.label">
        <th class="font-weight-regular" scope="row">{{ row.label }}</th>
        <td :class="{ 'text-medium-emphasis': !row.value }">
          {{ row.value || emptyValue }}
        </td>
      </tr>
    </tbody>

    <tbody v-for="(periodRows, idx) in periodSections" :key="idx" class="mb-4">
      <tr>
        <th colspan="2" scope="rowgroup" class="text-left">
          <h3 class="mb-2 mt-5">
            {{
              $t('components.campImport.hitobitoEventSummary.period', { number: idx + 1 })
            }}
          </h3>
        </th>
      </tr>
      <tr v-for="row in periodRows" :key="row.label">
        <th class="font-weight-regular" scope="row">{{ row.label }}</th>
        <td :class="{ 'text-medium-emphasis': !row.value }">
          {{ row.value || emptyValue }}
        </td>
      </tr>
    </tbody>
  </v-table>
</template>

<script>
export default {
  name: 'HitobitoEventSummary',
  props: {
    camp: { type: Object, required: true },
  },
  computed: {
    emptyValue() {
      return '-'
    },
    campRows() {
      return [
        {
          label: this.$t('components.campImport.hitobitoEventSummary.fields.title'),
          value: this.camp.title,
        },
        {
          label: this.$t('components.campImport.hitobitoEventSummary.fields.motto'),
          value: this.camp.motto,
        },
        {
          label: this.$t('components.campImport.hitobitoEventSummary.fields.address'),
          value: this.camp.addressName,
        },
      ]
    },
    periodSections() {
      return this.camp.periods.map(({ description, start, end }) => [
        {
          label: this.$t('entity.period.fields.description'),
          value: description,
        },
        {
          label: this.$t('components.campImport.hitobitoEventSummary.fields.start'),
          value: this.formatDate(start),
        },
        {
          label: this.$t('components.campImport.hitobitoEventSummary.fields.end'),
          value: this.formatDate(end),
        },
      ])
    },
  },
  methods: {
    formatDate(date) {
      if (!date) return null
      return this.$date(date).format('L')
    },
  },
}
</script>

<style scoped></style>
