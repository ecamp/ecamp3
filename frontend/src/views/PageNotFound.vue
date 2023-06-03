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

      <v-card-text class="justify-center">
        <p id="error-title" class="font-weight-bold text-center px-8 text-h4">
          {{ $tc('views.pageNotFound.detail') }}
        </p>
      </v-card-text>
      <v-card-actions class="justify-center">
        <icon-button
          v-if="hasHistory"
          class="mr-5"
          icon="mdi-arrow-left"
          @click="$router.go(-1)"
          >{{ $tc('views.pageNotFound.back') }}</icon-button
        >
        <icon-button icon="mdi-tent" @click="$router.push({ name: 'camps' })">{{
          $tc('views.pageNotFound.goToCamps')
        }}</icon-button>
        <v-icon size="32">$vuetify.icons.four</v-icon>


      </v-card-actions>
      <!--v-row>
        <v-col
          offset="0"
          offset-sm="1"
          offset-md="2"
          offset-lg="2"
          offset-xl="2"
          cols="12"
          sm="10"
          md="8"
          lg="8"
          xl="8"
        >
        </v-col>
        <v-col offset-sm="0" sm="12" offset="2" cols="8">
          <h1 class="font-weight-bold text-center px-8">
            {{ $tc('views.pageNotFound.detail') }}
          </h1>
        </v-col>
        <v-col class="justify-center d-flex">
          <icon-button
            v-if="hasHistory"
            class="mr-5"
            icon="mdi-arrow-left"
            @click="$router.go(-1)"
            >{{ $tc('views.pageNotFound.back') }}</icon-button
          >
          <icon-button icon="mdi-tent" @click="$router.push({ name: 'camps' })">{{
            $tc('views.pageNotFound.goToCamps')
          }}</icon-button>
        </v-col>
      </v-row-->
    </content-card>
  </v-container>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import UserAvatar from '@/components/user/UserAvatar.vue'
import IconButton from '@/components/buttons/IconButton.vue'
import FourZeroFourImage from '@/assets/FourZeroFourImage.svg'

export default {
  name: 'PageNotFound',
  components: {
    FourZeroFourImage,
    UserAvatar,
    ContentCard,
    IconButton,
  },
  data: () => {
    return {
      hasHistory: false,
    }
  },
  computed: {
    user() {
      //TODO: Use getter
      return this.$store.state.auth.user
    },
  },
  mounted() {
    if (window.history.length && window.history.length >= 1) {
      this.hasHistory = true
    }
  },
}
</script>

<style scoped lang="scss">
#ttt {
}
#error-title {
  font-size: 2em;
}
@media #{map-get($display-breakpoints, 'lg-and-down')} {
  #error-title {
    font-size: 3em;
  }
}
@media #{map-get($display-breakpoints, 'md-and-down')} {
  #error-title {
    font-size: 2em;
  }
}
</style>
