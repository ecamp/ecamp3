<template>
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
              v-if="!item.readonly"
              :disabled="layoutMode"
              dense
              :uri="item.uri"
              fieldname="quantity" />
            <span v-if="item.readonly">{{ item.quantity }}</span>
          </td>
          <td>
            <api-text-field
              v-if="!item.readonly"
              :disabled="layoutMode"
              dense
              :uri="item.uri"
              fieldname="unit" />
            <span v-if="item.readonly">{{ item.unit }}</span>
          </td>
          <td>
            <api-text-field
              v-if="!item.readonly"
              :disabled="layoutMode"
              dense
              :uri="item.uri"
              fieldname="article" />
            <span v-if="item.readonly">{{ item.article }}</span>
          </td>

          <!-- Material list (hidden in mobile view) -->
          <td v-if="$vuetify.breakpoint.smAndUp">
            <api-select
              v-if="!item.readonly"
              :disabled="layoutMode"
              dense
              :uri="item.uri"
              relation="materialList"
              fieldname="materialListId"
              :items="materialLists" />
            <span v-if="item.readonly">{{ item.listName }}</span>
          </td>

          <!-- Activity link (only visible in full period view) -->
          <td v-if="period">
            <schedule-entry-links
              v-if="item.entityObject && item.entityObject.contentNode"
              :activity="item.entityObject.contentNode().owner" />
          </td>

          <!-- Action buttons -->
          <td>
            <div v-if="!item.readonly" class="d-flex">
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
          :material-item-collection="materialItemCollection"
          @item-adding="onItemAdding" />
      </tbody>
    </template>

    <template #footer>
      <!-- add new item (mobile view) -->
      <div v-if="!layoutMode && !$vuetify.breakpoint.smAndUp" class="mt-5">
        <dialog-material-item-create
          :camp="camp"
          :material-item-collection="materialItemCollection">
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
</template>

<script>
import ApiTextField from '@/components/form/api/ApiTextField.vue'
import ApiSelect from '@/components/form/api/ApiSelect.vue'
import ButtonEdit from '@/components/buttons/ButtonEdit.vue'
import ButtonAdd from '@/components/buttons/ButtonAdd.vue'
import DialogMaterialItemCreate from './DialogMaterialItemCreate.vue'
import DialogMaterialItemEdit from './DialogMaterialItemEdit.vue'
import MaterialCreateItem from './MaterialCreateItem.vue'
import ScheduleEntryLinks from './ScheduleEntryLinks.vue'

export default {
  name: 'MaterialTable',
  components: {
    ApiTextField,
    ApiSelect,
    DialogMaterialItemCreate,
    DialogMaterialItemEdit,
    MaterialCreateItem,
    ButtonEdit,
    ButtonAdd,
    ScheduleEntryLinks
  },
  props: {
    // camp Entity
    camp: { type: Object, required: true },

    // layoutMode=true --> data editing is disabled
    layoutMode: { type: Boolean, required: false, default: false },

    // materialItems Collection to display
    materialItemCollection: { type: Object, required: true },

    // contentNode Entity for displaying material tables within activitiy
    contentNode: { type: Object, required: false, default: null },

    // contentNode Entity for displaying material tables within activitiy
    period: { type: Object, required: false, default: null },

    // controls if material belonging to ContentNodes should be shown or not
    showContentNodeMaterial: { type: Boolean, default: true }
  },
  data () {
    return {
      newMaterialItems: {}
    }
  },
  computed: {
    headers () {
      const headers = [{
        text: this.$tc('entity.materialItem.fields.quantity'),
        align: 'start',
        sortable: false,
        value: 'quantity',
        width: '10%'
      },
      { text: this.$tc('entity.materialItem.fields.unit'), value: 'unit', sortable: false, width: '10%' },
      { text: this.$tc('entity.materialItem.fields.article'), value: 'article' }]

      // disable material list in mobile view
      if (this.$vuetify.breakpoint.smAndUp) {
        headers.push({ text: this.$tc('entity.materialList.name'), value: 'listName', width: '20%' })
      }

      // Activity column only shown in period overview
      if (this.period) {
        headers.push({ text: this.$tc('entity.activity.name'), value: 'activity', width: '15%' })
      }

      headers.push({ text: '', value: 'actions', sortable: false, width: '10%' })

      return headers
    },
    materialLists () {
      return this.camp.materialLists().items.map(l => ({
        value: l.id,
        text: l.name
      }))
    },
    materialItemsData () {
      const items = this.materialItemCollection.items
        .filter(item => {
          // filter out material items belonging to content nodes (if showContentNodeMaterial is deactivated)
          if (!this.showContentNodeMaterial && item.contentNode !== null) {
            return false
          }

          return true
        })

        .map(item => ({
          id: item.id,
          uri: item._meta.self,
          quantity: item.quantity,
          unit: item.unit,
          article: item.article,
          listId: item.materialList().id,
          listName: item.materialList().name,
          activity: item.contentNode ? item.contentNode().id : null,
          entityObject: item,
          readonly: this.period && item.contentNode // if complete component is in period overview, disable editing of material that belongs to contentNodes (Acitity material)
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
          new: true,
          readonly: true
        })
      }

      return items
    },
    materialItemsSorted () {
      const items = this.materialItemCollection.items

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
    deleteMaterialItem (materialItem) {
      this.api.del(materialItem.uri)
    },
    onItemAdding (key, data, res) {
      this.$set(this.newMaterialItems, key, data)

      res.then(mi => {
        this.api.reload(this.materialItemCollection).then(() => {
          this.$delete(this.newMaterialItems, key)
        })
      })
    }
  }
}
</script>

<style scoped>

  .v-data-table >>> .v-data-table__wrapper > table > thead > tr > th {
    padding: 0 2px;
  }
  .v-data-table >>> .v-data-table__wrapper > table > tbody > tr > td {
    padding: 0 2px;
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
