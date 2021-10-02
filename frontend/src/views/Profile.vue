<template>
  <v-container fluid>
    <content-card max-width="800" :title="$tc('views.profile.profile') + ': ' + user.displayName" toolbar>
      <v-col>
        <v-skeleton-loader type="text" :loading="user._meta.loading">
          <api-text-field
            :name="$tc('entity.user.fields.email')"
            :uri="user._meta.self"
            fieldname="email"
            :editing="false"
            required />
          <api-text-field
            :name="$tc('entity.user.fields.firstname')"
            :uri="user._meta.self"
            fieldname="firstname" />
          <api-text-field
            :name="$tc('entity.user.fields.surname')"
            :uri="user._meta.self"
            fieldname="surname" />
          <api-text-field
            :name="$tc('entity.user.fields.nickname')"
            :uri="user._meta.self"
            fieldname="nickname" />
          <api-select
            :name="$tc('entity.user.fields.language')"
            :uri="user._meta.self"
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
        </v-skeleton-loader>
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
    user () {
      return this.$auth.user()
    },
    availableLocales () {
      return VueI18n.availableLocales.map(l => ({
        value: l,
        text: this.$tc('global.language', 1, l)
      }))
    }
  },
  watch: {
    user () {
      if (VueI18n.availableLocales.includes(this.user.language)) {
        this.$store.commit('setLanguage', this.user.language)
      }
    }
  },
  mounted () {
    this.api.reload(this.user)
  }
}
</script>

<style lang="scss" scoped>
</style>
