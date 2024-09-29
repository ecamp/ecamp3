<template>
  <div class="tw-flex tw-flex-col">
    <FlexItem
      v-if="children['aside-top']"
      :children="children['aside-top']"
      direction="row"
    />
    <FlexItem v-if="children['main']" :children="children['main']" direction="column" />
    <FlexItem
      v-if="children['aside-bottom']"
      :children="children['aside-bottom']"
      direction="row"
    />
  </div>
</template>

<script>
import groupBy from 'lodash/groupBy.js'
import sortBy from 'lodash/sortBy.js'
import FlexItem from './FlexItem.vue'
export default {
  components: { FlexItem },
  props: {
    contentNode: { type: Object, required: true },
  },
  computed: {
    children() {
      return groupBy(
        sortBy(this.contentNode.children().items, (child) => parseInt(child.position)),
        (child) => child.slot
      )
    },
  },
}
</script>
