<template>
  <div class="wrapper">
    <div v-if="instanceName" class="instance-name">{{ instanceName }}</div>
    <div class="storyboard-row">
      <div class="column column1 header">
        {{ $tc('contentNode.storyboard.entity.section.fields.column1') }}
      </div>
      <div class="column column2 header">
        {{ $tc('contentNode.storyboard.entity.section.fields.column2') }}
      </div>
      <div class="column column3 header">
        {{ $tc('contentNode.storyboard.entity.section.fields.column3') }}
      </div>
    </div>
    <div v-for="section in sections" :key="section.id" class="storyboard-row">
      <div class="column column1">
        <rich-text :rich-text="section.column1" />
      </div>
      <div class="column column2">
        <rich-text :rich-text="section.column2" />
      </div>
      <div class="column column3">
        <rich-text :rich-text="section.column3" />
      </div>
    </div>
  </div>
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
