<template>
  <div>
    <div class="e-form-container d-flex gap-2">
      <e-text-field
        v-model="localActivity.title"
        name="title"
        :label="$tc('entity.activity.fields.title')"
        vee-rules="required"
        class="flex-grow-1"
      />
      <slot name="textFieldTitleAppend" />
    </div>

    <e-select
      v-model="localActivity.category"
      name="category"
      :label="$tc('entity.activity.fields.category')"
      :items="categories.items"
      item-value="_meta.self"
      item-text="name"
      vee-rules="required"
    >
      <template #item="{ item, on, attrs }">
        <v-list-item :key="item._meta.self" v-bind="attrs" v-on="on">
          <v-list-item-title>
            <category-chip :category="item" dense />
            {{ item.name }}
          </v-list-item-title>
        </v-list-item>
      </template>
      <template #selection="{ item }">
        <div class="v-select__selection">
          <category-chip :category="item" dense />
          {{ item.name }}
        </div>
      </template>
    </e-select>

    <e-text-field
      v-if="!hideLocation"
      v-model="localActivity.location"
      name="location"
      :label="$tc('entity.activity.fields.location')"
    />

    <FormScheduleEntryList
      v-if="activity.scheduleEntries"
      :schedule-entries="activity.scheduleEntries"
      :period="period"
      :periods="camp.periods().items"
    />
  </div>
</template>

<script>
import CategoryChip from '@/components/generic/CategoryChip.vue'
import FormScheduleEntryList from './FormScheduleEntryList.vue'

export default {
  name: 'DialogActivityForm',
  components: {
    CategoryChip,
    FormScheduleEntryList,
  },
  props: {
    activity: {
      type: Object,
      required: true,
    },
    // currently visible period
    period: {
      type: Function,
      required: true,
    },
    hideLocation: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      localActivity: this.activity,
    }
  },
  computed: {
    categories() {
      return this.camp.categories()
    },
    camp() {
      return this.period().camp()
    },
  },
}
</script>
