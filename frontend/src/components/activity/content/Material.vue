<template>
  <card-content-node v-bind="$props">
    <div class="mb-3">
      <!--
        <tbody>
          <template v-for="materialItem in materialItemsSorted">
            <tr v-if="materialItem._meta != undefined && $vuetify.breakpoint.smAndUp" :key="materialItem.id">
              // implemented
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
        mobile-breakpoint="0"
        hide-default-footer>
        <template #body="props">
          <tbody is="transition-group" name="fade">
            <tr v-for="item in props.items" :key="item.id" :class="item.new ? 'new' : ''">
              <td>
                <api-text-field
                  v-if="!item.new"
                  :disabled="layoutMode"
                  dense
                  :uri="item.uri"
                  fieldname="quantity" />
                <span v-if="item.new">{{ item.quantity }}</span>
              </td>
              <td>
                <api-text-field
                  v-if="!item.new"
                  :disabled="layoutMode"
                  dense
                  :uri="item.uri"
                  fieldname="unit" />
                <span v-if="item.new">{{ item.unit }}</span>
              </td>
              <td>
                <api-text-field
                  v-if="!item.new"
                  :disabled="layoutMode"
                  dense
                  :uri="item.uri"
                  fieldname="article" />
                <span v-if="item.new">{{ item.article }}</span>
              </td>

              <!-- Material list (hidden in mobile view) -->
              <td v-if="$vuetify.breakpoint.smAndUp">
                <api-select
                  v-if="!item.new"
                  :disabled="layoutMode"
                  dense
                  :uri="item.uri"
                  relation="materialList"
                  fieldname="materialListId"
                  :items="materialLists" />
                <span v-if="item.new">{{ item.listName }}</span>
              </td>

              <!-- Action buttons -->
              <td>
                <div v-if="!item.new" class="d-flex">
                  <!-- edit dialog (mobile only) -->
                  <dialog-material-item-edit
                    v-if="!$vuetify.breakpoint.smAndUp"
                    class="float-left"
                    :material-item-uri="item.uri">
                    <template #activator="{ on }">
                      <button-edit v-on="on" />
                    </template>
                  </dialog-material-item-edit>

                  <!-- delete button (behind menu) -->
                  <v-menu v-if="!layoutMode">
                    <template #activator="{ on, attrs }">
                      <v-btn icon v-bind="attrs" v-on="on">
                        <v-icon>mdi-dots-vertical</v-icon>
                      </v-btn>
                    </template>
                    <v-list>
                      <v-list-item @click="deleteMaterialItem(item)">
                        <v-list-item-icon>
                          <v-icon>mdi-delete</v-icon>
                        </v-list-item-icon>
                        <v-list-item-title>
                          {{ $tc('global.button.delete') }}
                        </v-list-item-title>
                      </v-list-item>
                    </v-list>
                  </v-menu>
                </div>

                <!-- loading spinner for newly added items -->
                <v-progress-circular
                  v-if="item.new"
                  size="16"
                  indeterminate
                  color="primary" />
              </td>
            </tr>

            <!-- add new item (desktop view) -->
            <material-create-item
              v-if="!layoutMode && $vuetify.breakpoint.smAndUp"
              key="addItemRow"
              :camp="camp"
              :content-node="contentNode"
              @item-adding="onItemAdding" />
          </tbody>
        </template>

        <template #footer>
          <!-- add new item (mobile view) -->
          <div v-if="!layoutMode && !$vuetify.breakpoint.smAndUp" class="mt-5">
            <dialog-material-item-create :camp="camp" :content-node="contentNode">
              <template #activator="{ on }">
                <button-add v-on="on">
                  {{ $tc('components.camp.periodMaterialLists.addNewItem') }}
                </button-add>
              </template>
            </dialog-material-item-create>
          </div>
        </template>

        <template #no-data>
          <v-btn
            color="primary">
            No material items found
          </v-btn>
        </template>
      </v-data-table>
    </div>
  </card-content-node>
</template>

<script>
import ApiTextField from '../../form/api/ApiTextField.vue'
import ApiSelect from '../../form/api/ApiSelect.vue'
import DialogMaterialItemCreate from '../../dialog/DialogMaterialItemCreate.vue'
import DialogMaterialItemEdit from '../../dialog/DialogMaterialItemEdit.vue'
import MaterialCreateItem from '../../camp/MaterialCreateItem.vue'
import CardContentNode from '@/components/activity/CardContentNode.vue'
import { contentNodeMixin } from '@/mixins/contentNodeMixin.js'
import ButtonEdit from '@/components/buttons/ButtonEdit.vue'
import ButtonAdd from '@/components/buttons/ButtonAdd.vue'

export default {
  name: 'Material',
  components: {
    CardContentNode,
    ApiTextField,
    ApiSelect,
    DialogMaterialItemCreate,
    DialogMaterialItemEdit,
    MaterialCreateItem,
    ButtonEdit,
    ButtonAdd
  },
  mixins: [contentNodeMixin],
  data () {
    return {
      newMaterialItems: {},
      headers2: [
        {
          text: this.$tc('entity.materialItem.fields.quantity'),
          align: 'start',
          sortable: false,
          value: 'quantity',
          width: '10%'
        },
        { text: this.$tc('entity.materialItem.fields.unit'), value: 'unit', sortable: false, width: '10%' },
        { text: this.$tc('entity.materialItem.fields.article'), value: 'article', width: '40%' },
        { text: this.$tc('entity.materialList.name'), value: 'listName', width: '30%' },
        { text: '', value: 'actions', sortable: false, width: '10%' }
      ]
    }
  },
  computed: {
    headers () {
      if (this.$vuetify.breakpoint.smAndUp) {
        return [
          {
            text: this.$tc('entity.materialItem.fields.quantity'),
            align: 'start',
            sortable: false,
            value: 'quantity',
            width: '10%'
          },
          { text: this.$tc('entity.materialItem.fields.unit'), value: 'unit', sortable: false, width: '10%' },
          { text: this.$tc('entity.materialItem.fields.article'), value: 'article', width: '40%' },
          { text: this.$tc('entity.materialList.name'), value: 'listName', width: '30%' },
          { text: '', value: 'actions', sortable: false, width: '10%' }
        ]
      } else {
        return [
          {
            text: this.$tc('entity.materialItem.fields.quantity'),
            align: 'start',
            sortable: false,
            value: 'quantity',
            width: '10%'
          },
          { text: this.$tc('entity.materialItem.fields.unit'), value: 'unit', sortable: false, width: '10%' },
          { text: this.$tc('entity.materialItem.fields.article'), value: 'article', width: '40%' },
          { text: '', value: 'actions', sortable: false, width: '10%' }
        ]
      }
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
          listName: this.materialLists.find(listItem => listItem.value === mi.materialListId).text,
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

  .fade-enter-active, .fade-leave-active {
    transition: all 1s;
    background: #c8ebfb;
  }

   /* transitions for newly added items (remove instantly) */
   .fade-enter-active.new {
    transition: all 1s;
    background: #c8ebfb;
  }

  .fade-leave-active.new {
    transition: all 0s;
    background: #c8ebfb;
  }

  .fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
    opacity: 0;
  }

</style>
