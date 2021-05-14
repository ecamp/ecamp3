<template>
  <div>
    <e-text-field
      v-model="localMaterialItem.quantity"
      :name="$tc('entity.materialItem.fields.quantity')"
      autofocus />
    <e-text-field
      v-model="localMaterialItem.unit"
      :name="$tc('entity.materialItem.fields.unit')" />
    <e-text-field
      v-model="localMaterialItem.article"
      :name="$tc('entity.materialItem.fields.article')"
      vee-rules="required" />
    <e-select
      v-model="localMaterialItem.materialListId"
      dense
      vee-rules="required"
      :name="$tc('entity.materialList.name')"
      fieldname="materialListId"
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
      return this.materialLists().items.map(l => ({
        value: l.id,
        text: l.name
      }))
    }
  }
}
</script>
