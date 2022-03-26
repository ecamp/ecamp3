<template>
  <v-row no-gutters>
    <v-col cols="12">
      <div class="tw-break-after-page">
        <h1>Story line for period {{ period.description }}</h1>
      </div>
    </v-col>
  </v-row>
</template>

<script>
export default {
  props: {
    camp: { type: Object, required: true },
    period: {
      type: Object,
      required: true,
    },
  },
}
</script>
