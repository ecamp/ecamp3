<template>
  <div>
    <v-table density="compact" class="mb-4">
      <thead>
        <tr>
          <th class="text-left">
            {{ $t('components.campImport.hitobitoEventSummary.field') }}
          </th>
          <th class="text-left">
            {{ $t('components.campImport.hitobitoEventSummary.value') }}
          </th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="row in campRows" :key="row.label">
          <td>{{ row.label }}</td>
          <td :class="{ 'text-medium-emphasis': !row.value }">
            {{ row.value || emptyValue }}
          </td>
        </tr>
      </tbody>
    </v-table>

    <div v-for="(period, idx) in camp.periods" :key="idx" class="mb-4">
      <h3 class="font-weight-bold mb-1">
        {{ $t('components.campImport.hitobitoEventSummary.period', { number: idx + 1 }) }}
      </h3>
      <v-table density="compact">
        <thead>
          <tr>
            <th class="text-left">
              {{ $t('components.campImport.hitobitoEventSummary.field') }}
            </th>
            <th class="text-left">
              {{ $t('components.campImport.hitobitoEventSummary.value') }}
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="row in periodRows(period)" :key="row.label">
            <td>{{ row.label }}</td>
            <td :class="{ 'text-medium-emphasis': !row.value }">
              {{ row.value || emptyValue }}
            </td>
          </tr>
        </tbody>
      </v-table>
    </div>
  </div>
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
  },
  methods: {
    periodRows(period) {
      return [
        {
          label: this.$t('entity.period.fields.description'),
          value: period.description,
        },
        {
          label: this.$t('components.campImport.hitobitoEventSummary.fields.start'),
          value: this.formatDate(period.start),
        },
        {
          label: this.$t('components.campImport.hitobitoEventSummary.fields.end'),
          value: this.formatDate(period.end),
        },
      ]
    },
    formatDate(date) {
      if (!date) return null
      return this.$date(date).format('L')
    },
  },
}
</script>
