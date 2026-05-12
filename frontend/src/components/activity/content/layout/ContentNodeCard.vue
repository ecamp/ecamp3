<template>
  <v-card
    :elevation="draggable ? 4 : 0"
    :tile="!layoutMode"
    class="ec-content-nodecard max-w-screen d-flex flex-column"
    :class="{ 'mx-2 my-2 elevation-4--light': draggable }"
  >
    <v-toolbar density="compact" flat color="transparent" class="px-4">
      <v-icon>{{ icon }}</v-icon>

      <api-form
        v-if="editInstanceName"
        :entity="contentNode"
        style="flex: 1"
        @click.stop
        @keyup.prevent
      >
        <api-text-field
          density="compact"
          autofocus
          :auto-save="false"
          path="instanceName"
          @finished="editInstanceName = false"
        />
      </api-form>

      <v-toolbar-title
        v-if="!editInstanceName"
        style="flex-basis: auto"
        tag="h2"
        :class="{ 'user-select-none': layoutMode }"
      >
        {{ instanceOrContentTypeName }}
      </v-toolbar-title>

      <v-btn
        v-if="!editInstanceName && !disabled"
        icon
        class="ml-1"
        :class="{ 'visible-on-hover': !layoutMode }"
        width="24"
        height="24"
        @click="toggleEditInstanceName"
      >
        <v-icon size="small">mdi-pencil</v-icon>
      </v-btn>

      <v-spacer v-if="!editInstanceName" />
      <IconWithTooltip
        v-if="!editInstanceName && !layoutMode"
        :tooltip-i18n-key="`contentNode.${camelCase(contentNode.contentTypeName)}.info`"
        width="36"
        height="36"
      />

      <DialogEntityDelete
        v-if="layoutMode && !disabled"
        :entity="contentNode"
        :warning-text-entity="instanceOrContentTypeName"
      >
        <template #activator="{ props }">
          <v-btn
            v-bind="props"
            icon
            size="small"
            color="error"
            class="float-right"
            width="36"
            height="36"
          >
            <v-icon>mdi-trash-can-outline</v-icon>
          </v-btn>
        </template>
      </DialogEntityDelete>
    </v-toolbar>
    <slot name="outer">
      <v-card-text
        class="flex-grow-1 pt-0"
        :class="{ 'pointer-events-none user-select-none': layoutMode }"
      >
        <slot />
      </v-card-text>
    </slot>
  </v-card>
</template>

<script>
import { camelCase } from 'lodash-es'
import DialogEntityDelete from '@/components/dialog/DialogEntityDelete.vue'
import IconWithTooltip from '@/components/generic/IconWithTooltip.vue'
import ApiForm from '@/components/form/api/ApiForm.vue'
import ApiTextField from '@/components/form/api/ApiTextField.vue'
import contentTypeIcons from '../contentTypeIcons.js'

export default {
  name: 'ContentNodeCard',
  components: {
    ApiTextField,
    ApiForm,
    IconWithTooltip,
    DialogEntityDelete,
  },
  props: {
    contentNode: { type: Object, required: true },
    layoutMode: { type: Boolean, required: true },
    draggable: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
  },
  data() {
    return {
      editInstanceName: false,
    }
  },
  computed: {
    instanceOrContentTypeName() {
      if (this.contentNode.instanceName) {
        return this.contentNode.instanceName
      }
      return this.$t(`contentNode.${camelCase(this.contentNode.contentTypeName)}.name`)
    },
    icon() {
      return contentTypeIcons[camelCase(this.contentNode.contentTypeName)]
    },
  },
  methods: {
    camelCase,
    toggleEditInstanceName() {
      if (this.disabled) {
        return
      }
      this.editInstanceName = !this.editInstanceName
    },
  },
}
</script>

<style scoped lang="scss">
.ec-content-nodecard {
  transition: all 0.2s ease;
  transition-property: background-color, border-color, box-shadow;
  background-color: inherit;
  border-color: rgba(0, 0, 0, 0.32);
  &:hover {
    border-color: rgba(0, 0, 0, 0.6);
  }
}

:deep(.v-toolbar__content:not(:hover) button.visible-on-hover:not(:focus)),
.v-card:not(:hover):deep(button.tooltip-activator) {
  opacity: 0;
}
:deep(.v-toolbar__content button.visible-on-hover),
.v-card:hover:deep(button.tooltip-activator) {
  opacity: 1;
  transition: opacity 0.2s linear;
}

:deep(.grow-v-slot.e-form-container),
.grow-v-slot :deep(.e-form-container),
:deep(.grow-v-slot .v-field),
.grow-v-slot :deep(.v-input) {
  height: 100%;
}

:deep(.v-text-field__details) {
  flex-grow: 0;
}

:deep(.grow-v-slot .v-input__slot) {
  flex-grow: 1;
}
</style>
