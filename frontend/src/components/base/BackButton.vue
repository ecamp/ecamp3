<template>
  <v-btn v-if="$vuetify.breakpoint.xsOnly" aria-label="Zurück"
         icon @click="$router.go(-1)">
    <v-icon>mdi-arrow-left</v-icon>
  </v-btn>
</template>

<script>
export default {
  name: 'BackButton'
}
</script>

<style scoped>

</style>
