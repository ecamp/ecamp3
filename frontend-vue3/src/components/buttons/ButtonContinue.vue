<template>
  <v-btn color="primary" v-bind="$attrs" v-on="$listeners">
    {{ $t('global.button.continue') }}
  </v-btn>
</template>

<script>
export default {
  name: 'ButtonContinue',
}
</script>
