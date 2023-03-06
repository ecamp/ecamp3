<template>
  <component
    :is="variant !== 'dense' ? 'tr' : 'div'"
    :role="variant === 'dense' ? 'row' : null"
    class="e-storyboard-row"
    :class="{
      'e-storyboard-row--default': variant === 'default',
      'e-storyboard-row--dense': variant === 'dense',
    }"
  >
    <component
      :is="variant !== 'dense' ? 'td' : 'div'"
      :role="variant === 'dense' ? 'cell' : null"
      class="e-storyboard-row__handle"
    >
      <v-btn
        icon
        small
        class="drag-and-drop-handle"
        :disabled="isLastSection"
        :aria-label="$tc('components.activity.content.storyboardRow.move')"
        @keydown.down="$emit('moveDown', itemKey)"
        @keydown.up="$emit('moveUp', itemKey)"
      >
        <v-icon>mdi-drag</v-icon>
      </v-btn>
    </component>
    <component
      :is="variant !== 'dense' ? 'td' : 'div'"
      :role="variant === 'dense' ? 'cell' : null"
      class="e-storyboard-row__time"
    >
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
    </component>
    <component
      :is="variant !== 'dense' ? 'td' : 'div'"
      v-if="variant !== 'dense'"
      :role="variant === 'dense' ? 'cell' : null"
      class="e-storyboard-row__text"
    >
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
    </component>
    <component
      :is="variant !== 'dense' ? 'td' : 'div'"
      :role="variant === 'dense' ? 'cell' : null"
      class="e-storyboard-row__responsible"
    >
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
    </component>
    <component
      :is="variant !== 'dense' ? 'td' : 'div'"
      v-if="variant === 'dense'"
      :role="variant === 'dense' ? 'cell' : null"
      class="e-storyboard-row__text"
    >
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
    </component>
    <component
      :is="variant !== 'dense' ? 'td' : 'div'"
      v-if="!layoutMode && !disabled"
      :role="variant === 'dense' ? 'cell' : null"
      class="e-storyboard-row__controls"
    >
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
    </component>
  </component>
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

.e-form-container + .e-form-container {
  margin-top: 0;
}
</style>
