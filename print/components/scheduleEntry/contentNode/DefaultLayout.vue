<template>
  <div class="tw-flex tw-flex-col">
    <FlexItem :content-node="contentNode" direction="row" column-slot="aside-top" />
    <FlexItem :content-node="contentNode" direction="column" column-slot="main" />
    <FlexItem :content-node="contentNode" direction="row" column-slot="aside-bottom" />
  </div>
</template>

<script>
import FlexItem from './FlexItem.vue'
export default {
  components: { FlexItem },
  props: {
    contentNode: { type: Object, required: true },
  },
}
</script>
