<template>
  <v-btn
    text
    color="secondary"
    v-bind="$attrs"
    @click="$router.go(-1)">
    {{ $tc('global.button.cancel') }}
  </v-btn>
</template>

<script>
export default {
  name: 'ButtonCancel'
}
</script>
