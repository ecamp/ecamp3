<template>
  <e-form name="materialItem">
    <e-number-field
      v-model="localMaterialItem.quantity"
      path="quantity"
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
    <e-select
      v-model="localMaterialItem.materialList"
      dense
      vee-rules="required"
      :label="$tc('entity.materialList.name')"
      :items="materialListArray"
    />
  </e-form>
</template>

<script>
export default {
  name: 'DialogMaterialItemForm',
  props: {
    materialLists: { type: Object, required: true },
    materialItem: { type: Object, required: true },
  },
  computed: {
    localMaterialItem() {
      return this.materialItem
    },
    materialListArray() {
      return this.materialLists.items.map((l) => ({
        value: l._meta.self,
        text: l.name,
      }))
    },
  },
}
</script>
