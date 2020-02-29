<template>
  <v-responsive :max-width="searchFocused ? 350 : 200" class="align-center transition-swing">
    <v-text-field rounded
                  hide-details solo
                  dark label="Suchen…"
                  solo-inverted single-line
                  flat class="align-center px-2"
                  append-icon="mdi-magnify"
                  @focus="searchFocused = true"
                  @blur="searchFocused = false"
                  color="white" dense />
  </v-responsive>
</template>

<script>
export default {
  name: 'GlobalSearch',
  data () {
    return {
      searchFocused: false
    }
  }
}
</script>

<style scoped>

</style>
