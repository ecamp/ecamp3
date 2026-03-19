<template>
  <e-form name="materialList">
    <e-text-field
      v-model="localMaterialList.name"
      path="name"
      autofocus
      vee-rules="required"
      maxlength="32"
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
