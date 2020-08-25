<template>
  <v-row no-gutters>
    <v-col cols="12">
      <div class="page_break">
        <h1>Title page</h1>

        <p>Name: {{ camp.name }}</p>
        <p>Title: {{ camp.title }}</p>
        <p>Motto: {{ camp.motto }}</p>
      </div>
    </v-col>
  </v-row>
</template>

<script>
export default {
  props: ['camp'],
}
</script>

<style lang="scss" scoped>
@media print {
  .page_break {
    page-break-after: always;
  }
}
</style>
