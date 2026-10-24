<template>
  <v-table density="compact" class="mb-4">
    <thead>
      <tr>
        <th class="text-left w-0" scope="col">
          {{ $t('components.campHitobitoSync.hitobitoSyncDiffTable.field') }}
        </th>
        <th class="text-left" scope="col">
          {{ $t('components.campHitobitoSync.hitobitoSyncDiffTable.current') }}
        </th>
        <th class="text-left" scope="col">
          {{
            $t('components.campHitobitoSync.hitobitoSyncDiffTable.updated', { provider })
          }}
        </th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="row in rows" :key="row.label">
        <th class="font-weight-regular" scope="row">{{ row.label }}</th>
        <td class="text-medium-emphasis">
          <s v-if="row.changed">{{ row.current || emptyValue }}</s>
          <template v-else>{{ row.current || emptyValue }}</template>
        </td>
        <td
          :class="
            row.changed
              ? 'text-green-darken-3 font-weight-medium'
              : 'text-medium-emphasis'
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

<style scoped></style>
