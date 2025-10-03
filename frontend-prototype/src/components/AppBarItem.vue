<script>
  export default {
    name: 'AppBarItem',
    props: {
      text: { type: String, required: true },
      icon: { type: String, required: true },
    },
  }
</script>

<template>
  <v-btn text>
    <template v-if="$vuetify.display.mdAndUp" #prepend>
      <v-icon :icon="icon" />
    </template>
    <template v-if="!$vuetify.display.mdAndUp">
      <v-icon :icon="icon" />
    </template>
    <span class="sr-only-sm-and-down">{{ text }}</span>
  </v-btn>
</template>

<style scoped></style>
