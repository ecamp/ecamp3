<template>
  <v-container fluid>
    <content-card max-width="600">
      <template v-slot:title>
        <v-card-title>
          <button-back />
          {{ $t('views.profile.profile') + ': ' + profile.displayName }}
        </v-card-title>
      </template>
      <v-col>
        <api-text-field
          label="Email"
          :uri="profile._meta.self"
          fieldname="mail"
          :editing="false"
          required />
      </v-col>
      <v-col>
        <api-text-field
          :label="$t('entity.user.fields.firstname')"
          :uri="profile._meta.self"
          fieldname="firstname" />
        <api-text-field
          :label="$t('entity.user.fields.surname')"
          :uri="profile._meta.self"
          fieldname="surname" />
        <api-text-field
          :label="$t('entity.user.fields.nickname')"
          :uri="profile._meta.self"
          fieldname="nickname" />
        <api-select
          :label="$t('entity.user.fields.language')"
          :uri="profile._meta.self"
          fieldname="language"
          :items="availableLocales" />
      </v-col>
    </content-card>
  </v-container>
</template>

<script>
import ApiTextField from '@/components/form/api/ApiTextField'
import ContentCard from '@/components/layout/ContentCard'
import ButtonBack from '@/components/buttons/ButtonBack'
import ApiSelect from '@/components/form/api/ApiSelect'
import VueI18n from '@/plugins/i18n'

export default {
  name: 'Home',
  components: {
    ApiSelect,
    ApiTextField,
    ContentCard,
    ButtonBack
  },
  computed: {
    profile () {
      return this.api.get().profile()
    },
    availableLocales () {
      return VueI18n.availableLocales.map(l => ({
        value: l,
        text: this.$i18n.t('global.language', l)
      }))
    }
  },
  watch: {
    profile () {
      if (VueI18n.availableLocales.includes(this.profile.language)) {
        VueI18n.locale = this.profile.language
      }
    }
  }
}
</script>

<style lang="scss" scoped>
</style>
