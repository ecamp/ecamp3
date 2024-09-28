<template>
  <content-node-content :content-node="contentNode" :icon-path="mdiClipboardListOutline">
    <generic-error-message v-if="error" :error="error" />

    <div v-for="entry in checklistsWithItems" :key="entry.checklist.id" class="tw-mb-2">
      <div class="tw-font-bold">{{ entry.checklist.name }}</div>
      <ul class="tw-list-disc tw-list-inside">
        <li v-for="item in entry.items" :key="item.id">
          {{ item.text }}
        </li>
      </ul>
    </div>
  </content-node-content>
</template>

<script setup>
const props = defineProps({
  contentNode: { type: Object, required: true },
})

const { error } = await useAsyncData(
  `ContentNodeChecklist-${props.contentNode._meta.self}`,
  async () => {
    return await props.contentNode.checklistItems().$loadItems()
  }
)
</script>

<script>
import ContentNodeContent from './ContentNodeContent.vue'
import { mdiClipboardListOutline } from '@mdi/js'
import uniqWith from 'lodash/uniqWith.js'

export default {
  components: {
    ContentNodeContent,
  },
  data() {
    return {
      mdiClipboardListOutline,
    }
  },
  computed: {
    items() {
      return this.contentNode.checklistItems().items
    },
    checklists() {
      return uniqWith(
        this.contentNode
          .checklistItems()
          .items.map((checklistItem) => checklistItem.checklist()),
        function (checklist1, checklist2) {
          return checklist1._meta.self === checklist2._meta.self
        }
      )
    },
    checklistsWithItems() {
      return this.checklists.map((checklist) => ({
        checklist,
        items: this.items.filter(
          (item) => item.checklist()._meta.self === checklist._meta.self
        ),
      }))
    },
  },
}
</script>

<style scoped lang="scss"></style>
