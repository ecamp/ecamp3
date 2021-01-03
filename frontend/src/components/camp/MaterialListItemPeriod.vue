<template>
  <tr v-if="$vuetify.breakpoint.smAndUp">
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
        {{ $tc('global.button.delete') }}
      </v-btn>
    </td>
  </tr>
  <tr v-else>
    <td class="font-size-16 text-align-bottom">
      <div class="text-align-right">
        {{ item.materialItem.quantity }}
      </div>
    </td>
    <td class="font-size-16 text-align-bottom">
      {{ item.materialItem.unit }}
    </td>
    <td class="font-size-16 text-align-bottom">
      {{ item.materialItem.article }}
    </td>
    <td style="text-align: center;">
      <dialog-material-item-edit :material-item="item.materialItem">
        <template #activator="{ on }">
          <v-btn
            small
            class="short-button"
            v-on="on">
            <v-icon small>mdi-pen</v-icon>
          </v-btn>
        </template>
      </dialog-material-item-edit>
    </td>
  </tr>
</template>

<script>
import ApiTextField from '../form/api/ApiTextField.vue'
import DialogMaterialItemEdit from '../dialog/DialogMaterialItemEdit'

export default {
  name: 'MaterialListItemPeriod',
  components: {
    ApiTextField,
    DialogMaterialItemEdit
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
  .text-align-right {
    text-align: right;
    padding-right: 9px !important;
  }
  .text-align-right >>> .v-text-field .v-input__slot input {
    text-align: right;
    padding-right: 5px;
  }
  .text-align-bottom {
    vertical-align: bottom;
  }
  .font-size-16 {
    font-size: 16px !important;
  }
</style>
