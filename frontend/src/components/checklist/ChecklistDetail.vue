<template>
  <content-card
    v-if="checklist"
    :key="checklist._meta.self"
    class="ec-checklist"
    toolbar
    back
  >
    <template #title>
      <v-toolbar-title v-if="!editChecklistName" tag="h1" class="font-weight-bold">
        {{ checklist.name }}
      </v-toolbar-title>
      <v-btn
        v-if="!editChecklistName"
        icon
        class="ml-1 visible-on-hover"
        width="24"
        height="24"
        @click="makeChecklistNameEditable()"
      >
        <v-icon small>mdi-pencil</v-icon>
      </v-btn>
      <api-form v-if="editChecklistName" :entity="checklist" class="mx-2 flex-grow-1">
        <api-text-field
          path="name"
          :disabled="layoutMode"
          dense
          autofocus
          :auto-save="false"
          @finished="editChecklistName = false"
        />
      </api-form>
    </template>
    <template #title-actions>
      <ChecklistItemCreate :checklist="checklist" />
      <!-- hamburger menu -->
      <v-menu offset-y>
        <template #activator="{ on, attrs }">
          <v-btn icon v-bind="attrs" v-on="on">
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
            <template #activator="{ on }">
              <v-list-item v-on="on">
                <v-list-item-icon>
                  <v-icon>mdi-delete</v-icon>
                </v-list-item-icon>
                <v-list-item-title>
                  {{ $tc('global.button.delete') }}
                </v-list-item-title>
              </v-list-item>
            </template>
            {{ $tc('components.checklist.checklistDetail.deleteWarning') }}
          </DialogEntityDelete>
        </v-list>
      </v-menu>
    </template>
    <v-list>
      <SortableChecklist :parent="null" :checklist="checklist" />
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

export default {
  name: 'ChecklistDetail',
  components: {
    SortableChecklist,
    ChecklistItemCreate,
    ContentCard,
    ApiForm,
    DialogEntityDelete,
  },
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
    return { dragging: false, editChecklistName: false }
  },
  computed: {
    items() {
      return this.checklist.checklistItems().items.filter((item) => !item.parent)
    },
  },
  methods: {
    makeChecklistNameEditable() {
      this.editChecklistName = true
    },
    deleteErrorHandler(e) {
      if (e?.response?.status === 422 /* Validation Error */) {
        return this.$tc('components.checklist.checklistDetail.deleteError')
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
