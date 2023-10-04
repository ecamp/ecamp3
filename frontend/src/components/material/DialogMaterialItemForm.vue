<template>
  <div>
    <e-text-field
      v-model.number="localMaterialItem.quantity"
      :name="$tc('entity.materialItem.fields.quantity')"
      inputmode="decimal"
      autofocus
    />
    <e-text-field
      v-model="localMaterialItem.unit"
      :name="$tc('entity.materialItem.fields.unit')"
      maxlength="32"
    />
    <e-text-field
      v-model="localMaterialItem.article"
      :name="$tc('entity.materialItem.fields.article')"
      vee-rules="required"
      maxlength="64"
    />
    <e-select
      v-model="localMaterialItem.materialList"
      dense
      vee-rules="required"
      :name="$tc('entity.materialList.name')"
      fieldname="materialList"
      :items="materialListArray"
    />
  </div>
</template>

<script>
export default {
  name: 'DialogMaterialItemForm',
  props: {
    materialLists: { type: Function, required: true },
    materialItem: { type: Object, required: true },
  },
  computed: {
    localMaterialItem() {
      return this.materialItem
    },
    materialListArray() {
      return this.materialLists().items.map((l) => ({
        value: l._meta.self,
        text: l.name,
      }))
    },
  },
}
</script>
