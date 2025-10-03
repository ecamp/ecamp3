<template>
  <v-chip label outlined :color="value ? 'primary' : null" @click="$emit('input', !value)"
    >{{ label }} <CountBadge v-if="resultCount !== null" :count="resultCount"
  /></v-chip>
</template>

<script>
import CountBadge from '@/components/dashboard/CountBadge.vue'

export default {
  name: 'BooleanFilter',
  components: { CountBadge },
  props: {
    value: Boolean,
    label: { type: String, required: true },
    resultCount: { type: Number, default: null },
  },
}
</script>
