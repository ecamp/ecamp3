<template>
  <v-card :elevation="draggable ? 4 : 0" :class="{ 'mx-2 my-2': draggable }">
    <v-card-title hide-actions class="pa-0 pr-sm-2">
      <v-toolbar dense flat>
        <v-icon class="mr-1">{{ icon }}</v-icon>

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

        <v-toolbar-title v-else>
          {{ instanceOrContentTypeName }}
        </v-toolbar-title>

        <v-spacer />

        <v-menu v-if="!layoutMode && !disabled" bottom left offset-y>
          <template #activator="{ on, attrs }">
            <v-btn icon v-bind="attrs" v-on="on">
              <v-icon>mdi-dots-vertical</v-icon>
            </v-btn>
          </template>
          <v-list>
            <v-list-item @click="toggleEditInstanceName">
              <v-list-item-icon>
                <v-icon>mdi-pencil</v-icon>
              </v-list-item-icon>
              <v-list-item-title>
                {{ $tc('components.activity.contentNode.editName') }}
              </v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
        <dialog-entity-delete v-else-if="!disabled" :entity="contentNode">
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
    toggleEditInstanceName() {
      if (this.disabled) {
        return
      }
      this.editInstanceName = !this.editInstanceName
    },
  },
}
</script>

<style scoped></style>
