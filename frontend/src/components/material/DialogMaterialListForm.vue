<template>
  <div>
    <e-text-field
      v-model="localMaterialList.name"
      :name="$tc('entity.materialList.fields.name')"
      autofocus
      vee-rules="required"
    />
  </div>
</template>

<script>
export default {
  name: 'DialogMaterialListForm',
  props: {
    materialList: { type: Object, required: true },
  },
  computed: {
    localMaterialList() {
      return this.materialList
    },
  },
}
</script>
