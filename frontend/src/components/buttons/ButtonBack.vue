<template>
  <v-btn
    icon
    :aria-label="$tc('global.button.back')"
    v-bind="$attrs"
    @click="$router.go(-1)"
  >
    <v-icon>mdi-arrow-left</v-icon>
  </v-btn>
</template>

<script>
export default {
  name: 'ButtonBack',
}
</script>

<style scoped></style>
