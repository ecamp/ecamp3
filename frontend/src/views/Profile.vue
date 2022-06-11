<template>
  <v-container fluid>
    <content-card max-width="800" :title="$tc('views.profile.profile') + ': ' + user.displayName" toolbar>
      <v-col>
        <v-skeleton-loader type="text" :loading="profile._meta.loading">
          <v-row class="e-form-container">
            <v-col cols="10">
              <api-text-field
                :name="$tc('entity.user.fields.email')"
                :uri="profile._meta.self"
                fieldname="email"
                :editing="false"
                required />
            </v-col>
            <v-col cols="2">
              <dialog-change-mail>
                <template #activator="{ on }">
                  <v-btn block outlined v-on="on">
                    <v-icon left>
                      mdi-pencil
                    </v-icon>
                    {{ $tc('views.profile.changeEmail') }}
                  </v-btn>
                </template>
              </dialog-change-mail>
            </v-col>
          </v-row>
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
        </v-skeleton-loader>
      </v-col>
    </content-card>

    <dialog-change-mail-running :email-verification-key="emailVerificationKey" />
  </v-container>
</template>

<script>
import ApiSelect from '@/components/form/api/ApiSelect.vue'
import ApiTextField from '@/components/form/api/ApiTextField.vue'
import ContentCard from '@/components/layout/ContentCard.vue'
import DialogChangeMail from '@/components/user/DialogChangeMail.vue'
import DialogChangeMailRunning from '@/components/user/DialogChangeMailRunning.vue'
import VueI18n from '@/plugins/i18n'

export default {
  name: 'Home',
  components: {
    ApiSelect,
    ApiTextField,
    ContentCard,
    DialogChangeMail,
    DialogChangeMailRunning
  },
  props: {
    emailVerificationKey: { type: String, required: false, default: null }
  },
  computed: {
    user () {
      return this.$auth.user()
    },
    profile () {
      return this.user.profile()
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
      if (VueI18n.availableLocales.includes(this.profile.language)) {
        this.$store.commit('setLanguage', this.profile.language)
      }
    }
  },
  mounted () {
    this.api.reload(this.user)
      .then(user => this.api.reload(user.profile()))
  }
}
</script>

<style lang="scss" scoped>
</style>
