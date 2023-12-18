<template>
  <div
    class="tw-flex tw-overflow-hidden"
    :class="{
      'tw-flex-col': direction === 'column',
      'tw-flex-wrap -tw-mx-4': direction === 'row',
    }"
  >
    <div
      v-for="child in children"
      :key="child.id"
      class="tw-bg-white"
      :class="{
        'tw-px-4 -tw-ml-px tw-border-l tw-border-black tw-border-solid tw-flex-1 tw-basis-[320px]':
          direction === 'row',
      }"
    >
      <ContentNodeComponent :content-node="child" />
    </div>
  </div>
</template>

<script setup>
import ContentNodeComponent from './ContentNode.vue'
</script>

<script>
export default defineNuxtComponent({
  props: {
    children: { type: Array, required: true },
    direction: { type: String, required: true },
  },
})
</script>
