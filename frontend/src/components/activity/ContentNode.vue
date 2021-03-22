<template>
  <component :is="contentNode.contentTypeName" v-if="!contentNode.loading" :content-node="contentNode" />
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
    contentNode: { type: Object, required: true }
  }
}
</script>

<style scoped>
</style>
