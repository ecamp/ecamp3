<template>
  <v-container fluid>
    <content-card
      class="contentCard"
      :title="$tc('views.pageNotFound.title')"
      max-width="1200"
      toolbar
    >
      <template #title-actions>
        <v-btn
          class="d-md-none"
          icon
          :to="{ name: 'profile', query: { isDetail: true } }"
        >
          <user-avatar :user="user" :size="36" />
        </v-btn>
      </template>

      <v-row>
        <v-col
          offset="0"
          offset-sm="1"
          offset-md="1"
          offset-lg="2"
          offset-xl="2"
          cols="12"
          sm="10"
          md="10"
          lg="8"
          xl="8"
        >
          <four-zero-four-display />
        </v-col>
        <v-col offset-sm="0" sm="12" offset="1" cols="10">
          <h1 class="font-weight-bold text-center">
            {{ $tc('views.pageNotFound.detail') }}
          </h1>
        </v-col>
      </v-row>
    </content-card>
  </v-container>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import FourZeroFourDisplay from '@/components/navigation/FourZeroFourDisplay.vue'
import UserAvatar from '@/components/user/UserAvatar.vue'

export default {
  name: 'PageNotFound',
  components: {
    FourZeroFourDisplay,
    UserAvatar,
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
h1 {
  font-size: 4em;
}
@media #{map-get($display-breakpoints, 'lg-and-down')} {
  h1 {
    font-size: 3em;
  }
}
@media #{map-get($display-breakpoints, 'md-and-down')} {
  h1 {
    font-size: 2em;
  }
}
</style>
