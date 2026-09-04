<template>
  <div>
    <i18n-t
      keypath="components.category.categoryTemplate.createLayoutHelp"
      tag="p"
      scope="global"
      class="mb-4"
    >
      <template #categoryShort>
        <CategoryChip :category="category" dense />
      </template>
      <template #applyToActivities>
        <DialogApplyCategoryLayoutToActivities :category="category" :camp="camp" />
      </template>
      <template #br><br /></template>
    </i18n-t>

    <div class="area rounded">
      <div class="relative">
        <v-tabs
          v-model="layoutMode"
          class="ec-category-layoutmode-tabs uppercase"
          align-tabs="center"
          color="primary"
          hide-slider
        >
          <v-tab :value="true" variant="text">
            <v-icon start>mdi-view-compact-outline</v-icon>
            {{ $t('components.category.categoryTemplate.layout') }}
          </v-tab>
          <v-tab :value="false" variant="text">
            <v-icon start>mdi-text</v-icon>
            {{ $t('components.category.categoryTemplate.contents') }}
          </v-tab>
        </v-tabs>
      </div>
      <v-divider />
      <div class="relative">
        <div class="ec-category-layoutmode-tab__layout">
          <v-skeleton-loader v-if="loading" type="article" />
          <div
            v-else-if="
              !layoutMode && category.rootContentNode().children().items.length === 0
            "
            class="pa-2 text-center"
          >
            {{ $t('components.category.categoryTemplate.noTemplate') }}
          </div>
          <root-node
            v-else
            :class="{
              'ec-category-layoutmode-tab--hidden': loading,
            }"
            :content-node="category.rootContentNode()"
            :layout-mode="layoutMode"
            :disabled="disabled"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import CategoryChip from '@/components/generic/CategoryChip.vue'
import RootNode from '@/components/activity/RootNode.vue'
import { campRoleMixin } from '@/mixins/campRoleMixin.js'

export default {
  name: 'CategoryTemplate',
  components: { CategoryChip, RootNode },
  mixins: [campRoleMixin],
  props: {
    category: { type: Object, required: true },
    loading: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
  },
  data() {
    return {
      layoutMode: true,
    }
  },
  computed: {
    camp() {
      return this.category.camp()
    },
  },
}
</script>

<style scoped>
/* eslint-disable-next-line vue-scoped-css/no-unused-selector */
.v-tabs :deep(.v-tabs-slider-wrapper) {
  transition: none;
}

.area {
  border: 2px dashed #ccc;
}

.ec-category-layoutmode-tab__layout {
  box-shadow:
    inset rgba(0, 0, 0, 0.07) 0 2px 5px,
    inset rgba(0, 0, 0, 0.05) 0 6px 20px,
    inset rgba(0, 0, 0, 0.02) 0 10px 42px;
}

.ec-category-layoutmode-tabs :deep(.v-btn) {
  text-transform: uppercase;
  letter-spacing: 0.0892857143em;
  font-weight: 600;

  :deep(.v-icon) {
    --v-icon-size-multiplier: 1;
  }
}

.ec-category-layoutmode-tabs :deep(.v-btn__underlay) {
  border-radius: 999px;
  top: 8px;
  left: 4px;
  right: 4px;
  bottom: 8px;
  width: calc(100% - 8px);
  height: calc(100% - 16px);
  background: currentColor;
  opacity: 0;
}

.ec-category-layoutmode-tabs :deep(.v-tab--selected .v-btn__underlay) {
  opacity: 0.12;
}

.ec-category-layoutmode-tab--hidden {
  display: block !important;
  position: absolute;
  opacity: 0;
  top: 0;
  height: 100%;
  overflow: hidden;
  width: 100%;
  pointer-events: none;
  user-focus: none;
  user-select: none;
}
</style>
