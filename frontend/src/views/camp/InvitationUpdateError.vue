<template>
  <auth-container>
    <v-alert type="error">
      {{ $tc('components.invitation.error') }}
    </v-alert>
    <v-btn color="primary" x-large class="my-4" block :to="{ name: 'home' }">
      {{ $tc('components.invitation.backToHome') }}
    </v-btn>
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
