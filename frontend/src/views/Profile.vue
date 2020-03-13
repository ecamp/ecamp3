<template>
  <v-container fluid>
    <content-card max-width="600">
      <template v-slot:title>
        <v-card-title>
          <back-button />
          {{ 'Profil: ' + profile.username }}
        </v-card-title>
      </template>
      <v-col>
        <api-input
          label="Email"
          :uri="profile._meta.self"
          fieldname="mail"
          :value="profile.mail"
          :editing="false"
          required />
      </v-col>
    </content-card>
  </v-container>
</template>

<script>
export default {
  name: 'Home',
  components: {
    ApiInput: () => import('@/components/form/ApiInput'),
    ContentCard: () => import('@/components/base/ContentCard'),
    BackButton: () => import('@/components/base/BackButton')
  },
  computed: {
    profile () {
      return this.api.get().profile()
    }
  }
}
</script>

<style lang="scss" scoped>
</style>
