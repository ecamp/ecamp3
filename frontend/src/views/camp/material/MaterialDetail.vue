<template>
  <v-container fluid>
    <content-card :title="materialList.name" toolbar back>
      <template #title>
        <v-toolbar-title
          v-if="!editMaterialListName"
          tag="h1"
          class="font-weight-bold ml-0"
        >
          {{ materialList.name }}

          <v-btn
            v-if="!editMaterialListName && !isOutsider"
            icon
            class="ml-1 visible-on-hover"
            width="24"
            height="24"
            @click="makeMaterialListNameEditable()"
          >
            <v-icon size="x-small">mdi-pencil</v-icon>
          </v-btn>
        </v-toolbar-title>
        <api-form v-if="editMaterialListName" :entity="materialList" class="flex-grow-1">
          <api-text-field
            path="name"
            density="compact"
            autofocus
            :auto-save="false"
            @finished="editMaterialListName = false"
            @keydown.esc="editMaterialListName = false"
          />
        </api-form>
      </template>

      <template #title-actions>
        <v-menu offset-y>
          <template #activator="{ props }">
            <v-btn icon v-bind="props">
              <v-icon>mdi-dots-vertical</v-icon>
            </v-btn>
          </template>
          <v-list class="py-0">
            <v-list-item :disabled="isDownloadingXlsx" @click.stop="downloadXlsx">
              <template #prepend>
                <v-progress-circular
                  v-if="isDownloadingXlsx"
                  class="mr-2"
                  indeterminate
                  size="24"
                  width="2"
                />
                <v-icon v-else>mdi-microsoft-excel</v-icon>
              </template>
              {{ $t('global.button.download') }}
            </v-list-item>
            <DialogEntityDelete
              :entity="materialList"
              :warning-text-entity="materialList.name"
              :error-handler="deleteErrorHandler"
              :success-handler="
                () =>
                  $router.push({
                    path: `/camps/${camp.id}/${camp.shortTitle}/material/all`,
                  })
              "
            >
              <template #activator="{ props }">
                <v-list-item v-bind="props">
                  <template #prepend>
                    <v-icon>mdi-delete</v-icon>
                  </template>
                  <v-list-item-title>
                    {{ $t('global.button.delete') }}
                  </v-list-item-title>
                </v-list-item>
              </template>
            </DialogEntityDelete>
          </v-list>
        </v-menu>
      </template>
      <v-expansion-panels
        v-if="collection.length > 1"
        v-model="openPeriods"
        multiple
        flat
        variant="accordion"
      >
        <PeriodMaterialLists
          v-for="{ period, materialItems } in collection"
          :key="period._meta.self"
          :period="period"
          :material-item-collection="materialItems"
          :material-list="materialList"
          :disabled="!isContributor"
        />
      </v-expansion-panels>
      <v-card-text v-else-if="collection.length === 1">
        <MaterialTable
          v-for="{ period, materialItems } in collection"
          :key="period._meta.self"
          :camp="camp"
          :material-item-collection="materialItems"
          :period="period"
          :material-list="materialList"
          :disabled="!isContributor"
        />
      </v-card-text>
    </content-card>
  </v-container>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import PeriodMaterialLists from '@/components/material/PeriodMaterialLists.vue'
import MaterialTable from '@/components/material/MaterialTable.vue'
import DialogEntityDelete from '@/components/dialog/DialogEntityDelete.vue'
import { campRoleMixin } from '@/mixins/campRoleMixin.js'
import { useMaterialViewHelper } from '@/components/material/useMaterialViewHelper.js'

export default {
  name: 'MaterialDetail',
  components: {
    ContentCard,
    DialogEntityDelete,
    MaterialTable,
    PeriodMaterialLists,
  },
  mixins: [campRoleMixin],
  props: {
    camp: { type: Object, required: true },
    materialList: { type: Object, required: true },
  },
  setup(props) {
    return useMaterialViewHelper(props.camp, true)
  },
  data() {
    return {
      dragging: false,
      editMaterialListName: false,
      debouncedDisabled: true,
    }
  },
  head() {
    return {
      title: () => this.materialList.name,
    }
  },
  methods: {
    makeMaterialListNameEditable() {
      this.editMaterialListName = true
    },
    deleteErrorHandler(e) {
      if (e?.response?.status === 422 /* Validation Error */) {
        return this.$t('components.campAdmin.dialogMaterialListEdit.deleteError')
      }
      return null
    },
  },
}
</script>
