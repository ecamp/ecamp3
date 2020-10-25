<template>
  <div>
    <e-text-field
      v-model="activity.title"
      :name="$tc('entity.period.fields.description')"
      vee-rules="required" />

    <e-select v-model="activity.activityCategoryId" label="Aktivitätstyp"
              :items="activityCategories.items"
              item-value="id"
              item-text="name"
              vee-rules="required">
      <template #item="{item, on, attrs}">
        <v-list-item :key="item.id" v-bind="attrs" v-on="on">
          <v-list-item-avatar>
            <v-chip :color="item.color">{{ item.short }}</v-chip>
          </v-list-item-avatar>
          <v-list-item-content>
            {{ item.name }}
          </v-list-item-content>
        </v-list-item>
      </template>
      <template #selection="{item}">
        <div class="v-select__selection">
          <span class="black--text">
            {{ item.name }}
          </span>
          <v-chip x-small :color="item.color">{{ item.short }}</v-chip>
        </div>
      </template>
    </e-select>

    <e-text-field
      v-model="activity.location"
      :name="$tc('entity.activity.fields.location')" />

    <create-activity-schedule-entries :schedule-entries="scheduleEntries.items" />
  </div>
</template>

<script>
import ETextField from '@/components/form/base/ETextField'
import CreateActivityScheduleEntries from '@/components/activity/CreateActivityScheduleEntries'

export default {
  name: 'DialogActivityForm',
  components: {
    CreateActivityScheduleEntries,
    ETextField
  },
  props: {
    activity: {
      type: Object,
      required: true
    }
  },
  computed: {
    activityCategories () {
      return this.activity.camp().activityCategories()
    },
    scheduleEntries () {
      return this.activity.scheduleEntries()
    }
  }
}
</script>
