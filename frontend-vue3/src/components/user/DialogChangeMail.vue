<template>
  <dialog-form
    v-model="showDialog"
    :title="$t('components.user.dialogChangeMail.title')"
    :submit-action="status === 'initial' ? sendChangeMailRequest : null"
    :cancel-action="close"
    :cancel-label="
      status === 'initial' ? $t('global.button.cancel') : $t('global.button.close')
    "
    submit-color="success"
  >
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>
    <e-form v-if="status === 'initial'" name="profile">
      <e-text-field
        v-model="entityData.newEmail"
        path="email"
        vee-rules="email|required"
        append-icon="mdi-at"
        autofocus
      />
      <p class="mt-5">
        {{ $t('components.user.dialogChangeMail.message') }}
      </p>
    </e-form>
    <div v-if="status == 'success'">
      {{ $t('components.user.dialogChangeMail.success') }}
    </div>
    <div v-if="status == 'error'">
      {{ $t('components.user.dialogChangeMail.error') }}
    </div>
  </dialog-form>
</template>

<script>
import DialogForm from '@/components/dialog/DialogForm.vue'
import DialogBase from '@/components/dialog/DialogBase.vue'

export default {
  name: 'DialogChangeMail',
  components: { DialogForm },
  extends: DialogBase,
  data() {
    return {
      status: '',
      entityProperties: ['newEmail'],
    }
  },
  watch: {
    // copy data whenever dialog is opened
    showDialog: function (showDialog) {
      if (showDialog) {
        this.status = 'initial'
        this.loadEntityData(this.$store.getters.getLoggedInUser.profile()._meta.self)
      }
    },
  },
  methods: {
    async sendChangeMailRequest() {
      await this.api
        .patch(this.entityUri, this.entityData)
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
