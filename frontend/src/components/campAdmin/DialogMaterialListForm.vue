<template>
  <e-form name="materialList">
    <e-text-field
      v-model="localMaterialList.name"
      name="name"
      autofocus
      vee-rules="required"
    />
  </e-form>
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
