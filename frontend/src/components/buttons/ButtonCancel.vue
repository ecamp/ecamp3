<template>
  <v-btn text color="secondary"
         v-bind="$attrs"
         v-on="$listeners">
    {{ $tc('global.button.cancel') }}
  </v-btn>
</template>

<script>
export default {
  name: 'ButtonCancel'
}
</script>
