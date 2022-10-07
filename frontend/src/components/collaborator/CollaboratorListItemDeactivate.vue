<template>
  <dialog-form
    v-model="showDialog"
    icon="mdi-cancel"
    :title="$tc('components.camp.collaboratorListItemDeactivate.title')"
    :error="error"
    :submit-action="deactivateUser"
    :submit-enabled="!$slots.error"
    submit-label="components.camp.collaboratorListItemDeactivate.deactivate"
    submit-color="error"
    submit-icon="mdi-cancel"
    cancel-icon=""
    :cancel-action="close"
  >
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>
    <slot>
      {{
        $tc('components.camp.collaboratorListItemDeactivate.warningText', 1, {
          name: displayName,
        })
      }}
    </slot>
    <template v-if="$slots.error || error" #error>
      <slot name="error">
        {{ error }}
      </slot>
    </template>
  </dialog-form>
</template>

<script>
import DialogForm from '@/components/dialog/DialogForm.vue'
import DialogBase from '@/components/dialog/DialogBase.vue'
import campCollaborationDisplayName from '@/common/helpers/campCollaborationDisplayName.js'

export default {
  name: 'CollaboratorListItemDeactivate',
  components: { DialogForm },
  extends: DialogBase,
  props: {
    entity: { type: Object, required: true },
  },
  computed: {
    isOwnCampCollaboration() {
      if (!(typeof this.entity.user === 'function')) {
        return false
      }
      return this.$store.state.auth.user.id === this.entity.user().id
    },
    displayName() {
      return campCollaborationDisplayName(this.entity, this.$tc.bind(this))
    },
  },
  created() {
    this.entityUri = this.entity._meta.self
  },
  methods: {
    deactivateUser() {
      const ok = this.api.patch(this.entity, { status: 'inactive' })

      if (this.isOwnCampCollaboration) {
        // User left camp -> navigate to camp-overview
        ok.then(() => this.$router.push({ name: 'camps' }))
      }
    },
  },
}
</script>

<style scoped></style>
