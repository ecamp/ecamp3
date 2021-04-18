<template>
  <card-content-node v-bind="$props">
    <div class="mb-3">
      <!--
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
      </v-simple-table> -->

      <v-data-table
        :headers="headers"
        :items="materialItemsData"
        :disable-pagination="true"
        hide-default-footer>
        <template #[`item.quantity`]="{ item }">
          <api-text-field
            v-if="!item.new"
            dense
            :uri="item.uri"
            fieldname="quantity" />
          <span v-if="item.new">{{ item.quanity }}</span>
        </template>
        <template #[`item.unit`]="{ item }">
          <api-text-field
            v-if="!item.new"
            dense
            :uri="item.uri"
            fieldname="unit" />
          <span v-if="item.new">{{ item.unit }}</span>
        </template>
        <template #[`item.article`]="{ item }">
          <api-text-field
            v-if="!item.new"
            dense
            :uri="item.uri"
            fieldname="article" />
          <span v-if="item.new">{{ item.article }}</span>
        </template>
        <template #[`item.listName`]="{ item }">
          <api-select
            v-if="!item.new"
            dense
            :uri="item.uri"
            relation="materialList"
            fieldname="materialListId"
            :items="materialLists" />
          <span v-if="item.new">{{ item.listName }}</span>
        </template>
        <template #[`item.actions`]="{ item }">
          <v-icon
            v-if="!item.new"
            small
            @click="deleteMaterialItem(item)">
            mdi-delete
          </v-icon>
          <v-progress-circular
            v-if="item.new"
            size="16"
            indeterminate
            color="primary" />
        </template>
        <template #no-data>
          <v-btn
            color="primary">
            No material items found
          </v-btn>
        </template>

        <template v-if="!layoutMode" #[`body.append`]>
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
        </template>
      </v-data-table>
    </div>
  </card-content-node>
</template>

<script>
import ApiTextField from '../../form/api/ApiTextField.vue'
import ApiSelect from '../../form/api/ApiSelect.vue'
import DialogMaterialItemCreate from '../../dialog/DialogMaterialItemCreate.vue'
// import DialogMaterialItemEdit from '../../dialog/DialogMaterialItemEdit.vue'
import MaterialCreateItem from '../../camp/MaterialCreateItem.vue'
import CardContentNode from '@/components/activity/CardContentNode.vue'
import { contentNodeMixin } from '@/mixins/contentNodeMixin.js'

export default {
  name: 'Material',
  components: {
    CardContentNode,
    ApiTextField,
    ApiSelect,
    DialogMaterialItemCreate,
    /* DialogMaterialItemEdit, */
    MaterialCreateItem
  },
  mixins: [contentNodeMixin],
  data () {
    return {
      newMaterialItems: {},
      headers: [
        {
          text: this.$tc('entity.materialItem.fields.quantity'),
          align: 'start',
          sortable: false,
          value: 'quantity',
          width: '10%'
        },
        { text: this.$tc('entity.materialItem.fields.unit'), value: 'unit', sortable: false, width: '10%' },
        { text: this.$tc('entity.materialItem.fields.article'), value: 'article', width: '40%' },
        { text: this.$tc('entity.materialList.name'), value: 'listName', width: '35%' },
        { text: '', value: 'actions', sortable: false, width: '5%' }
      ]
    }
  },
  computed: {
    materialLists () {
      return this.camp.materialLists().items.map(l => ({
        value: l.id,
        text: l.name
      }))
    },
    materialItems () {
      return this.api.get().materialItems({ contentNodeId: this.contentNode.id })
    },
    materialItemsData () {
      const items = this.materialItems.items.map(item => ({
        id: item.id,
        uri: item._meta.self,
        quantity: item.quantity,
        unit: item.unit,
        article: item.article,
        listId: item.materialList().id,
        listName: item.materialList().name
      }))

      // eager add new Items
      for (const key in this.newMaterialItems) {
        const mi = this.newMaterialItems[key]
        items.push({
          id: key,
          quantity: mi.quantity,
          unit: mi.unit,
          article: mi.article,
          new: true
        })
      }

      return items
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
      this.api.del(materialItem.uri)
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
