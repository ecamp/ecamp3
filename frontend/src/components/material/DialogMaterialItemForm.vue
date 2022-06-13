<template>
  <div>
    <e-text-field
      v-model="localMaterialItem.quantity"
      :name="$tc('entity.materialItem.fields.quantity')"
      type="number"
      autofocus />
    <e-text-field
      v-model="localMaterialItem.unit"
      :name="$tc('entity.materialItem.fields.unit')" />
    <e-text-field
      v-model="localMaterialItem.article"
      :name="$tc('entity.materialItem.fields.article')"
      vee-rules="required" />
    <e-select
      v-model="localMaterialItem.materialList"
      dense
      vee-rules="required"
      :name="$tc('entity.materialList.name')"
      fieldname="materialList"
      :items="materialListArray" />
  </div>
</template>

<script>
export default {
  name: 'DialogMaterialItemForm',
  props: {
    materialLists: { type: Function, required: true },
    materialItem: { type: Object, required: true }
  },
  computed: {
    localMaterialItem () {
      return this.materialItem
    },
    materialListArray () {
      return this.materialLists().items.map((l) => ({
        value: l._meta.self,
        text: l.name
      }))
    }
  }
}
</script>
