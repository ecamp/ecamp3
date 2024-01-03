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
import EForm from '@/components/form/base/EForm.vue'

export default {
  name: 'DialogMaterialListForm',
  components: { EForm },
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
