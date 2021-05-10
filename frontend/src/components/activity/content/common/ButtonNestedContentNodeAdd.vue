<template>
  <v-row v-if="layoutMode"
         no-gutters
         justify="center"
         class="my-3">
    <v-menu bottom
            left
            offset-y>
      <template #activator="{ on, attrs }">
        <v-btn color="primary"
               outlined
               v-bind="attrs"
               v-on="on">
          <v-icon left>mdi-plus-circle-outline</v-icon>
          {{ $tc('global.button.add') }}
        </v-btn>
      </template>
      <v-list>
        <v-list-item v-for="act in availableContentTypes"
                     :key="act.contentType.id"
                     @click="addContentNode(act.contentType.id)">
          <v-list-item-icon>
            <v-icon>{{ $tc(act.contentTypeIconKey) }}</v-icon>
          </v-list-item-icon>
          <v-list-item-title>
            {{ $tc(act.contentTypeNameKey) }}
          </v-list-item-title>
        </v-list-item>
      </v-list>
    </v-menu>
  </v-row>
</template>
<script>
import { camelCase } from 'lodash'

export default {
  name: 'ButtonNestedContentNodeAdd',
  props: {
    layoutMode: { type: Boolean, default: false },
    parentContentNode: { type: Object, required: true },
    slotName: { type: String, required: true }
  },
  computed: {
    availableContentTypes () {
      return this.parentContentNode.ownerCategory().preferredContentTypes().items.map(ct => ({
        id: ct.id,
        contentType: ct,
        contentTypeNameKey: 'contentNode.' + camelCase(ct.name) + '.name',
        contentTypeIconKey: 'contentNode.' + camelCase(ct.name) + '.icon'
      }))
    }
  },
  methods: {
    async addContentNode (contentTypeId) {
      await this.api.post(await this.api.href(this.api.get(), 'contentNodes'), {
        parentId: this.parentContentNode.id,
        contentTypeId: contentTypeId,
        slot: this.slotName
      })
      this.parentContentNode.owner().$reload()
    }
  }
}
</script>
