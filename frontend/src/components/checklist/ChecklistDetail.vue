<template>
  <content-card
    v-if="checklist"
    :key="checklist._meta.self"
    class="ec-checklist"
    toolbar
    :back="checklistRoute(camp)"
  >
    <template #title>
      <v-toolbar-title v-if="!editChecklistName" tag="h1" class="font-weight-bold">
        {{ checklist.name }}
      </v-toolbar-title>
      <v-btn
        v-if="!editChecklistName && !isOutsider"
        icon
        class="ml-1 visible-on-hover"
        width="24"
        height="24"
        @click="makeChecklistNameEditable()"
      >
        <v-icon size="small">mdi-pencil</v-icon>
      </v-btn>
      <api-form v-if="editChecklistName" :entity="checklist" class="mx-2 flex-grow-1">
        <api-text-field
          path="name"
          density="compact"
          autofocus
          :auto-save="false"
          @finished="editChecklistName = false"
        />
      </api-form>
    </template>
    <template #title-actions>
      <ChecklistItemCreate v-if="isContributor" :checklist="checklist" />
      <!-- hamburger menu -->
      <v-menu v-if="isContributor" offset-y>
        <template #activator="{ props }">
          <v-btn icon v-bind="props">
            <v-icon>mdi-dots-vertical</v-icon>
          </v-btn>
        </template>
        <v-list>
          <!-- remove checklist -->
          <DialogEntityDelete
            :entity="checklist"
            :error-handler="deleteErrorHandler"
            :success-handler="deleteSuccessHandler"
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
            {{ $t('components.checklist.checklistDetail.deleteWarning') }}
          </DialogEntityDelete>
        </v-list>
      </v-menu>
    </template>
    <v-list>
      <SortableChecklist
        v-if="checklist && !checklist._meta.deleting"
        :parent="null"
        :checklist="checklist"
        :disabled="debouncedDisabled"
      />
    </v-list>
  </content-card>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import ChecklistItemCreate from '@/components/checklist/ChecklistItemCreate.vue'
import SortableChecklist from '@/components/checklist/SortableChecklist.vue'
import ApiForm from '@/components/form/api/ApiForm.vue'
import DialogEntityDelete from '@/components/dialog/DialogEntityDelete.vue'
import { checklistRoute } from '@/router.js'
import { campRoleMixin } from '@/mixins/campRoleMixin.js'
import { nextTick } from 'vue'

export default {
  name: 'ChecklistDetail',
  components: {
    SortableChecklist,
    ChecklistItemCreate,
    ContentCard,
    ApiForm,
    DialogEntityDelete,
  },
  mixins: [campRoleMixin],
  props: {
    camp: {
      type: Object,
      default: null,
      required: false,
    },
    checklist: {
      type: Object,
      default: null,
      required: false,
    },
  },
  data() {
    return {
      dragging: false,
      editChecklistName: false,
      debouncedDisabled: true,
    }
  },
  computed: {
    items() {
      return this.checklist.checklistItems().items.filter((item) => !item.parent)
    },
  },
  async mounted() {
    await this.api
      .get()
      .contentNodes({
        isRoot: 'true',
        camp: this.camp._meta.self,
      })
      .$loadItems()

    await nextTick()
    this.debouncedDisabled = this.isOutsider
  },
  methods: {
    checklistRoute,
    makeChecklistNameEditable() {
      this.editChecklistName = true
    },
    deleteErrorHandler(e) {
      if (e?.response?.status === 422 /* Validation Error */) {
        return this.$t('components.checklist.checklistDetail.deleteError')
      }
      return null
    },
    deleteSuccessHandler() {
      // redirect to Checklist overview
      this.$router.replace(checklistRoute(this.camp))
    },
  },
}
</script>
