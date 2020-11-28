<template>
  <tr>
    <td class="text-align-right">
      <api-text-field
        dense
        :outlined="false"
        :uri="item.materialItem._meta.self"
        fieldname="quantity" />
    </td>
    <td>
      <api-text-field
        dense
        :outlined="false"
        :uri="item.materialItem._meta.self"
        fieldname="unit" />
    </td>
    <td>
      <api-text-field
        dense
        :outlined="false"
        :uri="item.materialItem._meta.self"
        fieldname="article" />
    </td>
    <td />
    <td>
      <v-btn
        small
        @click="deleteMaterialItem(item.materialItem)">
        {{ $tc('global.button.delete') }}
      </v-btn>
    </td>
  </tr>
</template>

<script>
import ApiTextField from '../form/api/ApiTextField.vue'

export default {
  name: 'MaterialListItemPeriod',
  components: {
    ApiTextField
  },
  props: {
    item: { type: Object, required: true }
  },
  methods: {
    deleteMaterialItem (materialItem) {
      this.api.del(materialItem)
    }
  }
}
</script>

<style scoped>
  .text-align-right >>> .v-text-field .v-input__slot input {
    text-align: right;
    margin-right: 5px;
  }
</style>
