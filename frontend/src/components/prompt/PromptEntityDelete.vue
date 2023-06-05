<template>
  <PopoverPrompt
    v-model="showDialog"
    :icon="icon"
    :title="$tc('components.prompt.promptEntityDelete.title')"
    :error="error"
    :submit-action="del"
    :submit-enabled="submitEnabled && !$slots.error"
    :submit-label="$tc('global.button.delete')"
    submit-color="error"
    :submit-icon="icon"
    cancel-icon=""
    :cancel-action="close"
  >
    <template #activator="scope">
      <slot name="activator" v-bind="scope" />
    </template>
    <slot>{{ $tc('components.prompt.promptEntityDelete.warningText') }}</slot>
    <template v-if="$slots.error || error" #error>
      <slot name="error">
        {{ error }}
      </slot>
    </template>
  </PopoverPrompt>
</template>

<script>
import DialogBase from '../dialog/DialogBase.vue'
import PopoverPrompt from '@/components/prompt/PopoverPrompt.vue'

export default {
  name: 'PromptEntityDelete',
  components: { PopoverPrompt },
  extends: DialogBase,
  props: {
    entity: { type: Object, required: true },
    submitEnabled: { type: Boolean, required: false, default: true },
    icon: { type: String, required: false, default: 'mdi-delete' },
  },
  created() {
    this.entityUri = this.entity._meta.self
  },
}
</script>

<style scoped></style>
