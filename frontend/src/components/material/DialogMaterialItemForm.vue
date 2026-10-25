<template>
  <e-form name="materialItem">
    <e-number-field
      v-model="localMaterialItem.quantity"
      path="quantity"
      vee-rules="greaterThan:0"
      inputmode="decimal"
      autofocus
    />
    <e-text-field v-model="localMaterialItem.unit" path="unit" maxlength="32" />
    <e-text-field
      v-model="localMaterialItem.article"
      path="article"
      vee-rules="required"
      maxlength="64"
    />
    <MaterialListSelect
      v-model="localMaterialItem.materialList"
      :camp="camp"
      path="materialList"
      density="compact"
      vee-rules="required"
      @update:creating="$emit('update:creating', $event)"
    />
  </e-form>
</template>

<script>
import MaterialListSelect from './MaterialListSelect.vue'

export default {
  name: 'DialogMaterialItemForm',
  components: { MaterialListSelect },
  props: {
    camp: { type: Object, required: true },
    materialItem: { type: Object, required: true },
  },
  emits: ['update:creating'],
  computed: {
    localMaterialItem() {
      return this.materialItem
    },
  },
}
</script>
