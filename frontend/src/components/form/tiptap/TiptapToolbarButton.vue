<template>
  <v-btn icon tile height="40" width="42" color="black" class="rounded" v-on="$listeners">
    <v-icon>{{ icon }}</v-icon>
  </v-btn>
</template>
<script>
export default {
  name: 'TiptapToolbarButton',
  props: {
    icon: { type: String, required: true },
  },
}
</script>
