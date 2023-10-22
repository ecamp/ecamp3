<template>
  <div class="tw-text-rose-600">
    <h1 class="tw-text-lg tw-font-semibold">
      {{ title }}
    </h1>
    {{ error }}
  </div>
</template>

<script>
export default {
  props: {
    title: { type: String, default: 'Error' },
    error: { type: String, required: true },
  },
}
</script>
