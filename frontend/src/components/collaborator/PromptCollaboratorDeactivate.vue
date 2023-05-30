<template>
  <PopoverPrompt
    v-model="showDialog"
    icon="mdi-cancel"
    :title="$tc('components.collaborator.promptCollaboratorDeactivate.title')"
    :error="error"
    :submit-action="deactivateUser"
    :submit-enabled="!$slots.error"
    :submit-label="$tc('components.collaborator.promptCollaboratorDeactivate.deactivate')"
    submit-color="error"
    submit-icon="mdi-cancel"
    cancel-icon=""
    :cancel-action="close"
    v-bind="$attrs"
  >
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>
    <slot>
      {{
        $tc('components.collaborator.promptCollaboratorDeactivate.warningText', 1, {
          name: displayName,
        })
      }}
    </slot>
    <template v-if="$slots.error || error" #error>
      <slot name="error">
        {{ error }}
      </slot>
    </template>
  </PopoverPrompt>
</template>

<script>
import DialogBase from '@/components/dialog/DialogBase.vue'
import campCollaborationDisplayName from '@/common/helpers/campCollaborationDisplayName.js'
import { errorToMultiLineToast } from '@/components/toast/toasts'
import PopoverPrompt from '@/components/prompt/PopoverPrompt.vue'

export default {
  name: 'PromptCollaboratorDeactivate',
  components: { PopoverPrompt },
  extends: DialogBase,
  props: {
    entity: { type: Object, required: true },
  },
  computed: {
    isOwnCampCollaboration() {
      if (!(typeof this.entity.user === 'function')) {
        return false
      }
      return this.$store.state.auth.user?.id === this.entity.user().id
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
      this.error = null
      const promise = this.api
        .patch(this.entity, { status: 'inactive' })
        .catch((e) => this.$toast.error(errorToMultiLineToast(e)))

      // User left camp -> navigate to camp-overview
      promise.then(
        () => this.isOwnCampCollaboration && this.$router.push({ name: 'camps' })
      )

      return promise
    },
  },
}
</script>

<style scoped></style>
