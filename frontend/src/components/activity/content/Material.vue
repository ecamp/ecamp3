<template>
  <div class="mb-3">
    <v-simple-table dense>
      <colgroup>
        <col style="width: 55px;">
        <col style="width: 15%;">
        <col>
        <col style="width: 20%;">
        <col style="width: 20%;">
      </colgroup>
      <thead>
        <tr>
          <th class="text-left" colspan="2">
            {{ $tc("entity.materialItem.fields.quantity") }}
          </th>
          <th class="text-left">
            {{ $tc("entity.materialItem.fields.article") }}
          </th>
          <th v-if="$vuetify.breakpoint.smAndUp" class="text-left">
            {{ $tc('entity.materialList.name') }}
          </th>
          <th class="text-left">
            Option
          </th>
        </tr>
      </thead>
      <tbody>
        <template v-for="materialItem in materialItemsSorted">
          <tr v-if="materialItem._meta != undefined && $vuetify.breakpoint.smAndUp" :key="materialItem.id">
            <td class="text-align-right">
              <api-text-field
                dense
                :outlined="false"
                :uri="materialItem._meta.self"
                fieldname="quantity" />
            </td>
            <td>
              <api-text-field
                dense
                :outlined="false"
                :uri="materialItem._meta.self"
                fieldname="unit" />
            </td>
            <td>
              <api-text-field
                dense
                :outlined="false"
                :uri="materialItem._meta.self"
                fieldname="article" />
            </td>
            <td>
              <api-select
                dense
                :outlined="false"
                :uri="materialItem._meta.self"
                relation="materialList"
                fieldname="materialListId"
                :items="materialLists" />
            </td>
            <td style="text-align: center;">
              <v-btn
                small
                class="short-button"
                @click="deleteMaterialItem(materialItem)">
                <template v-if="$vuetify.breakpoint.smAndUp">
                  {{ $tc('global.button.delete') }}
                </template>
                <v-icon v-else>mdi-trash-can-outline</v-icon>
              </v-btn>
            </td>
          </tr>
          <tr v-else-if="materialItem._meta != undefined" :key="materialItem.id">
            <td class="font-size-16 text-align-bottom">
              <div class="text-align-right">
                {{ materialItem.quantity }}
              </div>
            </td>
            <td class="font-size-16 text-align-bottom">
              {{ materialItem.unit }}
            </td>
            <td class="font-size-16 text-align-bottom">
              {{ materialItem.article }}
            </td>
            <td style="text-align: center;">
              <dialog-material-item-edit :material-item="materialItem">
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
          <tr v-else :key="materialItem.id">
            <td>{{ materialItem.quantity }}</td>
            <td>{{ materialItem.unit }}</td>
            <td>{{ materialItem.article }}</td>
            <td />
            <td />
          </tr>
        </template>
      </tbody>
    </v-simple-table>

    <material-create-item
      v-if="$vuetify.breakpoint.smAndUp"
      :camp="camp"
      :content-node="contentNode"
      @item-adding="onItemAdding" />

    <div v-else style="margin-top: 20px; text-align: right">
      <dialog-material-item-create :camp="camp" :content-node="contentNode">
        <template #activator="{ on }">
          <v-btn color="success" v-on="on">
            {{ $tc('components.camp.periodMaterialLists.addNewItem') }}
          </v-btn>
        </template>
      </dialog-material-item-create>
    </div>
  </div>
</template>

<script>
import ApiTextField from '../../form/api/ApiTextField.vue'
import ApiSelect from '../../form/api/ApiSelect.vue'
import DialogMaterialItemCreate from '../../dialog/DialogMaterialItemCreate.vue'
import DialogMaterialItemEdit from '../../dialog/DialogMaterialItemEdit.vue'
import MaterialCreateItem from '../../camp/MaterialCreateItem.vue'

export default {
  name: 'Material',
  components: {
    ApiTextField,
    ApiSelect,
    DialogMaterialItemCreate,
    DialogMaterialItemEdit,
    MaterialCreateItem
  },
  props: {
    contentNode: { type: Object, required: true }
  },
  data () {
    return {
      newMaterialItems: {}
    }
  },
  computed: {
    camp () {
      return this.contentNode.activity().camp()
    },
    materialLists () {
      return this.camp.materialLists().items.map(l => ({
        value: l.id,
        text: l.name
      }))
    },
    materialItems () {
      return this.api.get().materialItems({ contentNodeId: this.contentNode.id })
    },
    materialItemsSorted () {
      const items = this.materialItems.items

      // eager add new Items
      for (const key in this.newMaterialItems) {
        const mi = this.newMaterialItems[key]
        items.push({
          id: key,
          quantity: mi.quantity,
          unit: mi.unit,
          article: mi.article
        })
      }

      return items.sort((a, b) => a.article.localeCompare(b.article))
    }
  },
  methods: {
    materialListItems (materialList) {
      return this.materialItems.filter(mi => mi.materialList().id === materialList.id)
    },
    deleteMaterialItem (materialItem) {
      this.api.del(materialItem)
    },
    onItemAdding (key, data, res) {
      this.$set(this.newMaterialItems, key, data)

      res.then(mi => {
        this.api.reload(this.materialItems).then(() => {
          this.$delete(this.newMaterialItems, key)
        })
      })
    }
  }
}
</script>

<style scoped>
  .short-button {
    min-width: 40px !important;
    padding: 0 7px !important;
  }
  .v-data-table >>> .v-data-table__wrapper > table > thead > tr > th {
    padding: 0 2px;
  }
  .v-data-table >>> .v-data-table__wrapper > table > tbody > tr > td {
    padding: 0 2px;
  }
  .text-align-right {
    text-align: right;
    padding-right: 9px !important;
  }
  .text-align-right >>> .v-text-field .v-input__slot input {
    text-align: right;
    margin-right: 5px;
  }
  .text-align-bottom {
    vertical-align: bottom;
  }
  .font-size-16 {
    font-size: 16px !important;
  }
</style>
