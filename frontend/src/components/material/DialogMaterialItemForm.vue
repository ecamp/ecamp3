<template>
  <e-form name="materialItem">
    <e-text-field
      v-model.number="localMaterialItem.quantity"
      name="quantity"
      inputmode="numeric"
      autofocus
    />

    <e-text-field v-model="localMaterialItem.unit" name="unit" maxlength="32" />

    <e-text-field
      v-model="localMaterialItem.article"
      name="article"
      vee-rules="required"
      maxlength="64"
    />

    <e-select
      v-model="localMaterialItem.materialList"
      dense
      vee-rules="required"
      name="materialList"
      :label="$tc('entity.materialList.name')"
      :items="materialListArray"
    />
  </e-form>
</template>

<script>
import EForm from '@/components/form/base/EForm.vue'

export default {
  name: 'DialogMaterialItemForm',
  components: { EForm },
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
