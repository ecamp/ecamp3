<template>
  <component :is="contentNode.contentTypeName"
             v-if="!contentNode.loading"
             :class="{ 'draggable-cursor': draggable && contentNode.parent !== null }"
             :content-node="contentNode"
             :layout-mode="layoutMode"
             v-bind="$attrs" />
</template>

<script>
const context = require.context('@/components/activity/content', false)
const contentNodeComponents = Object.fromEntries(context.keys().map(contentNodeComponent => {
  const component = context(contentNodeComponent).default
  return [component.name, component]
}))

export default {
  name: 'ContentNode',
  components: contentNodeComponents,
  props: {
    contentNode: { type: Object, required: true },
    layoutMode: { type: Boolean, required: true },
    draggable: { type: Boolean, default: false }
  }
}
</script>

<style lang="scss">
.draggable-cursor {
  cursor: pointer;

  &:hover {
    background: map-get($blue-grey, 'lighten-5');
  }

  .theme--light.v-toolbar.v-sheet {
    background: none;
  }
}

.draggable-cursor [disabled] {
  cursor: move;
}
</style>
