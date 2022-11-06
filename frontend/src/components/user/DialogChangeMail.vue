<template>
  <dialog-form
    v-model="showDialog"
    :title="$tc('components.user.dialogChangeMail.title')"
    :submit-action="status === 'initial' ? sendChangeMailRequest : null"
    :cancel-action="close"
    cancel-label="global.button.close"
    :cancel-visible="status !== 'initial'"
    submit-color="success"
  >
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>
    <div v-if="status === 'initial'">
      <e-text-field
        v-model="entityData.newEmail"
        :name="$tc('entity.user.fields.email')"
        vee-rules="email|required"
        append-icon="mdi-at"
        autofocus
      />
      <p class="mt-5">
        {{ $tc('components.user.dialogChangeMail.message') }}
      </p>
    </div>
    <div v-if="status == 'success'">
      {{ $tc('components.user.dialogChangeMail.success') }}
    </div>
    <div v-if="status == 'error'">
      {{ $tc('components.user.dialogChangeMail.error') }}
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
        this.loadEntityData(this.$store.state.auth.user.profile()._meta.self)
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
