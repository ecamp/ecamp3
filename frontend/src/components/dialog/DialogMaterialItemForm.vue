<template>
  <div>
    <e-text-field
      v-model="materialItem.quantity"
      :name="$tc('entity.materialItem.fields.quantity')"
      autofocus />
    <e-text-field
      v-model="materialItem.unit"
      :name="$tc('entity.materialItem.fields.unit')" />
    <e-text-field
      v-model="materialItem.article"
      :name="$tc('entity.materialItem.fields.article')"
      vee-rules="required" />
    <e-select
      v-model="materialItem.materialListId"
      dense
      vee-rules="required"
      :name="$tc('entity.materialList.name')"
      fieldname="materialListId"
      :items="materialLists" />
  </div>
</template>

<script>
export default {
  name: 'DialogMaterialItemForm',
  props: {
    camp: { type: Object, required: true },
    materialItem: { type: Object, required: true }
  },
  computed: {
    materialLists () {
      return this.camp.materialLists().items.map(l => ({
        value: l.id,
        text: l.name
      }))
    }
  }
}
</script>
