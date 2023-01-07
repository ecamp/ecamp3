<template>
  <v-card :elevation="draggable ? 4 : 0" :class="{ 'mx-2 my-2': draggable }">
    <v-card-title hide-actions class="pa-0 pr-sm-2">
      <v-toolbar dense flat>
        <v-icon class="mr-2">{{ icon }}</v-icon>

        <div v-if="editInstanceName" style="flex: 1" @click.stop @keyup.prevent>
          <api-text-field
            dense
            autofocus
            :auto-save="false"
            :uri="contentNode._meta.self"
            fieldname="instanceName"
            @finished="editInstanceName = false"
          />
        </div>

        <v-toolbar-title v-if="!editInstanceName">
          {{ instanceOrContentTypeName }}
        </v-toolbar-title>

        <v-spacer v-if="!editInstanceName" />
        <icon-with-tooltip
          v-if="!editInstanceName && !layoutMode"
          :tooltip-i18n-key="`contentNode.${camelCase(contentNode.contentTypeName)}.info`"
        />

        <v-btn
          v-if="!editInstanceName && !layoutMode"
          icon
          class="visible-on-hover"
          @click="toggleEditInstanceName"
        >
          <v-icon>mdi-pencil</v-icon>
        </v-btn>

        <dialog-entity-delete v-if="layoutMode && !disabled" :entity="contentNode">
          <template #activator="{ on }">
            <v-btn icon small color="error" class="float-right" v-on="on">
              <v-icon>mdi-trash-can-outline</v-icon>
            </v-btn>
          </template>
        </dialog-entity-delete>
      </v-toolbar>
    </v-card-title>
    <v-card-text>
      <slot />
    </v-card-text>
  </v-card>
</template>

<script>
import camelCase from 'lodash/camelCase'
import ApiTextField from '../form/api/ApiTextField.vue'
import DialogEntityDelete from '@/components/dialog/DialogEntityDelete.vue'

export default {
  name: 'CardContentNode',
  components: {
    ApiTextField,
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
      return this.$tc(`contentNode.${camelCase(this.contentNode.contentTypeName)}.name`)
    },
    icon() {
      return this.$tc(`contentNode.${camelCase(this.contentNode.contentTypeName)}.icon`)
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
.v-card:deep(button.visible-on-hover),
.v-card:deep(button.tooltip-activator) {
  width: 36px !important;
  height: 36px !important;
}

.v-card:not(:hover):deep(button.visible-on-hover),
.v-card:not(:hover):deep(button.tooltip-activator) {
  opacity: 0;
  width: 0px !important;

  transition: opacity 0.2s linear, width 0.3s steps(1, end);
}
.v-card:hover:deep(button.visible-on-hover),
.v-card:hover:deep(button.tooltip-activator) {
  opacity: 1;
  width: 36px !important;

  transition: opacity 0.2s linear, width 0.3s steps(1, start);
}
</style>
