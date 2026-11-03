<template>
  <v-chip
    label
    :class="modelValue ?? 'text-primary'"
    :border="modelValue ? null : 'sm'"
    :variant="modelValue ? 'outlined' : 'flat'"
    :color="modelValue ? 'primary' : 'surface'"
    @click.prevent="$emit('update:modelValue', !modelValue)"
    >{{ label }} <CountBadge v-if="resultCount !== null" :count="resultCount"
  /></v-chip>
</template>

<script>
import CountBadge from '@/components/generic/CountBadge.vue'

export default {
  name: 'BooleanFilter',
  components: { CountBadge },
  props: {
    modelValue: Boolean,
    label: { type: String, required: true },
    resultCount: { type: Number, default: null },
  },
  emits: ['update:modelValue'],
}
</script>
