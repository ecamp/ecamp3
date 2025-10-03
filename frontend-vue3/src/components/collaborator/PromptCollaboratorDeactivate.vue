<template>
  <PopoverPrompt
    v-model="showDialog"
    v-bind="$attrs"
    type="error"
    :error="error"
    :submit-action="deactivateUser"
    :submit-enabled="!$slots.error"
    :submit-label="$t('components.collaborator.promptCollaboratorDeactivate.deactivate')"
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
        $t('components.collaborator.promptCollaboratorDeactivate.warningText', 1, {
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
import isOwnCampCollaboration from './isOwnCampCollaboration.js'

export default {
  name: 'PromptCollaboratorDeactivate',
  components: { PopoverPrompt },
  extends: DialogBase,
  props: {
    entity: { type: Object, required: true },
  },
  computed: {
    isOwnCampCollaboration() {
      return isOwnCampCollaboration(this.entity, this.$store.state.auth)
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
      promise.then(() => {
        if (!this.isOwnCampCollaboration) {
          return
        }
        this.api.get().camps().$reload()
        this.$router.push({ name: 'camps' })
      })

      return promise
    },
  },
}
</script>

<style scoped></style>
