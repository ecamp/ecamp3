<template>
  <div class="tw-mb-3">
    <div v-if="instanceName" class="tw-font-bold">{{ instanceName }}</div>
    <table class="tw-w-full">
      <tr>
        <th class="column column1 header tw-w-1/12">
          {{ $tc('contentNode.storyboard.entity.section.fields.column1') }}
        </th>
        <th class="column column2 header tw-w-10/12">
          {{ $tc('contentNode.storyboard.entity.section.fields.column2') }}
        </th>
        <th class="column column3 header tw-w-1/12">
          {{ $tc('contentNode.storyboard.entity.section.fields.column3') }}
        </th>
      </tr>
      <tr v-for="section in sections" :key="section.id">
        <td class="column column1">
          <rich-text :rich-text="section.column1" />
        </td>
        <td class="column column2">
          <rich-text :rich-text="section.column2" />
        </td>
        <td class="column column3">
          <rich-text :rich-text="section.column3" />
        </td>
      </tr>
    </table>
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
.header {
  padding-bottom: 0;
  border-bottom: 1px solid rgb(148 163 184);
}

.column {
  padding-bottom: 8px;
  vertical-align: top;
}

.column1 {
  padding-right: 4px;
}

.column2 {
  border-left: 1px solid rgb(148 163 184);
  padding-left: 4px;
  padding-right: 4px;
}

.column3 {
  border-left: 1px solid rgb(148 163 184);
  padding-left: 4px;
}
</style>
