<template>
  <v-container fluid>
    <content-card
      class="contentCard"
      :title="$tc('views.pageNotFound.title')"
      max-width="800"
    >
      <v-row>
        <v-col
          offset-sm="0"
          offset-md="1"
          offset-lg="2"
          offset-xl="2"
          sm="12"
          md="10"
          lg="8"
          xl="8"
        >
          <four-zero-four-display />
        </v-col>
        <v-col offset-sm="0" sm="12" offset="1" cols="10">
          <h1 class="font-weight-bold text-center">Page not found</h1>
        </v-col>
      </v-row>
    </content-card>
    <template v-if="$vuetify.breakpoint.mdAndUp"> </template>
    <template v-else> </template>
  </v-container>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import FourZeroFourDisplay from '@/components/navigation/FourZeroFourDisplay.vue'

export default {
  name: 'PageNotFound',
  components: {
    FourZeroFourDisplay,
    ContentCard,
  },
  computed: {
    user() {
      //TODO: Use getter
      return this.$store.state.auth.user
    },
  },
}
</script>

<style scoped lang="scss">
@media #{map-get($display-breakpoints, 'lg-and-down')} {
  h1 {
    font-size: 10rem;
  }
}
@media #{map-get($display-breakpoints, 'md-and-down')} {
  h1 {
    font-size: 8rem;
  }
}
.contentCard > * {
  background-color: lightgrey !important;
}
</style>
