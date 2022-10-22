<template>
  <div class="tw-text-rose-600">
    <h1 class="tw-text-lg tw-font-semibold">{{ title }}</h1>
    {{ message }}
    {{ error.message }}
  </div>
</template>

<script>
export default {
  props: {
    title: { type: String, default: 'Error' },
    message: { type: String, default: '' },
    error: { type: Object, required: false, default: null },
  },
}
</script>
