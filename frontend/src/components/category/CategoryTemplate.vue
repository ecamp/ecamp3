<template>
  <div>
    <i18n path="views.category.category.createLayoutHelp" tag="p">
      <template #categoryShort>
        <CategoryChip :category="category" dense />
      </template>
      <template #br><br /></template>
    </i18n>

    <div
      class="area rounded"
      :style="{ maxWidth: isPaperDisplaySize ? '944px' : '100%' }"
    >
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

        <TogglePaperSize
          :value="isPaperDisplaySize"
          :button-props="{ large: true }"
          @input="toggleDisplaySize"
        />
      </div>
      <v-divider />
      <v-tabs-items v-model="layoutMode" class="transparent rounded-b">
        <v-tab-item :value="true" :transition="null">
          <v-skeleton-loader v-if="loading" type="article" />
          <root-node
            v-else
            :content-node="category.rootContentNode()"
            :layout-mode="true"
            :disabled="readonly"
          />
        </v-tab-item>
        <v-tab-item :value="false" :transition="null">
          <v-skeleton-loader v-if="loading" type="article" />
          <div
            v-else-if="category.rootContentNode().children().items.length === 0"
            class="pa-2 text-center"
          >
            {{ $tc('components.category.categoryTemplate.noTemplate') }}
          </div>
          <root-node
            v-else
            :content-node="category.rootContentNode()"
            :layout-mode="false"
            :disabled="readonly"
          />
        </v-tab-item>
      </v-tabs-items>
    </div>
  </div>
</template>
<script>
import CategoryChip from '@/components/generic/CategoryChip.vue'
import RootNode from '@/components/activity/RootNode.vue'
import TogglePaperSize from '@/components/activity/TogglePaperSize.vue'
import { campRoleMixin } from '@/mixins/campRoleMixin.js'

export default {
  name: 'CategoryTemplate',
  components: { TogglePaperSize, CategoryChip, RootNode },
  mixins: [campRoleMixin],
  props: {
    category: { type: Object, required: true },
    loading: { type: Boolean, default: false },
    readonly: { type: Boolean, default: false },
  },
  data() {
    return {
      layoutMode: true,
      isPaperDisplaySize: true,
    }
  },
  computed: {
    camp() {
      return this.category.camp()
    },
  },
  methods: {
    toggleDisplaySize() {
      this.isPaperDisplaySize = !this.isPaperDisplaySize
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
  background: #eee;
  transition: max-width 0.2s ease;
}

.ec-category-layoutmode-tabs :deep(.v-tab::before) {
  border-radius: 999px;
  margin: 8px 4px;
}

.ec-category-layoutmode-tabs :deep(.v-tab--active.v-tab:not(:focus)::before) {
  opacity: 0.12;
}

:deep(.ec-paper-size-toggle) {
  position: absolute;
  top: 2px;
  right: 6px;
}
</style>
