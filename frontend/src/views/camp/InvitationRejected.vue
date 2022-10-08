<template>
  <auth-container>
    <v-alert type="info">
      {{ $tc('components.invitation.successfullyRejected') }}
    </v-alert>
    <div class="mb-4 mt-8">
      <router-link color="primary" x-large block :to="{ name: 'home' }">
        {{ $tc('components.invitation.backToHome') }}
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
