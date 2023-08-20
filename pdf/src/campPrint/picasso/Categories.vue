<template>
  <View class="picasso-categories">
    <View v-for="category in categories" class="picasso-category">
      <CategoryLabel :category="category" style="margin-right: 2pt" />
      <Text>{{ category.name }}</Text>
    </View>
  </View>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import CategoryLabel from '../CategoryLabel.vue'

export default {
  name: 'Categories',
  components: { CategoryLabel },
  extends: PdfComponent,
  props: {
    period: { type: Object, required: true },
  },
  computed: {
    categories() {
      return this.period.camp().categories().items
    },
  },
}
</script>
<pdf-style>
.picasso-categories {
  font-size: 9pt;
  display: flex;
  flex-direction: row;
  flex-wrap: wrap;
  margin: 2pt 0 0;
}
.picasso-category {
  flex-direction: row;
  align-items: center;
  gap: 2pt;
  margin-right: 6pt;
  margin-bottom: 3pt;
}
</pdf-style>
