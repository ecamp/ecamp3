<template>
  <v-btn color="error" v-bind="$attrs" v-on="$listeners">
    <v-icon>mdi-refresh</v-icon>
    {{ $tc('global.button.tryagain') }}
  </v-btn>
</template>

<script>
export default {
  name: 'ButtonRetry'
}
</script>
