<template>
  <table>
    <caption v-if="instanceName" class="instance-name">{{ instanceName }}</caption>
    <thead>
      <tr>
      <th style="white-space: nowrap">
        {{ $tc('contentNode.storyboard.entity.section.fields.column1') }}
      </th>
      <th>
        {{ $tc('contentNode.storyboard.entity.section.fields.column2') }}
      </th>
      <th style="white-space: nowrap">
        {{ $tc('contentNode.storyboard.entity.section.fields.column3') }}
      </th>
      </tr>
    </thead>
    <tbody>
    <tr v-for="section in sections" :key="section.id">
      <th>
        <rich-text :rich-text="section.column1" />
      </th>
      <td>
        <rich-text :rich-text="section.column2" />
      </td>
      <td>
        <rich-text :rich-text="section.column3" />
      </td>
    </tr>
    </tbody>
  </table>
</template>

<script>
import RichText from '../../RichText.vue'

export default {
  components: {
    RichText,
  },
  props: {
    contentNode: { type: Object, required: true },
  },
  async fetch() {
    await this.contentNode.sections().$loadItems()
  },
  computed: {
    instanceName() {
      return this.contentNode.instanceName
    },
    sections() {
      return this.contentNode
        .sections()
        .items.sort(
          (section1, section2) => section1.position - section2.position
        )
    },
  },
}
</script>

<style scoped lang="scss">
table {
  border-collapse: collapse;
  border: 1px solid;
}
th, td {
  padding: 5px;
  vertical-align: baseline;
  border: 1px solid;
}
.wrapper {
  margin-bottom: 12px;
}
.instance-name {
  font-weight: bold;
}
.storyboard-row {
  display: flex;
  flex-direction: row;
}
.header {
  padding-bottom: 0;
  border-bottom: 1px solid black;
}
.column {
  flex-grow: 1;
  line-height: 1.6;
  padding-bottom: 8px;
}
.column1 {
  flex-basis: 26px;
  flex-shrink: 0;
  flex-grow: 0;
  padding-right: 4px;
}
.column2 {
  flex-grow: 1;
  border-left: 1px solid black;
  padding-left: 4px;
  padding-right: 4px;
}
.column3 {
  flex-basis: 80px;
  flex-shrink: 0;
  flex-grow: 0;
  border-left: 1px solid black;
  padding-left: 4px;
}
</style>
