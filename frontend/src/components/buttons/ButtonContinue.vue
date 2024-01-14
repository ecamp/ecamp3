<template>
  <v-btn v-bind="$attrs" color="primary" v-on="$listeners">
    {{ $tc('global.button.continue') }}
  </v-btn>
</template>

<script>
export default {
  name: 'ButtonContinue',
}
</script>
