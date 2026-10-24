<template>
  <dialog-form
    v-model="showDialog"
    icon="mdi-lock-outline"
    :title="$t('components.user.dialogChangePassword.title')"
    :submit-action="status === 'initial' ? changePassword : null"
    :cancel-action="close"
    :cancel-label="
      status === 'initial' ? $t('global.button.cancel') : $t('global.button.close')
    "
    submit-color="success"
  >
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>
    <e-form v-if="status === 'initial'" name="user">
      <e-password-field
        v-model="currentPassword"
        :label="$t('components.user.dialogChangePassword.currentPassword')"
        autocomplete="current-password"
        autofocus
        path="currentPassword"
        vee-rules="required"
      />

      <e-password-field
        v-model="newPassword"
        :label="$t('components.user.dialogChangePassword.newPassword')"
        autocomplete="new-password"
        :density="$vuetify.display.xs ? 'comfortable' : undefined"
        loading
        maxlength="128"
        minlength="12"
        passwordrules="minlength: 12; maxlength: 128;"
        path="password"
        validate-on-blur
        vee-rules="required|min:12|max:128"
        @input="(event) => debouncedPasswordStrengthCheck(event.target.value)"
      >
        <template #loader>
          <v-progress-linear
            :color="passwordStrengthColor"
            :model-value="passwordStrength"
            height="5"
          />
        </template>
      </e-password-field>

      <e-password-field
        v-model="newPasswordConfirmation"
        :label="$t('components.user.dialogChangePassword.newPasswordConfirmation')"
        autocomplete="new-password"
        maxlength="128"
        minlength="12"
        passwordrules="minlength: 12; maxlength: 128;"
        path="passwordConfirmation"
        validate-on-blur
        vee-rules="required|confirmed:@password"
      />
    </e-form>
    <p v-if="status == 'success'">
      {{ $t('components.user.dialogChangePassword.success') }}
    </p>
    <template v-if="status == 'error'" #error>
      {{ $t('components.user.dialogChangePassword.error') }}
    </template>
  </dialog-form>
</template>

<script>
import DialogForm from '@/components/dialog/DialogForm.vue'
import DialogBase from '@/components/dialog/DialogBase.vue'
import { passwordStrengthMixin } from '@/mixins/passwordStrengthMixin.js'

export default {
  name: 'DialogChangePassword',
  components: { DialogForm },
  extends: DialogBase,
  mixins: [passwordStrengthMixin],
  data() {
    return {
      status: '',
      currentPassword: '',
      newPassword: '',
      newPasswordConfirmation: '',
    }
  },
  watch: {
    // reset form whenever dialog is opened
    showDialog: function (showDialog) {
      if (showDialog) {
        this.status = 'initial'
        this.currentPassword = ''
        this.newPassword = ''
        this.newPasswordConfirmation = ''
        this.passwordStrength = 0
        this.loading = false
      }
    },
  },
  methods: {
    async changePassword() {
      const user = this.$store.getters.getLoggedInUser
      await this.api
        .patch(user._meta.self, {
          password: this.newPassword,
          currentPassword: this.currentPassword,
        })
        .then(() => {
          this.status = 'success'
        })
        .catch(() => {
          this.status = 'error'
        })
    },
  },
}
</script>
