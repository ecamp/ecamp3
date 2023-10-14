<template>
  <div
    class="ec-flexlayout"
    :class="{
      'ec-flexlayout--layout-mode': layoutMode,
      'ec-flexlayout--read-mode': !layoutMode,
      'ec-flexlayout--main-aside': currentLayout.name === 'main-aside',
      'ec-flexlayout--aside-main': currentLayout.name === 'aside-main',
    }"
  >
    <div v-if="layoutMode" class="text-center d-flex align-center justify-center gap-1">
      <span>Automatic Layout </span>
      <v-btn-toggle
        :value="currentLayoutName"
        small
        dense
        color="primary"
        @change="saveData"
      >
        <v-btn
          v-for="variant in variants"
          :key="variant.name"
          small
          :value="variant.name"
          @click="currentLayoutName = variant.name"
        >
          <v-icon dense>$vuetify.icons.{{ variant.icon }}</v-icon>
        </v-btn>
      </v-btn-toggle>

      <menu-cardless-content-node :content-node="contentNode" />
    </div>
    <div v-if="!contentNode.loading" class="ec-flexlayout__container">
      <LayoutItem
        v-for="(item, slot) in currentLayout.items"
        :key="slot + 1"
        :parent-content-node="contentNode"
        :layout-mode="layoutMode"
        :basis="item.basis"
        :grow="item.grow"
      >
        <draggable-content-nodes
          :slot-name="slot + 1 + ''"
          :layout-mode="layoutMode"
          :parent-content-node="contentNode"
          :disabled="disabled"
          :direction="item.direction"
        />
      </LayoutItem>
    </div>
  </div>
</template>

<script>
import { contentNodeMixin } from '@/mixins/contentNodeMixin.js'
import DraggableContentNodes from '@/components/activity/DraggableContentNodes.vue'
import { errorToMultiLineToast } from '@/components/toast/toasts'
import LayoutItem from '@/components/activity/content/LayoutItem.vue'
import MenuCardlessContentNode from '@/components/activity/MenuCardlessContentNode.vue'

export default {
  name: 'FlexLayout',
  components: {
    MenuCardlessContentNode,
    LayoutItem,
    DraggableContentNodes,
  },
  mixins: [contentNodeMixin],
  data() {
    return {
      currentLayoutName: 'main-aside',
      variants: [
        {
          name: 'aside-main',
          icon: 'layoutAsideMain',
          items: [
            { slot: '0', grow: 1, basis: '320px', direction: 'row' },
            { slot: '1', grow: 3, basis: '480px', direction: 'column' },
          ],
        },
        {
          name: 'main-aside',
          icon: 'layoutMainAside',
          items: [
            { slot: '0', grow: 3, basis: '480px', direction: 'column' },
            { slot: '1', grow: 1, basis: '320px', direction: 'row' },
          ],
        },
      ],
    }
  },
  computed: {
    dataLayoutName() {
      return this.contentNode.data.variant
    },
    currentLayout() {
      return this.variants.find((variant) => variant.name === this.currentLayoutName)
    },
    isRoot() {
      return this.contentNode._meta.self === this.contentNode.root()._meta.self
    },
  },
  watch: {
    dataLayoutName: {
      immediate: true,
      handler() {
        this.currentLayoutName = this.dataLayoutName
      },
    },
  },
  methods: {
    async saveData() {
      const payload = {
        data: {
          items: this.contentNode.data.items.map((item) => ({ slot: item.slot })),
          variant: this.currentLayout.name,
        },
      }
      try {
        await this.api.patch(this.contentNode, payload)
      } catch (e) {
        this.$toast.error(errorToMultiLineToast(e))
      }
    },
  },
}
</script>

<style lang="scss">
.ec-flexlayout--layout-mode {
  border: 1px solid black;
  border-radius: 10px;
}

.ec-flexlayout__container {
  display: flex;
  gap: 1px;
}

.ec-flexlayout--aside-main .ec-flexlayout__container {
  flex-wrap: wrap;
}

.ec-flexlayout--main-aside .ec-flexlayout__container {
  flex-wrap: wrap-reverse;
}

.ec-flexlayout--layout-mode .ec-flexlayout__container {
  padding: 4px;
  gap: 16px;
}

.ec-flexlayout__container {
  background-color: #ccc;
  border-bottom-left-radius: 9px;
  border-bottom-right-radius: 9px;
}

.ec-flexlayout--layout-mode .ec-layout-item {
  border-radius: 4px;
  grid-template-rows: auto 1fr;
  gap: 8px;
}

.ec-flexlayout--layout-mode .ec-draggable-area {
  padding: 0 8px 8px;
}
</style>
