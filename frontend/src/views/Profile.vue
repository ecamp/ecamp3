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
          <api-form :entity="profile" name="profile">
            <e-text-field
              class="e-profile--email"
              :value="profile.email"
              path="email"
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

            <api-text-field path="firstname" @finished="reloadUser()" />

            <api-text-field path="surname" @finished="reloadUser()" />

            <api-text-field path="nickname" @finished="reloadUser()" />

            <api-text-field
              path="abbreviation"
              vee-rules="oneEmojiOrTwoCharacters"
              @finished="reloadUser()"
            />

            <api-color-picker path="color" @finished="reloadUser()" />

            <api-select path="language" :items="availableLocales" />
          </api-form>
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
import ApiForm from '@/components/form/api/ApiForm.vue'
import ApiColorPicker from '@/components/form/api/ApiColorPicker.vue'

export default {
  name: 'Home',
  components: {
    ApiColorPicker,
    ApiForm,
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
.e-profile--email ::v-deep(.v-input__append-inner) {
  margin-top: 0 !important;
  align-self: center;
}
</style>
