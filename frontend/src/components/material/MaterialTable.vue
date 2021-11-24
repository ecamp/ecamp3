<template>
  <v-data-table
    :headers="tableHeaders"
    :items="materialItemsData"
    :disable-pagination="true"
    mobile-breakpoint="0"
    :group-by="groupByList ? 'listName' : null"
    item-class="class"
    hide-default-footer>
    <!-- skeleton loader (slot #body overrides all others) -->
    <template v-if="materialItemCollection._meta.loading || camp.materialLists()._meta.loading" #body="{ headers }">
      <tr v-for="row in 3" :key="row">
        <td v-for="col in headers.length" :key="col">
          <v-skeleton-loader
            height="25"
            class="pr-5 mt-1"
            type="text" />
        </td>
      </tr>
    </template>

    <template #[`group.header`]="{ groupBy, group, headers, isOpen, toggle }">
      <td :colspan="headers.length">
        <v-btn icon small @click="toggle">
          <v-icon v-if="isOpen">mdi-minus</v-icon>
          <v-icon v-else>mdi-plus</v-icon>
        </v-btn>
        {{ tableHeaders.find(header => header.value === groupBy[0] ).text }}:
        {{ group }}
        <!--
        <v-btn icon small @click="remove">
          <v-icon>mdi-close</v-icon>
        </v-btn> -->
      </td>
    </template>

    <template #[`item.quantity`]="{ item }">
      <api-text-field
        v-if="!item.readonly"
        :disabled="layoutMode || disabled"
        dense
        :uri="item.uri"
        fieldname="quantity"
        type="number" />
      <span v-if="item.readonly">{{ item.quantity }}</span>
    </template>

    <template #[`item.unit`]="{ item }">
      <api-text-field
        v-if="!item.readonly"
        :disabled="layoutMode || disabled"
        dense
        :uri="item.uri"
        fieldname="unit" />
      <span v-if="item.readonly">{{ item.unit }}</span>
    </template>

    <template #[`item.article`]="{ item }">
      <api-text-field
        v-if="!item.readonly"
        :disabled="layoutMode || disabled"
        dense
        :uri="item.uri"
        fieldname="article" />
      <span v-if="item.readonly">{{ item.article }}</span>
    </template>

    <template #[`item.listName`]="{ item }">
      <api-select
        v-if="!item.readonly"
        :disabled="layoutMode || disabled"
        dense
        :uri="item.uri"
        relation="materialList"
        fieldname="materialList"
        :items="materialLists" />
      <span v-if="item.readonly">{{ item.listName }}</span>
    </template>

    <template #[`item.lastColumn`]="{ item }">
      <!-- Activity link (only visible in full period view) -->
      <schedule-entry-links
        v-if="period && showActivityMaterial && item.entityObject && item.entityObject.materialNode"
        :activity="item.entityObject.materialNode().owner" />

      <!-- Action buttons -->
      <div v-if="!item.readonly" class="d-flex">
        <!-- edit dialog (mobile only) -->
        <dialog-material-item-edit
          v-if="!$vuetify.breakpoint.smAndUp && !layoutMode && !disabled"
          class="float-left"
          :material-item-uri="item.uri">
          <template #activator="{ on }">
            <button-edit small
                         fab
                         text
                         v-on="on" />
          </template>
        </dialog-material-item-edit>

        <!-- delete button (behind menu) -->
        <v-menu v-if="!layoutMode && !disabled">
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
      <div v-if="item.new">
        <v-progress-circular
          v-if="!item.serverError"
          size="16"
          indeterminate
          color="primary" />

        <div v-if="item.serverError">
          <v-tooltip top color="red darken-2">
            <template #activator="{ on, attrs }">
              <span
                v-bind="attrs"
                class="red--text text--darken-2"
                v-on="on">{{ $tc('global.serverError.short') }}</span>
            </template>
            <server-error-content :server-error="item.serverError" />
          </v-tooltip>

          <button-retry small class="my-1" @click="retry(item)" />
          <button-cancel small class="my-1" @click="cancel(item)" />
        </div>
      </div>
    </template>

    <template #[`body.append`]="{ headers }">
      <!-- add new item (desktop view) -->
      <material-create-item
        v-if="!layoutMode && $vuetify.breakpoint.smAndUp && !disabled"
        key="addItemRow"
        :camp="camp"
        :columns="headers.length"
        @item-adding="add" />
    </template>

    <template #footer>
      <!-- add new item (mobile view) -->
      <div v-if="!layoutMode && !$vuetify.breakpoint.smAndUp && !disabled" class="mt-5">
        <dialog-material-item-create
          :camp="camp"
          :material-item-collection="materialItemCollection"
          @item-adding="add">
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
import ServerErrorContent from '@/components/form/ServerErrorContent.vue'
import ButtonRetry from '@/components/buttons/ButtonRetry.vue'
import ButtonCancel from '@/components/buttons/ButtonCancel.vue'

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
    ButtonRetry,
    ButtonCancel,
    ScheduleEntryLinks,
    ServerErrorContent
  },
  props: {
    // camp Entity
    camp: { type: Object, required: true },

    disabled: { type: Boolean, default: false },

    // layoutMode=true --> data editing is disabled
    layoutMode: { type: Boolean, required: false, default: false },

    // materialItems Collection to display
    materialItemCollection: { type: Object, required: true },

    // materialNode Entity for displaying material tables within activitiy
    materialNode: { type: Object, required: false, default: null },

    // materialNode Entity for displaying material tables within activitiy
    period: { type: Object, required: false, default: null },

    // controls if material belonging to ContentNodes should be shown or not
    showActivityMaterial: { type: Boolean, default: true },

    // true --> displays table grouped by material list
    groupByList: { type: Boolean, default: false }
  },
  data () {
    return {
      newMaterialItems: {}
    }
  },
  computed: {
    tableHeaders () {
      const headers = [{
        text: this.$tc('entity.materialItem.fields.quantity'),
        value: 'quantity',
        align: 'start',
        sortable: false,
        groupable: false,
        width: '10%'
      },
      { text: this.$tc('entity.materialItem.fields.unit'), value: 'unit', groupable: false, sortable: false, width: '15%' },
      { text: this.$tc('entity.materialItem.fields.article'), value: 'article' }]

      headers.push({ text: this.$tc('entity.materialList.name'), value: 'listName', width: '20%' })

      // Activity column only shown in period overview
      if (this.period && this.showActivityMaterial) {
        headers.push({ text: this.$tc('entity.activity.name'), value: 'lastColumn', groupable: false, width: '15%' })
      } else {
        headers.push({ text: '', value: 'lastColumn', sortable: false, groupable: false, width: '5%' })
      }

      return headers
    },
    materialLists () {
      return this.camp.materialLists().items.map(l => ({
        value: l._meta.self,
        text: l.name
      }))
    },
    materialItemsData () {
      const items = this.materialItemCollection.items
        .filter(item => {
          // filter out material items belonging to content nodes (if showActivityMaterial is deactivated)
          if (!this.showActivityMaterial && item.materialNode !== null) {
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
          listName: item.materialList().name,
          activity: item.materialNode ? item.materialNode().id : null,
          entityObject: item,
          readonly: (this.period && item.materialNode), // if complete component is in period overview, disable editing of material that belongs to materialNodes (Acitity material)
          class: this.period && item.materialNode ? 'readonly' : 'period'
        }))

      // eager add new Items
      for (const key in this.newMaterialItems) {
        const mi = this.newMaterialItems[key]
        items.push({
          id: key,
          quantity: mi.quantity,
          unit: mi.unit,
          article: mi.article,
          listName: this.materialLists.find(listItem => listItem.value === mi.materialList).text,
          new: true,
          serverError: mi.serverError,
          readonly: true,
          class: 'new' // CSS class of new item rows
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
    // remove existing item
    deleteMaterialItem (materialItem) {
      this.api.del(materialItem.uri)
    },

    // add new item to list & save to API
    add (key, data) {
      // add item to local array
      this.$set(this.newMaterialItems, key, data)

      this.postToApi(key, data)
    },

    // retry to save to API (after server error)
    retry (item) {
      // reset error
      this.$set(this.newMaterialItems[item.id], 'serverError', null)

      // try to save same data again
      this.postToApi(item.id, this.newMaterialItems[item.id])
    },

    // cancel (remove) item that is not successfully stored to API
    cancel (item) {
      this.$delete(this.newMaterialItems, item.id)
    },

    postToApi (key, data) {
      // post new item to the API collection
      this.materialItemCollection.$post(data)
        .then(mi => {
          // reload list after item has successfully been added
          this.api.reload(this.materialItemCollection).then(() => {
            this.$delete(this.newMaterialItems, key)
          })
        })
      // catch server error
        .catch(error => {
          this.$set(this.newMaterialItems[key], 'serverError', error)
        })
    }
  }
}
</script>

<style scoped>

  .v-data-table >>> .v-data-table__wrapper th {
    padding: 0 2px;
  }
  .v-data-table >>> .v-data-table__wrapper td {
    padding: 0 2px;
  }

  .v-data-table >>> .v-data-table__wrapper tr.readonly td,
  .v-data-table >>> .v-data-table__wrapper tr.new td {
    padding-left: 10px;
  }

  .v-data-table >>> tr.new {
    animation: backgroundHighlight 2s;
  }

  @keyframes backgroundHighlight {
    from {background: #c8ebfb;}
    to {}
  }

</style>
