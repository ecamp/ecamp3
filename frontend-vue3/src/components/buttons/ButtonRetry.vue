<template>
  <v-btn color="error" v-bind="$attrs" v-on="$listeners">
    <v-icon left>mdi-refresh</v-icon>
    {{ $t('global.button.tryagain') }}
  </v-btn>
</template>

<script>
export default {
  name: 'ButtonRetry',
}
</script>
