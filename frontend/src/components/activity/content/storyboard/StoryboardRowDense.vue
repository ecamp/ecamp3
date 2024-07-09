<template>
  <div role="row" class="e-storyboard-row e-storyboard-row--dense">
    <div v-if="!layoutMode" role="cell" class="e-storyboard-row__handle">
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
    </div>
    <div role="cell" class="e-storyboard-row__time">
      <api-text-field
        :label="$tc('contentNode.storyboard.entity.section.fields.column1')"
        :single-line="false"
        :path="`data.sections[${itemKey}].column1`"
        :disabled="layoutMode || disabled"
      />
    </div>
    <div role="cell" class="e-storyboard-row__responsible">
      <api-text-field
        :label="$tc('contentNode.storyboard.entity.section.fields.column3')"
        :single-line="false"
        :path="`data.sections[${itemKey}].column3`"
        :disabled="layoutMode || disabled"
      />
    </div>
    <div role="cell" class="e-storyboard-row__text">
      <api-richtext
        :label="$tc('contentNode.storyboard.entity.section.fields.column2Html')"
        :path="`data.sections[${itemKey}].column2Html`"
        rows="4"
        :disabled="layoutMode || disabled"
      />
    </div>
    <div v-if="!layoutMode && !disabled" role="cell" class="e-storyboard-row__controls">
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
    </div>
  </div>
</template>
<script>
import DialogRemoveSection from './StoryboardDialogRemoveSection.vue'

export default {
  name: 'StoryboardRowDense',
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

.e-storyboard-row--dense {
  display: grid;
  gap: 0.5rem;
  padding-top: 0.5rem;
  padding-bottom: 0.5rem;
  grid-template-areas:
    'handle time responsible controls'
    'handle text text        controls';
  grid-template-columns: min-content 1fr 1fr min-content;
  align-items: baseline;

  .e-storyboard-row__time {
    grid-area: time;
  }

  .e-storyboard-row__text {
    grid-area: text;
  }

  .e-storyboard-row__responsible {
    grid-area: responsible;
  }

  .e-storyboard-row__handle {
    grid-area: handle;
    margin-right: -6px;
    margin-left: 2px;
  }

  .e-storyboard-row__controls {
    grid-area: controls;
    display: grid;
    margin-left: -6px;
    margin-right: 2px;
  }
}

.e-form-container + .e-form-container {
  margin-top: 0;
}
</style>
