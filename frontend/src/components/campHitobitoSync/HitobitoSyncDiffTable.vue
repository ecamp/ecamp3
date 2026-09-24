<template>
  <v-table density="comfortable">
    <thead>
      <tr>
        <th class="text-left">
          {{ $t('components.campHitobitoSync.hitobitoSyncDiffTable.field') }}
        </th>
        <th class="text-left">
          {{ $t('components.campHitobitoSync.hitobitoSyncDiffTable.current') }}
        </th>
        <th class="text-left">
          {{
            $t('components.campHitobitoSync.hitobitoSyncDiffTable.updated', { provider })
          }}
        </th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="row in rows" :key="row.label">
        <td class="font-weight-medium">{{ row.label }}</td>
        <td class="text-medium-emphasis">
          <s v-if="row.changed">{{ row.current || emptyValue }}</s>
          <template v-else>{{ row.current || emptyValue }}</template>
        </td>
        <td
          :class="
            row.changed ? 'text-success font-weight-medium' : 'text-medium-emphasis'
          "
        >
          {{ row.updated || emptyValue }}
        </td>
      </tr>
    </tbody>
  </v-table>
</template>

<script>
export default {
  name: 'HitobitoSyncDiffTable',
  props: {
    rows: { type: Array, required: true },
    provider: { type: String, required: true },
  },
  computed: {
    emptyValue() {
      return '-'
    },
  },
}
</script>
