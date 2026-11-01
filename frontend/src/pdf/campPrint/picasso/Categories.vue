<template>
  <View class="picasso-categories">
    <View v-for="category in categories" class="picasso-category">
      <CategoryLabel :category="category" :line-font-size="9" />
      <Text>{{ category.name }}</Text>
    </View>
  </View>
</template>
<script>
import PdfComponent from '@/pdf/PdfComponent.js'
import CategoryLabel from '../CategoryLabel.vue'

export default {
  name: 'Categories',
  components: { CategoryLabel },
  extends: PdfComponent,
  props: {
    period: { type: Object, required: true },
    scheduleEntries: { type: Array, default: () => [] },
  },
  computed: {
    categories() {
      const usedCategoryUris = [
        ...new Set(
          this.scheduleEntries.map(
            (scheduleEntry) => scheduleEntry.activity().category()._meta.self
          )
        ),
      ]
      return usedCategoryUris.map((uri) => this.api.get(uri))
    },
  },
}
</script>
<style lang="react-pdf">
.picasso-categories {
  font-size: 9pt;
  display: flex;
  flex-direction: row;
  justify-content: center;
  flex-wrap: wrap;
  column-gap: 10pt;
  row-gap: 1pt;
}
.picasso-category {
  flex-direction: row;
  align-items: flex-start;
  gap: 2pt;
}
</style>
