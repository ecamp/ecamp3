<template>
  <tr
    class="e-storyboard-row"
    :class="{
      'e-storyboard-row--default': variant === 'default',
      'e-storyboard-row--dense': variant === 'dense',
    }"
  >
    <td class="e-storyboard-row__time">
      <api-text-field
        :label="
          variant === 'dense'
            ? $tc('contentNode.storyboard.entity.section.fields.column1')
            : null
        "
        :single-line="variant === 'default'"
        :fieldname="`data.sections[${itemKey}].column1`"
        :disabled="layoutMode || disabled"
        :filled="layoutMode"
      />
    </td>
    <td class="e-storyboard-row__text">
      <api-richtext
        :label="
          variant === 'dense'
            ? $tc('contentNode.storyboard.entity.section.fields.column2Html')
            : null
        "
        :fieldname="`data.sections[${itemKey}].column2Html`"
        rows="4"
        :disabled="layoutMode || disabled"
        :filled="layoutMode"
      />
    </td>
    <td class="e-storyboard-row__responsible">
      <api-text-field
        :label="
          variant === 'dense'
            ? $tc('contentNode.storyboard.entity.section.fields.column3')
            : null
        "
        :single-line="variant === 'default'"
        :fieldname="`data.sections[${itemKey}].column3`"
        :disabled="layoutMode || disabled"
        :filled="layoutMode"
      />
    </td>
    <td v-if="!layoutMode && !disabled" class="e-storyboard-row__controls">
      <div class="e-storyboard-row__controls__move">
        <v-btn
          v-if="variant === 'dense'"
          icon
          small
          :disabled="isLastSection"
          @click="$emit('moveUp', itemKey)"
        >
          <v-icon>mdi-arrow-up</v-icon>
        </v-btn>
        <v-btn
          v-if="variant !== 'dense'"
          icon
          small
          class="drag-and-drop-handle"
          :disabled="isLastSection"
        >
          <v-icon>mdi-drag</v-icon>
        </v-btn>
        <v-btn
          v-if="variant === 'dense'"
          icon
          small
          :disabled="isLastSection"
          @click="$emit('moveDown', itemKey)"
        >
          <v-icon>mdi-arrow-down</v-icon>
        </v-btn>
      </div>
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
  name: 'StoryboardRow',
  components: { DialogRemoveSection },
  props: {
    isLastSection: { type: Boolean, required: true },
    itemKey: { type: String, required: true },
    layoutMode: { type: Boolean, required: true },
    disabled: { type: Boolean, default: false },
    variant: { type: String, default: 'default' },
  },
}
</script>
<style scoped lang="scss">
.e-storyboard-row {
  &:hover .e-storyboard-row__delete {
    opacity: 1;
  }
}

.e-storyboard-row__delete {
  color: rgba(0, 0, 0, 0.54) !important;

  &:hover {
    color: #d32f2f !important;
  }
}

.e-storyboard-row--dense {
  display: grid;
  gap: 0.5rem;
  margin-bottom: 1.5rem;
  grid-template-areas:
    'time responsible controls'
    'text text        controls';
  grid-template-columns: 1fr 1fr min-content;

  .e-storyboard-row__time {
    grid-area: time;
  }

  .e-storyboard-row__text {
    grid-area: text;
  }

  .e-storyboard-row__responsible {
    grid-area: responsible;
  }

  .e-storyboard-row__controls {
    grid-area: controls;
    display: grid;
  }

  .e-storyboard-row__delete {
    margin-top: auto;
  }
}

.e-storyboard-row--default {
  vertical-align: top;

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

  .e-storyboard-row__delete {
    opacity: 0;
  }
}

.e-form-container + .e-form-container {
  margin-top: 0;
}
</style>
