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
        <api-select
          :name="$tc('entity.user.fields.language')"
          :uri="profile._meta.self"
          fieldname="language"
          :items="availableLocales" />
        <p class="text-caption blue-grey--text mb-0">
          {{ $tc('global.lokaliseMessage') }}
        </p>
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
import ApiSelect from '@/components/form/api/ApiSelect.vue'
import ApiTextField from '@/components/form/api/ApiTextField.vue'
import ContentCard from '@/components/layout/ContentCard.vue'
import VueI18n from '@/plugins/i18n'

export default {
  name: 'Home',
  components: {
    ApiSelect,
    ApiTextField,
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
  },
  mounted () {
    this.api.reload(this.profile)
  }
}
</script>

<style lang="scss" scoped>
</style>
