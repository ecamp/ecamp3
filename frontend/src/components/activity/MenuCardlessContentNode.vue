<template>
  <v-menu bottom
          left
          offset-y>
    <template #activator="{ on, attrs }">
      <v-btn icon
             class="float-right mr-4 mt-3"
             v-bind="attrs"
             v-on="on">
        <v-icon>mdi-dots-vertical</v-icon>
      </v-btn>
    </template>
    <v-list>
      <dialog-entity-delete v-if="!contentNode.children()._meta.loading"
                            :entity="contentNode">
        <template #activator="{ on }">
          <v-list-item :disabled="deletingDisabled" v-on="on">
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

export default {
  name: 'MenuCardlessContentNode',
  components: {
    DialogEntityDelete
  },
  props: {
    contentNode: { type: Object, required: true }
  },
  computed: {
    deletingDisabled () {
      return this.contentNode.children().items.length > 0
    },
    deleteCaption () {
      return this.deletingDisabled
        ? this.$tc('components.activity.menuCardlessContentNode.deletingDisabled')
        : this.$tc('components.activity.menuCardlessContentNode.delete')
    }
  }
}
</script>
