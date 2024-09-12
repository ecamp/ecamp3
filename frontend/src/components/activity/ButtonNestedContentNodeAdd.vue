<template>
  <v-row
    v-if="layoutMode"
    no-gutters
    justify="center"
    class="mb-3 ec-button-contentnode-add-wrapper"
    :class="{
      'ec-button-contentnode-add-wrapper--center': root || single,
      'ec-button-contentnode-add-wrapper--single': single,
    }"
  >
    <v-menu bottom left offset-y>
      <template #activator="{ on, attrs }">
        <v-btn
          class="ec-button-contentnode-add"
          color="primary--text"
          block
          :loading="isAdding"
          v-bind="attrs"
          v-on="on"
        >
          <v-icon left>mdi-plus-circle-outline</v-icon>
          {{ $tc('global.button.add') }}
        </v-btn>
      </template>
      <v-list>
        <!-- preferred content types -->
        <v-list-item
          v-for="contentType in preferredContentTypesItems"
          :key="contentType._meta.self"
          @click="addContentNode(contentType)"
        >
          <v-list-item-icon>
            <v-icon>{{ $tc(contentTypeIconKey(contentType)) }}</v-icon>
          </v-list-item-icon>
          <v-list-item-title>
            {{ $tc(contentTypeNameKey(contentType)) }}
          </v-list-item-title>
        </v-list-item>

        <v-divider />

        <!-- all other content types -->
        <v-list-item
          v-for="contentType in nonpreferredContentTypesItems"
          :key="contentType._meta.self"
          @click="addContentNode(contentType)"
        >
          <v-list-item-icon>
            <v-icon>{{ $tc(contentTypeIconKey(contentType)) }}</v-icon>
          </v-list-item-icon>
          <v-list-item-title>
            {{ $tc(contentTypeNameKey(contentType)) }}
          </v-list-item-title>
        </v-list-item>
      </v-list>
    </v-menu>
  </v-row>
</template>
<script>
import { camelCase } from 'lodash'
import { errorToMultiLineToast } from '@/components/toast/toasts'
import { getEnv } from '@/environment.js'

export default {
  name: 'ButtonNestedContentNodeAdd',
  inject: ['draggableDirty', 'preferredContentTypes', 'allContentNodes'],
  props: {
    layoutMode: { type: Boolean, default: false },
    parentContentNode: { type: Object, required: true },
    slotName: { type: String, required: true },
    root: { type: Boolean, default: false },
    single: { type: Boolean, default: false },
  },
  data() {
    return {
      isAdding: false,
    }
  },
  computed: {
    preferredContentTypesItems() {
      if (this.contentTypesLoading) {
        return []
      }
      return this.preferredContentTypes()
        .items.filter(this.filterContentType)
        .sort(this.sortContentTypeByTranslatedName)
    },
    nonpreferredContentTypesItems() {
      if (this.contentTypesLoading) {
        return []
      }
      return this.api
        .get()
        .contentTypes()
        .items.filter(
          (ct) =>
            this.filterContentType(ct) &&
            !this.preferredContentTypes()
              .items.map((ct) => ct.id)
              .includes(ct.id)
        ) // remove contentTypes already included in preferredContentTypes
        .sort(this.sortContentTypeByTranslatedName)
    },
    contentTypesLoading() {
      return this.api.get().contentTypes()._meta.loading
    },
    featureChecklistEnabled() {
      return getEnv().FEATURE_CHECKLIST ?? false
    },
  },
  methods: {
    contentTypeNameKey(contentType) {
      return 'contentNode.' + camelCase(contentType.name) + '.name'
    },
    contentTypeIconKey(contentType) {
      return 'contentNode.' + camelCase(contentType.name) + '.icon'
    },
    filterContentType(contentType) {
      switch (contentType.name) {
        case 'ResponsiveLayout':
          return this.parentContentNode.parent === null
        case 'Checklist':
          return this.featureChecklistEnabled
        default:
          return true
      }
    },
    sortContentTypeByTranslatedName(ct1, ct2) {
      const ct1name = this.$i18n.tc(this.contentTypeNameKey(ct1))
      const ct2name = this.$i18n.tc(this.contentTypeNameKey(ct2))
      return ct1name.localeCompare(ct2name)
    },
    async addContentNode(contentType) {
      this.isAdding = true
      try {
        await this.api.post(await this.api.href(contentType, 'contentNodes'), {
          // this.api.href resolves to the correct endpoint for this contentType (e.g. '/content_node/single_texts?contentType=...')
          parent: this.parentContentNode._meta.self,
          contentType: contentType._meta.self,
          slot: this.slotName,
        })

        // need to clear dirty flag to ensure new content node is visible in case same Patch-calls are still ongoing
        this.draggableDirty.clearDirty(null)

        await this.allContentNodes().$reload()
      } catch (error) {
        this.$toast.error(errorToMultiLineToast(error))
      }
      this.isAdding = false
    },
  },
}
</script>

<style scoped lang="scss">
.ec-button-contentnode-add {
  transition: all 0.2s linear;
  transition-property: box-shadow, background-color;
  box-shadow: inset 0 0 115px #f8fcff;
  border: 2px dotted rgb(13, 71, 161, 0.36);
  background-color: transparent !important;
  justify-content: start;
  &:hover {
    outline: none;
    border: 1px solid rgb(13, 71, 161, 0.6);
    box-shadow: inset 0 0 115px #eef2fa;
    padding: 1px 17px;
  }
}

.dragging-content-node .ec-button-contentnode-add-wrapper--single {
  position: absolute;
  inset: 0;
  margin-bottom: 6px !important;
}

.ec-button-contentnode-add-wrapper--center .ec-button-contentnode-add {
  justify-content: center;
  min-height: 36px;
  height: 100% !important;
}
</style>
