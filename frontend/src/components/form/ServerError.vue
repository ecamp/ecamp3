<template>
  <v-alert v-if="serverError" text outlined color="red darken-2" icon="mdi-alert-circle">
    <server-error-content :server-error="serverError" />
  </v-alert>
</template>

<script>
import ServerErrorContent from './ServerErrorContent.vue'

export default {
  name: 'ServerError',
  components: {
    ServerErrorContent,
  },
  props: {
    serverError: {
      type: [Object, String, Error],
      default: null,
    },
  },
}
</script>
