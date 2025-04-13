<template>
  <v-data-table
    v-resizeobserver.debounce="onResize"
    :headers="tableHeaders"
    :items="materialItemsData"
    :disable-pagination="true"
    :disable-filtering="layoutMode"
    :disable-sort="layoutMode"
    mobile-breakpoint="0"
    item-class="rowClass"
    class="transparent"
    :class="{
      'ec-material-table--dense': !isDefaultVariant,
      'ec-material-table--default': isDefaultVariant,
    }"
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

    <template #[`item.quantity`]="{ item }">
      <api-number-field
        v-if="!item.readonly"
        :disabled="layoutMode || disabled"
        dense
        :uri="item.uri"
        vee-rules="greaterThan:0"
        path="quantity"
        inputmode="decimal"
      />
      <span v-if="item.readonly">{{ item.quantity }}</span>
    </template>

    <template #[`item.unit`]="{ item }">
      <api-text-field
        v-if="!item.readonly"
        :disabled="layoutMode || disabled"
        dense
        :uri="item.uri"
        path="unit"
        maxlength="32"
      />
      <span v-if="item.readonly">{{ item.unit }}</span>
    </template>

    <template #[`item.article`]="{ item }">
      <template v-if="isDefaultVariant">
        <api-text-field
          v-if="!item.readonly"
          :disabled="layoutMode || disabled"
          dense
          :uri="item.uri"
          path="article"
          maxlength="64"
        />
        <span v-if="item.readonly">{{ item.article }}</span>
      </template>
      <template v-else>
        <div class="d-flex">
          <span v-if="item.combinedQuantity">{{ item.combinedQuantity }}&nbsp;</span
          ><strong>{{ item.article }}</strong>
        </div>
        <small v-if="!materialList" class="opacity-60">{{ item.listName }}</small>
      </template>
    </template>

    <template #[`item.listName`]="{ item }">
      <api-select
        v-if="!item.readonly"
        :disabled="layoutMode || disabled"
        dense
        :uri="item.uri"
        path="materialList"
        :items="materialLists"
      />
      <span v-if="item.readonly">{{ item.listName }}</span>
    </template>

    <template #[`item.lastColumn`]="{ item }">
      <!-- Activity link (only visible in full period view) -->
      <ScheduleEntryLinks
        v-if="period && item.entityObject && item.entityObject.materialNode"
        :activity-promise="findOneActivityByContentNode(item.entityObject.materialNode())"
      />

      <!-- Action buttons -->
      <template v-if="!item.readonly && !layoutMode && !disabled">
        <PromptEntityDelete
          v-if="isDefaultVariant"
          :entity="item.uri"
          :warning-text-entity="item.article"
          :btn-attrs="{
            class: 'v-btn--has-bg',
            disabled: layoutMode || disabled,
            bg: true,
            width: !isDefaultVariant ? '100%' : null,
            iconOnly: true,
            btnIcon: false,
          }"
        />
        <DialogMaterialItemEdit v-else :material-item-uri="item.uri">
          <template #activator="{ on }">
            <ButtonEdit class="v-btn--has-bg" small text v-on="on" />
          </template>
        </DialogMaterialItemEdit>
      </template>

      <!-- loading spinner for newly added items -->
      <div v-if="item.new">
        <v-progress-circular
          v-if="!item.serverError"
          size="16"
          indeterminate
          color="primary"
        />

        <div v-if="item.serverError" class="d-flex gap-2 align-center">
          <v-tooltip top color="red darken-2">
            <template #activator="{ on, attrs }">
              <span v-bind="attrs" class="red--text text--darken-2" v-on="on">{{
                $tc('global.serverError.short')
              }}</span>
            </template>
            <ServerErrorContent :server-error="item.serverError" />
          </v-tooltip>

          <ButtonRetry @click="retry(item)" />
          <ButtonCancel class="v-btn--has-bg" @click="cancel(item)">
            {{ $tc('global.button.discard') }}
          </ButtonCancel>
        </div>
      </div>
    </template>

    <template #[`header.lastColumn`]>
      <button
        v-if="!hidePeriodFilter"
        class="ec-material-table__filterbutton"
        :class="{ 'primary--text': periodOnly }"
        :disabled="layoutMode || !periodFilterEnabled"
        @click="periodOnly = !periodOnly"
      >
        <span>
          <span v-if="!periodOnly" class="d-sr-only">{{
            $tc('global.button.filter')
          }}</span>
          {{
            periodOnly
              ? $tc('components.material.materialTable.periodOnly')
              : $tc('components.material.materialTable.reference')
          }}
        </span>
        <v-icon
          v-if="periodFilterEnabled"
          aria-hidden="true"
          small
          :color="periodOnly ? 'primary' : null"
        >
          {{ periodOnly ? 'mdi-filter' : 'mdi-filter-outline' }}
        </v-icon>
      </button>
    </template>

    <template #[`body.append`]="{ headers }">
      <!-- add new item (desktop view) -->
      <MaterialCreateItem
        v-if="!layoutMode && isDefaultVariant && !disabled"
        key="addItemRow"
        :camp="camp"
        :columns="headers.length"
        :material-list="materialList"
        @item-adding="add"
      />
    </template>

    <template #footer>
      <!-- add new item (mobile view) -->
      <DialogMaterialItemCreate
        v-if="!layoutMode && !isDefaultVariant && !disabled"
        :camp="camp"
        :material-item-collection="materialItemCollection"
        :material-list="materialList"
        @item-adding="add"
      >
        <template #activator="{ on }">
          <ButtonAdd class="mt-5" v-on="on">
            {{ $tc('components.material.materialTable.addNewItem') }}
          </ButtonAdd>
        </template>
      </DialogMaterialItemCreate>
    </template>

    <template #no-data>{{ $tc('components.material.materialTable.noItems') }}</template>
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
import { errorToMultiLineToast } from '@/components/toast/toasts'
import * as Sentry from '@sentry/browser'
import { serverErrorToString } from '@/helpers/serverError.js'
import PromptEntityDelete from '@/components/prompt/PromptEntityDelete.vue'
import ApiNumberField from '@/components/form/api/ApiNumberField.vue'

// Non-breaking space
const nbsp = '\u00A0'

export default {
  name: 'MaterialTable',
  components: {
    PromptEntityDelete,
    ApiTextField,
    ApiNumberField,
    ApiSelect,
    DialogMaterialItemCreate,
    DialogMaterialItemEdit,
    MaterialCreateItem,
    ButtonEdit,
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

    // materialList if its only one
    materialList: { type: Object, required: false, default: null },

    // period Entity for displaying material items within a period (should be null if materialNode is provided)
    period: { type: Object, required: false, default: null },

    // Hide the filter button activity / period
    hidePeriodFilter: { type: Boolean, required: false, default: false },
  },
  data() {
    return {
      newMaterialItems: {},
      periodOnly: false,
      clientWidth: 1000,
    }
  },
  computed: {
    tableHeaders() {
      const headers = []

      if (this.isDefaultVariant) {
        headers.push(
          {
            text: this.$tc('entity.materialItem.fields.quantity'),
            value: 'quantity',
            align: 'end',
            sortable: false,
            width: '10%',
          },
          {
            text: this.$tc('entity.materialItem.fields.unit'),
            value: 'unit',
            sortable: false,
            width: '15%',
          },
          {
            text: this.$tc('entity.materialItem.fields.article'),
            value: 'article',
            cellClass: 'font-weight-bold',
          },
          {
            text: this.$tc('entity.materialList.name'),
            value: 'listName',
            width: '20%',
          }
        )
      } else {
        headers.push({
          text: this.$tc('entity.materialItem.fields.article'),
          value: 'article',
          align: 'start',
          sortable: true,
          width: 'auto',
          cellClass: 'pl-0',
        })
      }

      // Activity column only shown in period overview
      if (this.period) {
        headers.push({
          text: this.$tc('entity.materialItem.fields.reference'),
          align: this.isDefaultVariant ? 'start' : 'end',
          width: this.isDefaultVariant ? '15%' : 'auto',
          value: 'lastColumn',
          sortable: false,
        })
      } else {
        headers.push({
          text: '',
          width: '0',
          value: 'lastColumn',
          sortable: false,
        })
      }

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
        .filter((item) => !(this.periodOnly && item.materialNode !== null))
        .map((item) => ({
          id: item.id,
          uri: item._meta.self,
          quantity: item.quantity,
          unit: item.unit,
          combinedQuantity: this.renderQuantity(item),
          article: item.article,
          listName: item.materialList().name,
          entityObject: item,
          readonly: this.disabled || (this.period && item.materialNode), // if complete component is in period overview, disable editing of material that belongs to materialNodes (Activity material)
          rowClass: 'readonly',
        }))

      // eager add new Items
      for (const key in this.newMaterialItems) {
        const mi = this.newMaterialItems[key]
        items.push({
          id: key,
          quantity: mi.quantity,
          unit: mi.unit,
          combinedQuantity: this.renderQuantity(mi),
          article: mi.article,
          listName: this.materialLists.find(
            (listItem) => listItem.value === mi.materialList
          ).text,
          new: true,
          serverError: mi.serverError,
          readonly: true,
          rowClass: 'new', // CSS class of new item rows
        })
      }

      return items
    },
    isDefaultVariant() {
      return this.clientWidth > 710
    },
    // Show filter just if period material is in the list
    periodFilterEnabled() {
      return this.materialItemCollection.items.some((item) => item.materialNode === null)
    },
  },
  watch: {
    periodFilterEnabled() {
      if (!this.periodFilterEnabled) {
        this.periodOnly = false
      }
    },
  },
  mounted() {
    this.clientWidth = this.$el.clientWidth
  },
  methods: {
    onResize({ width }) {
      this.clientWidth = width
    },
    renderQuantity(item) {
      if (item.quantity && item.unit) {
        return `${item.quantity}${nbsp}${item.unit}`
      } else if (item.quantity) {
        return `${item.quantity}${nbsp}×`
      } else if (item.unit) {
        return item.unit
      } else {
        return ''
      }
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
            this.api.reload(this.newMaterialItems[key].materialList)
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
.v-data-table:deep(.v-data-table__wrapper th),
.v-data-table:deep(.v-data-table__wrapper td) {
  padding: 0 2px;
}

.ec-material-table--dense.v-data-table::v-deep {
  .v-data-table__wrapper th,
  .v-data-table__wrapper td {
    padding: 4px 2px;
    line-height: normal;
  }
}

.v-data-table:deep(tr.new) {
  animation: backgroundHighlight 2s;
}

@keyframes backgroundHighlight {
  from {
    background: #c8ebfb;
  }
  to {
  }
}

.ec-material-table--default {
  .ec-material-table__filterbutton {
    width: fit-content;
  }
}

.ec-material-table__filterbutton {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  text-align: left;
  width: min-content;

  span {
    flex: 1 1 0;
  }
}
</style>
