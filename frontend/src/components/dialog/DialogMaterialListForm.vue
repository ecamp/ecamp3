<template>
  <div>
    <e-text-field
      v-model="materialList.name"
      :name="$tc('entity.materialList.fields.name')"
      autofocus
      vee-rules="required" />
  </div>
</template>

<script>
export default {
  name: 'DialogMaterialListForm',
  props: {
    materialList: { type: Object, required: true }
  }
}
</script>
