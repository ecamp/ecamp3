<template>
  <div>
    <e-text-field
      v-model="localActivity.title"
      :name="$tc('entity.activity.fields.title')"
      vee-rules="required" />

    <e-select
      v-model="localActivity.category"
      :name="$tc('entity.activity.fields.category')"
      :items="categories.items"
      item-value="_meta.self"
      item-text="name"
      vee-rules="required">
      <template #item="{ item, on, attrs }">
        <v-list-item :key="item._meta.self" v-bind="attrs" v-on="on">
          <v-list-item-avatar>
            <v-chip :color="item.color">{{ item.short }}</v-chip>
          </v-list-item-avatar>
          <v-list-item-content>
            {{ item.name }}
          </v-list-item-content>
        </v-list-item>
      </template>
      <template #selection="{ item }">
        <div class="v-select__selection">
          <span class="black--text">
            {{ item.name }}
          </span>
          <v-chip x-small :color="item.color">{{ item.short }}</v-chip>
        </div>
      </template>
    </e-select>

    <e-text-field
      v-model="localActivity.location"
      :name="$tc('entity.activity.fields.location')" />

    <form-schedule-entry-list
      v-if="activity.scheduleEntries"
      :schedule-entries="activity.scheduleEntries"
      :period="period"
      :periods="camp.periods().items" />
  </div>
</template>

<script>
import FormScheduleEntryList from './FormScheduleEntryList.vue'

export default {
  name: 'DialogActivityForm',
  components: { FormScheduleEntryList },
  props: {
    activity: {
      type: Object,
      required: true
    },

    // currently visible period
    period: {
      type: Function,
      required: true
    }
  },
  data () {
    return {
      localActivity: this.activity
    }
  },
  computed: {
    categories () {
      return this.camp.categories()
    },
    camp () {
      return this.period().camp()
    }
  }
}
</script>
