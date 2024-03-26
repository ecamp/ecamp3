<template>
  <v-container fluid>
    <content-card
      v-if="user"
      max-width="800"
      :title="
        $tc('views.profile.profile') + (user._meta.loading ? '' : ': ' + user.displayName)
      "
      toolbar
    >
      <template #title-actions>
        <UserMeta v-if="!$vuetify.breakpoint.mdAndUp" avatar-only btn-classes="mr-n4" />
      </template>
      <v-col>
        <v-skeleton-loader type="text" :loading="profile._meta.loading">
          <e-text-field
            class="e-profile--email"
            :name="$tc('entity.user.fields.email')"
            :value="profile.email"
            fieldname="email"
            outlined
            :filled="false"
            readonly
            required
          >
            <template #append>
              <dialog-change-mail>
                <template #activator="{ on }">
                  <ButtonEdit text class="v-btn--has-bg" v-on="on">
                    {{ $tc('views.profile.changeEmail') }}
                  </ButtonEdit>
                </template>
              </dialog-change-mail>
            </template>
          </e-text-field>
          <api-text-field
            :name="$tc('entity.user.fields.firstname')"
            :uri="profile._meta.self"
            fieldname="firstname"
            @finished="reloadUser()"
          />
          <api-text-field
            :name="$tc('entity.user.fields.surname')"
            :uri="profile._meta.self"
            fieldname="surname"
            @finished="reloadUser()"
          />
          <api-text-field
            :name="$tc('entity.user.fields.nickname')"
            :uri="profile._meta.self"
            fieldname="nickname"
            @finished="reloadUser()"
          />
          <api-select
            :name="$tc('entity.user.fields.language')"
            :uri="profile._meta.self"
            fieldname="language"
            :items="availableLocales"
          />
          <v-btn
            v-if="!$vuetify.breakpoint.mdAndUp"
            class="mt-2"
            color="red"
            block
            large
            dark
            @click="$auth.logout()"
          >
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
import { mapGetters } from 'vuex'
import ButtonEdit from '@/components/buttons/ButtonEdit.vue'

export default {
  name: 'Home',
  components: {
    ButtonEdit,
    ApiSelect,
    ApiTextField,
    ContentCard,
    DialogChangeMail,
    DialogChangeMailRunning,
  },
  props: {
    emailVerificationKey: { type: String, required: false, default: null },
  },
  computed: {
    ...mapGetters({
      user: 'getLoggedInUser',
    }),
    profile() {
      return this.user?.profile()
    },
    availableLocales() {
      return VueI18n.availableLocales.map((l) => ({
        value: l,
        text: this.$tc('global.language', 1, l),
      }))
    },
  },
  watch: {
    profile() {
      if (VueI18n.availableLocales.includes(this.profile?.language)) {
        this.$store.commit('setLanguage', this.profile?.language)
      }
    },
  },
  methods: {
    reloadUser() {
      this.api.reload(this.user)
    },
  },
}
</script>

<style lang="scss" scoped>
.e-profile--email ::v-deep .v-input__append-inner {
  margin-top: 0 !important;
  align-self: center;
}
</style>
