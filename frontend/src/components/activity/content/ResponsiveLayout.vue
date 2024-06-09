<template>
  <LayoutNodeCard
    v-resizeobserver.debounce="onResize"
    class="ec-responsivelayout"
    :class="{
      'ec-responsivelayout--layout-mode': layoutMode,
      'ec-responsivelayout--read-mode': !layoutMode,
      'ec-responsivelayout--paper-variant': !isDefaultVariant,
    }"
    :is-root="isRoot"
    :layout-mode="layoutMode"
  >
    <template #header>
      <strong>
        <v-icon color="blue darken-2">$vuetify.icons.responsiveLayout</v-icon>
        {{ $tc('contentNode.responsiveLayout.name') }}
      </strong>
      <menu-cardless-content-node :content-node="contentNode" />
    </template>
    <div v-if="!contentNode.loading" class="ec-responsivelayout__container">
      <div
        class="d-flex flex-column flex-grow-1 ec-responsivelayout__slot ec-responsivelayout__slot--main"
      >
        <p
          v-if="layoutMode"
          class="mt-2 mb-1 blue--text text--darken-3 font-weight-medium"
        >
          {{ $tc('contentNode.responsiveLayout.mainContent') }}
        </p>
        <draggable-content-nodes
          slot-name="main"
          :layout-mode="layoutMode"
          :parent-content-node="contentNode"
          :disabled="disabled"
          direction="column"
        />
      </div>
      <div
        v-if="hasAsideTop || layoutMode"
        class="d-flex flex-column ec-responsivelayout__slot ec-responsivelayout__slot--aside-top"
        :class="{ 'flex-grow-1': !layoutMode }"
      >
        <p
          v-if="layoutMode"
          class="mt-2 mb-1 blue--text text--darken-3 font-weight-medium"
        >
          {{ $tc('contentNode.responsiveLayout.printAboveMainContent') }}
        </p>
        <draggable-content-nodes
          slot-name="aside-top"
          :layout-mode="layoutMode"
          :parent-content-node="contentNode"
          :disabled="disabled"
          direction="row"
        />
      </div>
      <v-sheet
        v-if="!layoutMode && !(hasAsideTop && hasAsideBottom)"
        tile
        class="flex-grow-1"
      />
      <div
        v-if="hasAsideBottom || layoutMode"
        class="d-flex flex-column ec-responsivelayout__slot"
        :class="{ 'flex-grow-1': !layoutMode }"
      >
        <p
          v-if="layoutMode"
          class="mt-2 mb-1 blue--text text--darken-3 font-weight-medium"
        >
          {{ $tc('contentNode.responsiveLayout.printBelowMainContent') }}
        </p>
        <draggable-content-nodes
          slot-name="aside-bottom"
          :layout-mode="layoutMode"
          :parent-content-node="contentNode"
          :disabled="disabled"
          direction="row"
        />
      </div>
    </div>
  </LayoutNodeCard>
</template>

<script>
import { contentNodeMixin } from '@/mixins/contentNodeMixin.js'
import DraggableContentNodes from '@/components/activity/DraggableContentNodes.vue'
import MenuCardlessContentNode from '@/components/activity/MenuCardlessContentNode.vue'
import LayoutNodeCard from '@/components/activity/content/layout/LayoutNodeCard.vue'
import { groupBy } from 'lodash'

const ASIDE_CONTENT_WIDTH = 250
const MAIN_CONTENT_WIDTH = 600
const GAP = 1

export default {
  name: 'ResponsiveLayout',
  components: {
    DraggableContentNodes,
    MenuCardlessContentNode,
    LayoutNodeCard,
  },
  mixins: [contentNodeMixin],
  inject: ['isPaperDisplaySize'],
  data() {
    return {
      ASIDE_CONTENT_WIDTH,
      MAIN_CONTENT_WIDTH,
      clientWidth: ASIDE_CONTENT_WIDTH + MAIN_CONTENT_WIDTH + GAP,
    }
  },
  computed: {
    hasAsideTop() {
      return this.childrenContentNodesBySlot['aside-top']?.length > 0
    },
    hasAsideBottom() {
      return this.childrenContentNodesBySlot['aside-bottom']?.length > 0
    },
    childrenContentNodesBySlot() {
      return groupBy(this.contentNode.children().items, 'slot')
    },
    isDefaultVariant() {
      return (
        !this.isPaperDisplaySize() &&
        this.clientWidth >= ASIDE_CONTENT_WIDTH + MAIN_CONTENT_WIDTH + GAP
      )
    },
    isRoot() {
      return this.contentNode._meta.self === this.contentNode.root()._meta.self
    },
  },
  mounted() {
    this.clientWidth = this.$el.getBoundingClientRect().width
  },
  methods: {
    onResize({ width }) {
      this.clientWidth = width
    },
  },
}
</script>

<style lang="scss" scoped>
.ec-responsivelayout--layout-mode {
  border: 1px solid black;
  border-radius: 10px;
}

.ec-responsivelayout__container {
  display: grid;
  grid-template-columns: minmax(600px, 2fr) minmax(250px, 1fr);
  grid-template-rows: 1fr 1fr;
  gap: 1px;
}

.ec-responsivelayout--paper-variant .ec-responsivelayout__container {
  grid-template-columns: 1fr;
  grid-template-rows: auto;
}

.ec-responsivelayout--paper-variant .ec-responsivelayout__slot--aside-top {
  order: -1;
}

.ec-responsivelayout--layout-mode .ec-responsivelayout__container {
  padding: 0 4px 4px;
  gap: 16px;
}

.ec-responsivelayout--layout-mode .ec-layout-item {
  border-radius: 4px;
  grid-template-rows: auto 1fr;
  gap: 8px;
}

.ec-responsivelayout--layout-mode .ec-draggable-area {
  padding: 0 8px 8px;
}

.ec-responsivelayout--layout-mode .ec-responsivelayout__slot {
  padding: 0 8px;
}

.ec-responsivelayout__slot--main {
  grid-row: 2 span;
}

.ec-responsivelayout__aside {
  gap: 1px;
}
</style>
