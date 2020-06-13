<!--
Displays all periods of a single camp and allows to edit them & create new ones
-->

<template>
  <content-group :title="$t('activityCategory.title')">
    <v-skeleton-loader v-if="camp()._meta.loading" type="article" />
    <v-list>
      <v-list-item
        v-for="activityCategory in activityCategories.items"
        :key="activityCategory.id"
        class="px-0">
        <v-list-item-content class="pt-0 pb-2">
          <v-list-item-title>
            <v-chip dark :color="activityCategory.color">
              (1.{{ activityCategory.numberingStyle }})
              {{ activityCategory.short }}: {{ activityCategory.name }}
            </v-chip>
          </v-list-item-title>
        </v-list-item-content>

        <v-list-item-action style="display: inline">
          <v-item-group>
            <dialog-activity-category-edit :activity-category="activityCategory">
              <template v-slot:activator="{ on }">
                <button-edit class="mr-1" v-on="on" />
              </template>
            </dialog-activity-category-edit>

            <dialog-entity-delete :entity="activityCategory">
              <template v-slot:activator="{ on }">
                <button-delete v-on="on" />
              </template>
              the ActivityCategory "{{ activityCategory.short }}: {{ activityCategory.name }}"
            </dialog-entity-delete>
          </v-item-group>
        </v-list-item-action>
      </v-list-item>

      <v-list-item class="px-0">
        <v-list-item-content />
        <v-list-item-action>
          <dialog-activity-category-create :camp="camp()">
            <template v-slot:activator="{ on }">
              <button-add v-on="on">
                {{ $t('activityCategory.create') }}
              </button-add>
            </template>
          </dialog-activity-category-create>
        </v-list-item-action>
      </v-list-item>
    </v-list>
  </content-group>
</template>

<script>
import ButtonAdd from '@/components/buttons/ButtonAdd'
import ButtonEdit from '@/components/buttons/ButtonEdit'
import ButtonDelete from '@/components/buttons/ButtonDelete'
import ContentGroup from '@/components/layout/ContentGroup'
import DialogActivityCategoryCreate from '@/components/dialog/DialogActivityCategoryCreate'
import DialogEntityDelete from '@/components/dialog/DialogEntityDelete'
import DialogActivityCategoryEdit from '@/components/dialog/DialogActivityCategoryEdit'

export default {
  name: 'CampActivityCategories',
  components: {
    ButtonAdd,
    ButtonEdit,
    ButtonDelete,
    DialogActivityCategoryCreate,
    DialogActivityCategoryEdit,
    DialogEntityDelete,
    ContentGroup
  },
  props: {
    camp: { type: Function, required: true }
  },
  data () {
    return {
    }
  },
  computed: {
    activityCategories () {
      return this.camp().activityCategories()
    }
  }
}
</script>

<style scoped>
</style>
