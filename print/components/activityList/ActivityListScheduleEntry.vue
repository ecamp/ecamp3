<template>
  <generic-error-message v-if="error" :error="error" />
  <div v-else class="tw-mb-20 tw-mt-1">
    <ScheduleEntryTitle :schedule-entry="scheduleEntry" :index="index" />

    <ContentNodeComponent
      v-for="contentNode in contentNodeEntries"
      :key="contentNode.id"
      :content-node="contentNode"
    />
  </div>
</template>

<script setup>
const props = defineProps({
  scheduleEntry: { type: Object, required: true },
  contentTypes: { type: Array, required: true },
  contentNodes: { type: Array, required: true },
  index: { type: Number, required: true },
})

const { error } = await useAsyncData(
  `ActiityListScheduleEntry-${props.scheduleEntry._meta.self}`,
  async () => {
    return await Promise.all([
      props.scheduleEntry._meta.load,
      props.scheduleEntry.activity()._meta.load,
      props.scheduleEntry.activity().rootContentNode()._meta.load,
    ])
  }
)
</script>

<script>
import ContentNodeComponent from '@/components/scheduleEntry/contentNode/ContentNode.vue'

import { sortBy } from 'lodash'

export default defineNuxtComponent({
  components: { ContentNodeComponent },
  computed: {
    contentNodeEntries() {
      return sortBy(
        this.contentNodes
          .map((contentNodeList) =>
            contentNodeList.items.filter(
              (contentNode) =>
                contentNode.root()._meta.self ===
                this.scheduleEntry.activity().rootContentNode()._meta.self
            )
          )
          .flat(),
        ['parent', 'slot', 'position']
      )
    },
  },
})
</script>

<style lang="scss" scoped></style>
