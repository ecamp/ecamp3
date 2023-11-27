<template>
  <v-card-actions>
    <div class="d-flex gap-2 flex-wrap flex-grow-1">
      <slot />
    </div>
  </v-card-actions>
</template>

<script>
export default {
  name: 'ContentActions',
}
</script>
