<template>
  <v-row v-if="layoutMode"
         no-gutters
         justify="center"
         class="mb-3">
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
        <!-- preferred content types -->
        <v-list-item v-for="act in preferredContentTypes"
                     :key="act.contentType.id"
                     @click="addContentNode(act.contentType)">
          <v-list-item-icon>
            <v-icon>{{ $tc(act.contentTypeIconKey) }}</v-icon>
          </v-list-item-icon>
          <v-list-item-title>
            {{ $tc(act.contentTypeNameKey) }}
          </v-list-item-title>
        </v-list-item>

        <v-divider />

        <!-- all other content types -->
        <v-list-item v-for="act in allContentTypes"
                     :key="act.contentType.id"
                     @click="addContentNode(act.contentType)">
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
    preferredContentTypes () {
      return this.parentContentNode.ownerCategory().preferredContentTypes().items.map(this.contentTypeMap)
    },
    allContentTypes () {
      return this.api.get().contentTypes().items
        .filter(ct => !this.parentContentNode.ownerCategory().preferredContentTypes().items.map(ct => ct.id).includes(ct.id)) // remove contentTypes already included in preferredContentTypes
        .map(this.contentTypeMap)
    }
  },
  methods: {
    contentTypeMap (ct) {
      return {
        id: ct.id,
        contentType: ct,
        contentTypeNameKey: 'contentNode.' + camelCase(ct.name) + '.name',
        contentTypeIconKey: 'contentNode.' + camelCase(ct.name) + '.icon'
      }
    },
    async addContentNode (contentType) {
      await this.api.post(await this.api.href(contentType, 'contentNodes'), { // this.api.href resolves to the correct endpoint for this contentType (e.g. '/content_node/single_texts?contentType=...')
        parent: this.parentContentNode._meta.self,
        contentType: contentType._meta.self,
        slot: this.slotName
      })
      this.parentContentNode.owner().$reload()
    }
  }
}
</script>
