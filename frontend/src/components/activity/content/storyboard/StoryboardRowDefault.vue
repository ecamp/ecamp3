<template>
  <tr class="e-storyboard-row e-storyboard-row--default">
    <td v-if="!layoutMode" class="e-storyboard-row__handle">
      <v-btn
        icon="mdi-drag"
        size="small"
        class="drag-and-drop-handle"
        :disabled="isLastSection"
        variant="flat"
        density="comfortable"
        :aria-label="$t('global.button.move')"
        @keydown.down="$emit('moveDown', itemKey)"
        @keydown.up="$emit('moveUp', itemKey)"
      >
        <v-icon icon="mdi-drag" size="24" />
      </v-btn>
    </td>
    <td class="e-storyboard-row__time">
      <api-text-field
        :label="$t('contentNode.storyboard.entity.section.fields.column1')"
        single-line
        :path="`data.sections[${itemKey}].column1`"
        :disabled="layoutMode || disabled"
      />
    </td>
    <td class="e-storyboard-row__text">
      <api-richtext
        :label="$t('contentNode.storyboard.entity.section.fields.column2Html')"
        :path="`data.sections[${itemKey}].column2Html`"
        rows="4"
        :disabled="layoutMode || disabled"
      />
    </td>
    <td class="e-storyboard-row__responsible">
      <api-text-field
        :label="$t('contentNode.storyboard.entity.section.fields.column3')"
        single-line
        :path="`data.sections[${itemKey}].column3`"
        :disabled="layoutMode || disabled"
      />
    </td>
    <td v-if="!layoutMode && !disabled" class="e-storyboard-row__controls">
      <dialog-remove-section @submit="$emit('delete', itemKey)">
        <template #activator="{ props }">
          <v-btn
            icon="mdi-delete-outline"
            variant="text"
            size="small"
            density="comfortable"
            class="e-storyboard-row__delete"
            color="error"
            :disabled="isLastSection"
            v-bind="props"
          >
            <v-icon icon="mdi-delete-outline" size="24" />
          </v-btn>
        </template>
      </dialog-remove-section>
    </td>
  </tr>
</template>
<script>
import DialogRemoveSection from './StoryboardDialogRemoveSection.vue'

export default {
  name: 'StoryboardRowDefault',
  components: { DialogRemoveSection },
  props: {
    isLastSection: { type: Boolean, required: true },
    itemKey: { type: String, required: true },
    layoutMode: { type: Boolean, required: true },
    disabled: { type: Boolean, default: false },
  },
  emits: ['moveDown', 'moveUp', 'delete'],
}
</script>
<style scoped lang="scss">
.e-storyboard-row__delete {
  color: rgba(0, 0, 0, 0.54) !important;

  &:hover {
    color: #d32f2f !important;
  }
}

.e-storyboard-row--default {
  vertical-align: baseline;

  .e-storyboard-row__time {
    width: 15%;
    padding-right: 0.5rem;
    padding-bottom: 0.5rem;
  }

  .e-storyboard-row__text {
    width: 70%;
    padding-right: 0.5rem;
    padding-bottom: 0.5rem;
  }

  .e-storyboard-row__responsible {
    width: 15%;
    padding-bottom: 0.5rem;
  }
}
</style>
