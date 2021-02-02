<template>
  <auth-container>
    <v-alert type="info">
      {{ this.$tc('components.invitation.successfullyRejected') }}
    </v-alert>
    <v-btn color="primary"
           x-large
           class="my-4" block
           :to="{ name: 'home' }">
      {{ this.$tc('components.invitation.backToHome') }}
    </v-btn>
  </auth-container>
</template>

<script>
import AuthContainer from '@/components/layout/AuthContainer'
export default {
  name: 'InvitationRejected',
  components: { AuthContainer }
}
</script>

<style scoped>

</style>
