<template>
  <div>
    <i18n path="components.category.categoryTemplate.createLayoutHelp" tag="p">
      <template #categoryShort>
        <CategoryChip :category="category" dense />
      </template>
      <template #br><br /></template>
    </i18n>

    <div class="area rounded">
      <div class="relative">
        <v-tabs
          v-model="layoutMode"
          class="ec-category-layoutmode-tabs"
          centered
          hide-slider
        >
          <v-tab :tab-value="true">
            <v-icon left>mdi-view-compact-outline</v-icon>
            {{ $tc('components.category.categoryTemplate.layout') }}
          </v-tab>
          <v-tab :tab-value="false">
            <v-icon left>mdi-text</v-icon>
            {{ $tc('components.category.categoryTemplate.contents') }}
          </v-tab>
        </v-tabs>
      </div>
      <v-divider />
      <div class="relative">
        <div
          v-show="layoutMode"
          :key="true"
          class="ec-category-layoutmode-tab__layout"
          :class="{ 'ec-category-layoutmode-tab--hidden': !layoutMode }"
        >
          <v-skeleton-loader v-if="loading" type="article" />
          <root-node
            :class="{
              'ec-category-layoutmode-tab--hidden': loading,
            }"
            :content-node="category.rootContentNode()"
            :layout-mode="true"
            :disabled="disabled"
          />
        </div>
        <div
          v-show="!layoutMode"
          :key="false"
          :class="{ 'ec-category-layoutmode-tab--hidden': layoutMode }"
          :style="{ opacity: layoutMode ? 0 : 1 }"
        >
          <v-skeleton-loader v-if="loading" type="article" />
          <div
            v-else-if="category.rootContentNode().children().items.length === 0"
            class="pa-2 text-center"
          >
            {{ $tc('components.category.categoryTemplate.noTemplate') }}
          </div>
          <root-node
            v-if="category.rootContentNode().children().items.length !== 0"
            :class="{
              'ec-category-layoutmode-tab--hidden': loading,
            }"
            :content-node="category.rootContentNode()"
            :layout-mode="false"
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

.ec-category-layoutmode-tabs :deep(.v-tab::before) {
  border-radius: 999px;
  margin: 8px 4px;
}

.ec-category-layoutmode-tabs :deep(.v-tab--active.v-tab:not(:focus)::before) {
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
