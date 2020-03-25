<template>
  <v-container fluid>
    <content-card max-width="600">
      <template v-slot:title>
        <v-card-title>
          <button-back />
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
import ApiTextField from '@/components/form/api/ApiTextField'
import ContentCard from '@/components/layout/ContentCard'
import ButtonBack from '@/components/buttons/ButtonBack'

export default {
  name: 'Home',
  components: {
    ApiTextField,
    ContentCard,
    ButtonBack
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
