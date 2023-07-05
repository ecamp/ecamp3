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
    v-bind="$attrs"
  >
    <template #activator="scope">
      <slot name="activator" v-bind="scope">
        <ButtonDelete v-bind="{ ...scope.attrs, ...btnAttrs }" v-on="scope.on" />
      </slot>
    </template>
    <slot>{{
      $tc('components.prompt.promptEntityDelete.warningText', warningTextEntity ? 2 : 0, {
        entity: warningTextEntity,
      })
    }}</slot>
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
import ButtonDelete from '@/components/buttons/ButtonDelete.vue'

export default {
  name: 'PromptEntityDelete',
  components: { ButtonDelete, PopoverPrompt },
  extends: DialogBase,
  props: {
    entity: { type: Object, required: true },
    submitEnabled: { type: Boolean, required: false, default: true },
    icon: { type: String, required: false, default: 'mdi-delete' },
    warningTextEntity: { type: String, required: false, default: '' },
    btnAttrs: { type: Object, required: false, default: () => {} },
  },
  created() {
    this.entityUri = this.entity._meta.self
  },
}
</script>

<style scoped></style>
