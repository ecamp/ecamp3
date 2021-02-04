<template>
  <v-container fluid>
    <content-card max-width="800" :title="$tc('views.profile.profile') + ': ' + profile.displayName" toolbar>
      <v-col>
        <api-text-field
          :name="$tc('entity.user.fields.email')"
          :uri="profile._meta.self"
          fieldname="mail"
          :editing="false"
          required />
      </v-col>
      <v-col>
        <api-text-field
          :name="$tc('entity.user.fields.firstname')"
          :uri="profile._meta.self"
          fieldname="firstname" />
        <api-text-field
          :name="$tc('entity.user.fields.surname')"
          :uri="profile._meta.self"
          fieldname="surname" />
        <api-text-field
          :name="$tc('entity.user.fields.nickname')"
          :uri="profile._meta.self"
          fieldname="nickname" />
        <api-date-picker
          :name="$tc('entity.user.fields.birthday')"
          :uri="profile._meta.self"
          :value-format="this.$date.HTML5_FMT.DATE"
          fieldname="birthday" />
        <api-select
          :name="$tc('entity.user.fields.language') + ' (' + $tc('global.lokaliseMessage') + ')'"
          :uri="profile._meta.self"
          fieldname="language"
          :items="availableLocales" />
      </v-col>
      <v-col>
        <v-btn v-if="$vuetify.breakpoint.xsOnly" color="red"
               block
               large
               dark
               @click="$auth.logout()">
          {{ $tc('global.button.logout') }}
        </v-btn>
      </v-col>
    </content-card>
  </v-container>
</template>

<script>
import ApiSelect from '@/components/form/api/ApiSelect'
import ApiTextField from '@/components/form/api/ApiTextField'
import ApiDatePicker from '@/components/form/api/ApiDatePicker'
import ContentCard from '@/components/layout/ContentCard'
import VueI18n from '@/plugins/i18n'

export default {
  name: 'Home',
  components: {
    ApiSelect,
    ApiTextField,
    ApiDatePicker,
    ContentCard
  },
  computed: {
    profile () {
      return this.api.get().profile()
    },
    availableLocales () {
      return VueI18n.availableLocales.map(l => ({
        value: l,
        text: this.$tc('global.language', 1, l)
      }))
    }
  },
  watch: {
    profile () {
      if (VueI18n.availableLocales.includes(this.profile.language)) {
        this.$store.commit('setLanguage', this.profile.language)
      }
    }
  }
}
</script>

<style lang="scss" scoped>
</style>
