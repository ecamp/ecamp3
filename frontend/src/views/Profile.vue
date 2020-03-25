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
        <api-text-field
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
    ApiTextField: () => import('@/components/form/ApiTextField'),
    ContentCard: () => import('@/components/layout/ContentCard'),
    BackButton: () => import('@/components/buttons/ButtonBack')
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
