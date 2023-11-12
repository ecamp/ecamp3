<template>
  <div
    class="tw-flex tw-bg-black"
    :class="{
      'tw-flex-col': direction === 'column',
      'tw-flex-wrap tw-gap-x-px -tw-mx-4': direction === 'row',
    }"
  >
    <div
      v-for="child in children"
      :key="child.id"
      class="tw-bg-white"
      :class="{
        'tw-px-4 tw-flex-1 tw-basis-[320px]': direction === 'row',
      }"
    >
      <content-node :content-node="child" />
    </div>
  </div>
</template>

<script>
export default {
  components: { ContentNode: () => import('./ContentNode.vue') },
  props: {
    contentNode: { type: Object, required: true },
    columnSlot: { type: String, required: true },
    direction: { type: String, required: true },
  },
  computed: {
    column() {
      return this.contentNode.data.items.find((column) => column.slot === this.columnSlot)
    },
    children() {
      return this.contentNode
        .children()
        .items.filter((child) => child.slot === this.columnSlot)
        .sort((child1, child2) => parseInt(child1.position) - parseInt(child2.position))
    },
  },
}
</script>

<style lang="scss">
.ec-col {
  flex: 0 0 100%;
}

$step: (500-100) / 12;
@for $i from 1 through 12 {
  .ec-col-#{$i} {
    flex: $i 0 0;
  }
}
</style>
