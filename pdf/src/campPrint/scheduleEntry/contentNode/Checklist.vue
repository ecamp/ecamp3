<template>
  <View class="content-node">
    <InstanceName :content-node="contentNode" />

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
import uniqWith from 'lodash/uniqWith.js'
import sortBy from 'lodash/sortBy.js'

const props = defineProps({
  contentNode: { type: Object, required: true },
})

function calculateItemNumber(item) {
  if (!item.parent) {
    return item.position + 1
  }

  return calculateItemNumber(item.parent()) + '.' + (item.position + 1)
}
const items = props.contentNode.checklistItems().items.map((item) => {
  const number = calculateItemNumber(item)
  return {
    ...item,
    number,
  }
})

const checklists = uniqWith(
  props.contentNode
    .checklistItems()
    .items.map((checklistItem) => checklistItem.checklist()),
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
import PdfComponent from '@/PdfComponent.js'
import InstanceName from '../InstanceName.vue'

export default {
  name: 'Checklist',
  components: { InstanceName },
  extends: PdfComponent,
}
</script>
<pdf-style>
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
  font-variant-numeric: tabular-nums;
}
.checklist-item-column-2 {
  flex-basis: 0;
  flex-grow: 1;
  padding-left: 2pt;
}
</pdf-style>
