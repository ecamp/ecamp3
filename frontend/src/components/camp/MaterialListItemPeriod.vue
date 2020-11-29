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
    <td style="text-align: center;">
      <v-btn
        small
        class="short-button"
        @click="deleteMaterialItem(item.materialItem)">
        <template v-if="$vuetify.breakpoint.smAndUp">
          {{ $tc('global.button.delete') }}
        </template>
        <v-icon v-else>mdi-trash-can-outline</v-icon>
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
  .short-button {
    min-width: 40px !important;
    padding: 0 7px !important;
  }
  .text-align-right >>> .v-text-field .v-input__slot input {
    text-align: right;
    padding-right: 5px;
  }
</style>
