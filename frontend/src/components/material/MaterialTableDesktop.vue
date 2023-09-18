<template>
  <v-data-table
    :headers="tableHeaders"
    :items="materialItemsData"
    :disable-pagination="true"
    mobile-breakpoint="0"
    :group-by="groupByList ? 'listName' : null"
    item-class="class"
    hide-default-footer
  >
    <!-- skeleton loader (slot #body overrides all others) -->
    <template
      v-if="materialItemCollection._meta.loading || camp.materialLists()._meta.loading"
      #body="{ headers }"
    >
      <tr v-for="row in 3" :key="row">
        <td v-for="col in headers.length" :key="col">
          <v-skeleton-loader height="25" class="pr-5 mt-1" type="text" />
        </td>
      </tr>
    </template>

    <template #[`group.header`]="{ groupBy, group, headers, isOpen, toggle }">
      <td :colspan="headers.length">
        <v-btn icon small @click="toggle">
          <v-icon v-if="isOpen">mdi-minus</v-icon>
          <v-icon v-else>mdi-plus</v-icon>
        </v-btn>
        {{ tableHeaders.find((header) => header.value === groupBy[0]).text }}:
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
        :outlined="false"
        :uri="item.uri"
        fieldname="quantity"
        inputmode="decimal"
        class="ec-material-table__input"
        vee-rules="numeric"
      />
      <span v-else class="ec-material-table__readonly">{{ item.quantity }}</span>
    </template>

    <template #[`item.unit`]="{ item }">
      <api-text-field
        v-if="!item.readonly"
        :disabled="layoutMode || disabled"
        dense
        :outlined="false"
        :uri="item.uri"
        fieldname="unit"
        maxlength="32"
        class="ec-material-table__input"
      />
      <span v-else class="ec-material-table__readonly">{{ item.unit }}</span>
    </template>

    <template #[`item.article`]="{ item }">
      <api-text-field
        v-if="!item.readonly"
        :disabled="layoutMode || disabled"
        dense
        :outlined="false"
        :uri="item.uri"
        fieldname="article"
        maxlength="64"
        class="ec-material-table__input"
      />
      <span v-else class="ec-material-table__readonly">{{ item.article }}</span>
    </template>

    <template #[`item.listName`]="{ item }">
      <api-select
        v-if="!item.readonly"
        :disabled="layoutMode || disabled"
        dense
        :outlined="false"
        :uri="item.uri"
        fieldname="materialList"
        :items="materialLists"
        class="ec-material-table__input"
        @saved="reloadItems"
      />
      <Truncate v-else class="ec-material-table__readonly" style="max-width: 15vw"
        >{{ item.listName }}
      </Truncate>
    </template>

    <template #[`item.reference`]="{ item }">
      <div class="d-flex justify-between align-center">
        <!-- Activity link (only visible in full period view) -->
        <schedule-entry-links
          v-if="item.materialNode"
          :activity-promise="findOneActivityByContentNode(item.materialNode())"
        />
        <Truncate v-if="item.period" style="flex: 1 1 0; width: min-content">
          {{ item.period().description }}
        </Truncate>
      </div>
    </template>

    <template #[`item.lastColumn`]="{ item }">
      <!-- Action buttons -->
      <div v-if="!item.new">
        <!-- delete button (behind menu) -->
        <PromptEntityDelete
          v-if="$vuetify.breakpoint.smAndUp"
          :entity="item.entityObject"
          :warning-text-entity="item.singleline"
          :btn-attrs="{
            disabled: layoutMode || disabled,
            bg: true,
            width: $vuetify.breakpoint.mdAndUp ? '100%' : null,
            iconOnly: !$vuetify.breakpoint.mdAndUp,
          }"
          y="bottom"
          x="left"
        />
      </div>

      <!-- loading spinner for newly added items -->
      <div v-if="item.new">
        <v-progress-circular
          v-if="!item.serverError"
          size="16"
          indeterminate
          color="primary"
        />

        <div v-if="item.serverError">
          <v-tooltip top color="red darken-2">
            <template #activator="{ on, attrs }">
              <span v-bind="attrs" class="red--text text--darken-2" v-on="on">{{
                $tc('global.serverError.short')
              }}</span>
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
        :material-node="materialNode"
        :period="period"
        :columns="headers.length"
        :material-list="materialList?._meta.self"
        @item-adding="add"
      />
    </template>

    <template #footer>
      <!-- add new item (mobile view) -->
      <div v-if="!layoutMode && !$vuetify.breakpoint.smAndUp && !disabled" class="mt-5">
        <dialog-material-item-create
          :camp="camp"
          :material-item-collection="materialItemCollection"
          :material-list="materialList"
          @item-adding="add"
        >
          <template #activator="{ on }">
            <button-add v-on="on">
              {{ $tc('components.material.materialTableDesktop.addNewItem') }}
            </button-add>
          </template>
        </dialog-material-item-create>
      </div>
    </template>

    <template #no-data>
      <p>No material items found</p>
    </template>
  </v-data-table>
</template>

<script>
import ApiTextField from '@/components/form/api/ApiTextField.vue'
import ApiSelect from '@/components/form/api/ApiSelect.vue'
import ButtonAdd from '@/components/buttons/ButtonAdd.vue'
import DialogMaterialItemCreate from './DialogMaterialItemCreate.vue'
import MaterialCreateItem from './MaterialCreateItem.vue'
import ScheduleEntryLinks from './ScheduleEntryLinks.vue'
import ServerErrorContent from '@/components/form/ServerErrorContent.vue'
import ButtonRetry from '@/components/buttons/ButtonRetry.vue'
import ButtonCancel from '@/components/buttons/ButtonCancel.vue'
import { errorToMultiLineToast } from '@/components/toast/toasts'
import * as Sentry from '@sentry/browser'
import { serverErrorToString } from '@/helpers/serverError.js'
import PromptEntityDelete from '@/components/prompt/PromptEntityDelete.vue'
import Truncate from '@/components/generic/Truncate.vue'

export default {
  name: 'MaterialTableDesktop',
  components: {
    Truncate,
    PromptEntityDelete,
    ApiTextField,
    ApiSelect,
    DialogMaterialItemCreate,
    MaterialCreateItem,
    ButtonAdd,
    ButtonRetry,
    ButtonCancel,
    ScheduleEntryLinks,
    ServerErrorContent,
  },
  props: {
    // camp Entity
    camp: { type: Object, required: true },

    disabled: { type: Boolean, default: false },

    // layoutMode=true --> data editing is disabled
    layoutMode: { type: Boolean, required: false, default: false },

    // materialItems Collection to display
    materialItemCollection: { type: Object, required: true },

    // materialNode Entity for displaying material item within an activity (should be null if period is provided)
    materialNode: { type: Object, required: false, default: null },

    materialList: { type: Object, required: false, default: null },

    // period Entity for displaying material items within a period (should be null if materialNode is provided)
    period: { type: Object, required: false, default: null },

    // controls if material belonging to ContentNodes should be shown or not
    showActivityMaterial: { type: Boolean, default: true },

    // true --> displays table grouped by material list
    groupByList: { type: Boolean, default: false },

    // true --> displays table grouped by material list
    readonly: { type: Boolean, default: false },
  },
  data() {
    return {
      newMaterialItems: {},
    }
  },
  computed: {
    tableHeaders() {
      const headers = [
        {
          text: this.$tc('entity.materialItem.fields.quantity'),
          value: 'quantity',
          align: 'end',
          sortable: false,
          groupable: false,
          width: 'auto',
        },
        {
          text: this.$tc('entity.materialItem.fields.unit'),
          value: 'unit',
          groupable: false,
          sortable: false,
          width: 'auto',
        },
        {
          text: this.$tc('entity.materialItem.fields.article'),
          value: 'article',
          width: this.period ? '40%' : '30%',
        },
        {
          text: this.$tc('entity.materialItem.fields.list'),
          value: 'listName',
          width: '20%',
        },
        ...(this.materialNode
          ? []
          : [
              {
                text: this.$tc('entity.materialItem.fields.reference'),
                value: 'reference',
                groupable: false,
                width: 'auto',
                cellClass: 'vertical-middle',
              },
            ]),
        {
          text: '',
          value: 'lastColumn',
          align: 'center',
          sortable: false,
          groupable: false,
          width: 'auto',
        },
      ]

      return headers
    },
    materialLists() {
      return this.camp.materialLists().items.map((l) => ({
        value: l._meta.self,
        text: l.name,
      }))
    },
    materialItemsData() {
      const items = this.materialItemCollection.items
        .filter((item) => {
          // filter out material items belonging to content nodes (if showActivityMaterial is deactivated)
          if (!this.showActivityMaterial && item.materialNode !== null) {
            return false
          }

          return true
        })

        .map((item) => ({
          id: item.id,
          uri: item._meta.self,
          quantity: item.quantity,
          unit: item.unit,
          article: item.article,
          listName: item.materialList().name,
          entityObject: item,
          class: this.period && item.materialNode ? 'readonly' : 'period',
          period: item.period,
          materialNode: item.materialNode,
          singleline: [
            ...(item.quantity || item.unit
              ? [item.quantity, !item.unit && item.quantity ? '×' : item.unit]
              : []),
            item.article,
          ].join(' '),
        }))

      // eager add new Items
      for (const key in this.newMaterialItems) {
        const mi = this.newMaterialItems[key]
        items.push({
          id: key,
          quantity: mi.quantity,
          unit: mi.unit,
          article: mi.article,
          listName: this.materialLists.find(
            (listItem) => listItem.value === mi.materialList
          ).text,
          new: true,
          serverError: mi.serverError,
          readonly: true,
          class: 'new', // CSS class of new item rows
        })
      }

      return items
    },
    materialItemsSorted() {
      const items = this.materialItemCollection.items

      // eager add new Items
      for (const key in this.newMaterialItems) {
        const mi = this.newMaterialItems[key]
        items.push({
          id: key,
          quantity: mi.quantity,
          unit: mi.unit,
          article: mi.article,
        })
      }

      return items.sort((a, b) => a.article.localeCompare(b.article))
    },
  },
  async mounted() {
    if (!this.materialNode && !this.period) {
      await this.camp.activities().$loadItems()
    }
  },
  methods: {
    // remove existing item
    deleteMaterialItem(materialItem) {
      this.api
        .del(materialItem.uri)
        .catch((e) => this.$toast.error(errorToMultiLineToast(e)))
    },

    // add new item to list & save to API
    add(key, data) {
      // add item to local array
      this.$set(this.newMaterialItems, key, data)

      this.postToApi(key, data)
    },

    // retry to save to API (after server error)
    retry(item) {
      // reset error
      this.$set(this.newMaterialItems[item.id], 'serverError', undefined)

      // try to save same data again
      this.postToApi(item.id, this.newMaterialItems[item.id])
    },

    // cancel (remove) item that is not successfully stored to API
    cancel(item) {
      this.$delete(this.newMaterialItems, item.id)
    },

    postToApi(key, data) {
      // post new item to the API collection
      this.materialItemCollection
        .$post(data)
        .then(() => {
          // reload list after item has successfully been added
          this.api.reload(this.materialItemCollection).then(() => {
            this.$delete(this.newMaterialItems, key)
          })
        })
        // catch server error
        .catch((error) => {
          this.$set(this.newMaterialItems[key], 'serverError', error)
          this.$toast.error(errorToMultiLineToast(error))
          Sentry.captureMessage(serverErrorToString(error))
        })
    },

    reloadItems() {
      if (this.materialList) {
        this.api.reload(this.materialItemCollection)
      }
    },

    async findOneActivityByContentNode(contentNode) {
      await this.camp.activities().$loadItems()
      const root = await contentNode.$href('root')

      return this.camp.activities().items.find((activity) => {
        return activity.rootContentNode()._meta.self === root
      })
    },
  },
}
</script>

<style scoped lang="scss">
.v-data-table:deep(.v-data-table__wrapper th) {
  padding: 0 2px;
}

.v-data-table:deep(.v-data-table__wrapper td) {
  padding: 0 2px;
}

.v-data-table:deep(.v-data-table__wrapper tr.new td) {
  padding-left: 10px;
}

.v-data-table:deep(tr.new) {
  animation: backgroundHighlight 2s;
}

::v-deep .vertical-middle {
  vertical-align: middle !important;
}

.v-data-table::v-deep
  > .v-data-table__wrapper
  > table
  > :is(tbody, thead, tfoot)
  > tr
  > td:not(:last-child) {
  min-height: 48px;
  height: auto;
}

.v-data-table::v-deep
  > .v-data-table__wrapper
  > table
  > :is(tbody, thead, tfoot)
  > tr:not(.readonly, .newItemRow)
  > td:not(:last-child) {
  vertical-align: bottom;
}

.v-data-table ::v-deep tr:not(:hover) .ec-btn-delete {
  opacity: 0.5;
}

::v-deep td.text-end .v-text-field input {
  text-align: end;
}

::v-deep .ec-material-table__input .v-select.v-input--dense .v-select__selection--comma {
  margin-top: 8px;
  margin-bottom: 12px;
}

::v-deep td.text-end .v-text-field input::-webkit-textfield-decoration-container {
  flex-direction: row-reverse;
}

::v-deep .ec-material-table__readonly {
  font-size: 16px;
  padding: 12px 0;
  display: block;
}

::v-deep .ec-material-table__input {
  height: 100%;

  .v-select .v-input__append-inner {
    align-self: center;
  }

  .e-form-container,
  .v-input {
    height: 100%;
    font-size: 0.875rem;
  }

  .v-input {
    align-items: stretch;

    input {
      padding: 8px 0 12px;
      box-sizing: content-box;
    }
  }

  .v-text-field__details {
    margin-bottom: -8px;
  }

  .v-input__slot {
    margin-bottom: 0;
  }

  .v-input__control {
    flex-direction: column-reverse;
  }
}

@keyframes backgroundHighlight {
  from {
    background: #c8ebfb;
  }
  to {
  }
}
</style>
