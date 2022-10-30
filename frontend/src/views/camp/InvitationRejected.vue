<template>
  <auth-container>
    <v-alert type="info">
      {{ $tc('views.camp.invitationRejected.successfullyRejected') }}
    </v-alert>
    <div class="mb-4 mt-8">
      <router-link color="primary" x-large block :to="{ name: 'home' }">
        {{ $tc('views.camp.invitationRejected.backToHome') }}
      </router-link>
    </div>
  </auth-container>
</template>

<script>
import AuthContainer from '@/components/layout/AuthContainer.vue'
export default {
  name: 'InvitationRejected',
  components: { AuthContainer },
}
</script>

<style scoped></style>
