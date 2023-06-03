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
        <v-img contain class="error-image" src="/public/fourZeroFour.svg"></v-img>
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
        <!-- This doesnt work for some reason: -->
        <v-icon size="32">$vuetify.icons.fourZeroFour</v-icon>
      </v-card-actions>
    </content-card>
  </v-container>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import UserAvatar from '@/components/user/UserAvatar.vue'
import IconButton from '@/components/buttons/IconButton.vue'

export default {
  name: 'PageNotFound',
  components: {
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
.error-image {
  padding: 32px;
  margin: auto;
  max-width: 640px;
}
</style>
