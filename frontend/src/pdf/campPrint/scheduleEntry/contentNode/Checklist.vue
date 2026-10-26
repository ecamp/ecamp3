<template>
  <View class="content-node" :class="{ 'content-node-compact': compact }">
    <InstanceName :content-node="contentNode" :icon="mdiClipboardListOutline" />

    <View v-for="entry in checklistsWithItems" class="checklist">
      <Text class="checklist-title">{{ entry.checklist.name }}</Text>
      <View v-for="item in entry.items" class="checklist-item">
        <View class="checklist-item-column checklist-item-column-1">
          <Text>{{ item.number }}</Text>
        </View>
        <View class="checklist-item-column checklist-item-column-2">
          <Text>{{ item.text }}</Text>
        </View>
      </View>
    </View>
  </View>
</template>

<script setup>
import uniqWith from 'lodash-es/uniqWith.js'
import sortBy from 'lodash-es/sortBy.js'
import { inject } from 'vue'

const props = defineProps({
  contentNode: { type: Object, required: true },
})

function calculateItemNumber(item) {
  if (!item.parent) {
    return item.position + 1
  }

  return calculateItemNumber(item.parent()) + '.' + (item.position + 1)
}

const camp = inject('camp')
const items = checklistItemsFor(props.contentNode, camp).map((item) => {
  const number = calculateItemNumber(item)
  return {
    ...item,
    number,
  }
})

const checklists = uniqWith(
  items.map((checklistItem) => checklistItem.checklist()),
  function (checklist1, checklist2) {
    return checklist1._meta.self === checklist2._meta.self
  }
)

const checklistsWithItems = checklists.map((checklist) => ({
  checklist,
  items: sortBy(
    items.filter((item) => item.checklist()._meta.self === checklist._meta.self),
    'number'
  ),
}))
</script>

<script>
import PdfComponent from '@/pdf/PdfComponent.js'
import InstanceName from '../InstanceName.vue'
import { mdiClipboardListOutline } from '@mdi/js'

function checklistItemsFor(contentNode, camp) {
  return camp
    .checklists()
    .items.flatMap((checklist) => checklist.checklistItems().items)
    .filter((item) =>
      item
        .checklistNodes()
        .items.some(
          (checklistNode) => checklistNode._meta.self === contentNode._meta.self
        )
    )
}

export default {
  name: 'Checklist',
  components: { InstanceName },
  extends: PdfComponent,
  isEmpty(contentNode, camp) {
    return checklistItemsFor(contentNode, camp).length === 0
  },
  data() {
    return { mdiClipboardListOutline }
  },
}
</script>
<style lang="react-pdf">
.checklist {
  display: flex;
  flex-direction: column;
  margin-bottom:8pt;
}
.checklist-title{
  font-weight:bold;
  margin-bottom:3pt;
  margin-top:2pt;
}
.checklist-item {
  display: flex;
  flex-direction: row;
  padding-bottom:5pt;
}
.checklist-item-column {
  flex-grow: 1;
}
.checklist-item-column-1 {
  flex-basis: 17pt;
  flex-shrink: 0;
  flex-grow: 0;
  padding-right: 2pt;
  font-feature-settings: 'tnum';
}
.checklist-item-column-2 {
  flex-basis: 0;
  flex-grow: 1;
  padding-left: 2pt;
}
</style>
