<template>
  <v-menu location="bottom left" offset-y>
    <template #activator="{ props }">
      <v-btn class="mr-n1" icon size="small" v-bind="props">
        <v-icon>mdi-dots-vertical</v-icon>
      </v-btn>
    </template>
    <v-list>
      <slot />
      <dialog-entity-delete
        v-if="showDelete"
        :entity="contentNode"
        :warning-text-entity="contentNodeName"
        @error="deletingFailed"
      >
        <template #activator="{ props }">
          <v-list-item :disabled="deletingDisabled" v-bind="props">
            <v-list-item-icon>
              <v-icon>mdi-trash-can-outline</v-icon>
            </v-list-item-icon>
            <v-list-item-title>
              {{ deleteCaption }}
            </v-list-item-title>
          </v-list-item>
        </template>
      </dialog-entity-delete>
    </v-list>
  </v-menu>
</template>
<script>
import DialogEntityDelete from '@/components/dialog/DialogEntityDelete.vue'
import { errorToMultiLineToast } from '@/components/toast/toasts'
import camelCase from 'lodash-es/camelCase.js'

export default {
  name: 'MenuCardlessContentNode',
  components: {
    DialogEntityDelete,
  },
  inject: ['allContentNodes'],
  props: {
    contentNode: { type: Object, required: true },
  },
  computed: {
    isRoot() {
      return this.contentNode._meta.self === this.contentNode.root()._meta.self
    },
    children() {
      return this.allContentNodes().items.filter((child) => {
        return (
          child.parent !== null &&
          child.parent()._meta.self === this.contentNode._meta.self
        )
      })
    },
    showDelete() {
      return !this.isRoot
    },
    deletingDisabled() {
      return this.children.length > 0
    },
    deleteCaption() {
      return this.deletingDisabled
        ? this.$t('components.activity.menuCardlessContentNode.deletingDisabled')
        : this.$t('global.button.delete')
    },
    contentNodeName() {
      return this.$t(`contentNode.${camelCase(this.contentNode.contentTypeName)}.name`)
    },
  },
  methods: {
    deletingFailed(error) {
      this.$toast.error(errorToMultiLineToast(error))
      this.allContentNodes().$reload()
    },
  },
}
</script>
