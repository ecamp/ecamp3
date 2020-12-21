<!--
Displays all periods of a single camp and allows to edit them & create new ones
-->

<template>
  <content-group>
    <slot name="title">
      <div class="ec-content-group__title py-1 subtitle-1">
        {{ $tc('components.camp.campActivityCategories.title') }}
        <dialog-activity-category-create :camp="camp()">
          <template v-slot:activator="{ on }">
            <button-add color="secondary" text
                        :hide-label="true"
                        v-on="on">
              {{ $tc('components.camp.campActivityCategories.create') }}
            </button-add>
          </template>
        </dialog-activity-category-create>
      </div>
    </slot>
    <v-skeleton-loader v-if="camp()._meta.loading" type="article" />
    <v-list>
      <v-list-item
        v-for="activityCategory in activityCategories.items"
        :key="activityCategory.id"
        class="px-0">
        <v-list-item-content class="pt-0 pb-2">
          <v-list-item-title>
            <v-chip dark :color="activityCategory.color">
              (1.{{ activityCategory.numberingStyle }}) {{ activityCategory.short }}: {{ activityCategory.name }}
            </v-chip>
          </v-list-item-title>
        </v-list-item-content>

        <v-list-item-action style="display: inline">
          <v-item-group>
            <dialog-activity-category-edit :camp="camp()" :activity-category="activityCategory">
              <template v-slot:activator="{ on }">
                <button-edit class="mr-1" v-on="on" />
              </template>
            </dialog-activity-category-edit>
          </v-item-group>
        </v-list-item-action>

        <v-menu offset-y>
          <template v-slot:activator="{ on, attrs }">
            <v-btn icon v-bind="attrs" v-on="on">
              <v-icon>mdi-dots-vertical</v-icon>
            </v-btn>
          </template>
          <v-card>
            <v-item-group>
              <v-list-item-action>
                <dialog-entity-delete :entity="activityCategory">
                  {{ $tc('components.camp.campActivityCategories.deleteActivityCategoryQuestion') }}
                  <ul>
                    <li>
                      {{ activityCategory.short }}: {{ activityCategory.name }}
                    </li>
                  </ul>
                  <template v-slot:activator="{ on }">
                    <button-delete v-on="on" />
                  </template>
                  <template v-if="findActivities(activityCategory).length > 0" v-slot:error>
                    {{ $tc('components.camp.campActivityCategories.deleteActivityCategoryNotPossibleInUse') }}
                    <ul>
                      <li v-for="activity in findActivities(activityCategory)" :key="activity.id">
                        {{ activity.title }}
                        <ul>
                          <li v-for="scheduleEntry in activity.scheduleEntries().items" :key="scheduleEntry.id">
                            <router-link :to="{ name: 'activity', params: { campId: camp().id, scheduleEntryId: scheduleEntry.id } }">
                              {{ scheduleEntry.startTime }} - {{ scheduleEntry.endTime }}
                            </router-link>
                          </li>
                        </ul>
                      </li>
                    </ul>
                  </template>
                </dialog-entity-delete>
              </v-list-item-action>
            </v-item-group>
          </v-card>
        </v-menu>
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
    return {}
  },
  computed: {
    activityCategories () {
      return this.camp().activityCategories()
    }
  },
  methods: {
    findActivities (activityCategory) {
      const activities = this.camp().activities()
      return activities.items.filter(a => a.activityCategory().id === activityCategory.id)
    }
  }
}
</script>

<style scoped>
</style>
