<template>
  <auth-container>
    <v-alert type="error">
      {{ $tc('views.camp.invitationUpdateError.error') }}
    </v-alert>
    <div class="mb-4 mt-8">
      <router-link color="primary" x-large block :to="{ name: 'home' }">
        {{ $tc('views.camp.invitationUpdateError.backToHome') }}
      </router-link>
    </div>
  </auth-container>
</template>

<script>
import AuthContainer from '@/components/layout/AuthContainer.vue'
export default {
  name: 'InvitationUpdateError',
  components: { AuthContainer },
}
</script>

<style scoped></style>
