<template>
  <tr class="e-storyboard-row e-storyboard-row--default">
    <td v-if="!layoutMode" class="e-storyboard-row__handle">
      <v-btn
        icon
        small
        class="drag-and-drop-handle"
        :disabled="isLastSection"
        :aria-label="$tc('global.button.move')"
        @keydown.down="$emit('move-down', itemKey)"
        @keydown.up="$emit('move-up', itemKey)"
      >
        <v-icon>mdi-drag</v-icon>
      </v-btn>
    </td>
    <td class="e-storyboard-row__time">
      <api-text-field
        :label="null"
        single-line
        :path="`data.sections[${itemKey}].column1`"
        :disabled="layoutMode || disabled"
      />
    </td>
    <td class="e-storyboard-row__text">
      <api-richtext
        :label="null"
        :path="`data.sections[${itemKey}].column2Html`"
        rows="4"
        :disabled="layoutMode || disabled"
      />
    </td>
    <td class="e-storyboard-row__responsible">
      <api-text-field
        :label="null"
        single-line
        :path="`data.sections[${itemKey}].column3`"
        :disabled="layoutMode || disabled"
      />
    </td>
    <td v-if="!layoutMode && !disabled" class="e-storyboard-row__controls">
      <dialog-remove-section @submit="$emit('delete', itemKey)">
        <template #activator="{ on }">
          <v-btn
            icon
            small
            class="e-storyboard-row__delete"
            color="error"
            :disabled="isLastSection"
            v-on="on"
          >
            <v-icon>mdi-delete-outline</v-icon>
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

  .e-storyboard-row__controls {
    align-content: space-between;
  }
}
</style>
