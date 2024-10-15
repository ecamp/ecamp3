<template>
  <DetailPane
    v-model="showDialog"
    :loading="loading"
    :error="error"
    icon="mdi-account-plus"
    :title="$tc('components.checklist.checklistItemEdit.title')"
    :submit-action="update"
    :submit-label="$tc('global.button.submit')"
    submit-icon="mdi-send"
    submit-color="success"
    :cancel-action="close"
  >
    <template #moreActions>
      <PromptEntityDelete
        :entity="checklistItem"
        :submit-enabled="activitiesWithChecklistItem.length === 0"
        align="left"
        position="top"
        :btn-attrs="{
          class: 'v-btn--has-bg',
        }"
      >
        <template v-if="activitiesWithChecklistItem.length > 0" #error>
          <ErrorExistingActivitiesList
            :camp="camp"
            :existing-activities="activitiesWithChecklistItem"
          />
        </template>
        {{
          $tc('components.checklist.checklistItemEdit.delete', 0, {
            text: checklistItem.text,
          })
        }}
      </PromptEntityDelete>
    </template>

    <template #activator="{ on }">
      <slot name="activator" v-bind="{ on }" />
    </template>

    <e-text-field
      v-model="entityData.text"
      type="text"
      path="text"
      vee-rules="required"
      autofocus
    />
  </DetailPane>
</template>

<script>
import { sortBy } from 'lodash'
import DetailPane from '@/components/generic/DetailPane.vue'
import DialogBase from '@/components/dialog/DialogBase.vue'
import PromptEntityDelete from '@/components/prompt/PromptEntityDelete.vue'
import ErrorExistingActivitiesList from '@/components/campAdmin/ErrorExistingActivitiesList.vue'

export default {
  name: 'ChecklistItemEdit',
  components: {
    DetailPane,
    PromptEntityDelete,
    ErrorExistingActivitiesList,
  },
  extends: DialogBase,
  provide() {
    return {
      entityName: 'checklistItem',
    }
  },
  props: {
    checklist: { type: Object, required: true },
    checklistItem: { type: Object, default: null },
  },
  data() {
    return {
      entityProperties: ['checklist', 'text'],
      entityUri: '',
    }
  },
  computed: {
    camp() {
      return this.checklist.camp()
    },
    activitiesWithChecklistItem() {
      const activities = this.camp.activities().items
      const allChecklistNodes = this.api
        .get()
        .checklistNodes({ camp: this.camp._meta.self }).items

      const checklistNodes = allChecklistNodes.filter((cn) =>
        cn.checklistItems().items.some((ci) => ci.id === this.checklistItem.id)
      )

      return sortBy(
        activities.filter((a) =>
          checklistNodes.some((cn) => cn.root().id === a.rootContentNode().id)
        ),
        (activity) =>
          activity
            .scheduleEntries()
            .items.map(
              (s) =>
                `${s.dayNumber}`.padStart(3, '0') +
                `${s.scheduleEntryNumber}`.padStart(3, '0')
            )
            .reduce((p, v) => (p < v ? p : v))
      )
    },
  },
  watch: {
    showDialog: function (showDialog) {
      if (showDialog) {
        this.entityUri = this.checklistItem._meta.self
        this.setEntityData({
          text: this.checklistItem.text,
        })
      } else {
        // clear form on exit
        this.clearEntityData()
      }
    },
  },
  async mounted() {
    this.api.href(this.api.get(), 'checklistItems').then((uri) => (this.entityUri = uri))

    await this.api.get().checklistNodes({ camp: this.camp._meta.self })
    await this.api.get().checklistItems({ 'checklist.camp': this.camp._meta.self })
  },
}
</script>

<style scoped></style>
