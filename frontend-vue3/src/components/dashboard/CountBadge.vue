<template>
  <v-badge inline bordered dark color="blue-grey lighten-5" :content="String(count)" />
</template>

<script>
export default {
  name: 'CountBadge',
  props: {
    count: { type: [Number, String], required: true },
  },
}
</script>

<style scoped>
/* eslint-disable vue-scoped-css/no-unused-selector */
.v-badge {
  margin-top: 0;
}

:deep(.v-badge__badge) {
  color: currentcolor;
  min-width: 18px;
  font-weight: 500;
  line-height: 12.5px;
  margin-top: 0;
}

:is(.v-chip.primary, .v-list-item--active)
  .v-badge--bordered
  :deep(.v-badge__badge:after) {
  border-color: currentcolor;
}

:is(.v-chip.primary, .v-list-item--active) .v-badge :deep(.v-badge__badge) {
  background-color: #f2f9fc !important;
}

.v-badge--bordered :deep(.v-badge__badge:after) {
  border-width: 1px;
  transform: none;
  border-color: #cfd8dc;
}
/* eslint-enable vue-scoped-css/no-unused-selector */
</style>
