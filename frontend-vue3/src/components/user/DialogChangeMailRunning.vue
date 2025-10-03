<template>
  <dialog-form
    v-model="showDialog"
    :title="$t('components.user.dialogChangeMailRunning.title')"
    :cancel-action="status === 'initial' ? null : close"
    :cancel-label="$t('global.button.close')"
  >
    <div v-if="status === 'initial'">
      <v-progress-circular indeterminate />
      {{ $t('components.user.dialogChangeMailRunning.message') }}
    </div>
    <div v-if="status === 'success'">
      <v-icon>mdi mdi-check</v-icon>
      {{ $t('components.user.dialogChangeMailRunning.success') }}
    </div>
    <div v-if="status === 'error'">
      {{ $t('components.user.dialogChangeMailRunning.error') }}
    </div>
  </dialog-form>
</template>

<script>
import DialogForm from '@/components/dialog/DialogForm.vue'
import DialogBase from '@/components/dialog/DialogBase.vue'

export default {
  name: 'DialogChangeMailRunning',
  components: { DialogForm },
  extends: DialogBase,
  props: {
    emailVerificationKey: { type: String, required: false, default: null },
  },
  data() {
    return {
      status: '',
    }
  },
  watch: {
    showDialog: function (showDialog) {
      if (!showDialog) {
        this.$router.push({ name: 'profile' })
      }
    },
  },
  mounted() {
    if (this.emailVerificationKey) {
      this.status = 'initial'
      this.showDialog = true

      this.$auth.loadUser().then((u) => {
        u.profile()._meta.load.then((p) => {
          p.$patch({
            untrustedEmailKey: this.emailVerificationKey,
          })
            .then(() => {
              this.status = 'success'
            })
            .catch(() => {
              this.status = 'error'
            })
        })
      })
    }
  },
}
</script>
