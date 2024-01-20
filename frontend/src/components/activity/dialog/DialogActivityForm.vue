<template>
  <e-form name="activity">
    <div class="e-form-container d-flex gap-2">
      <e-text-field
        v-model="localActivity.title"
        path="title"
        vee-rules="required"
        class="flex-grow-1"
        autofocus
        @focus="autoselectTitle ? $event.target.select() : null"
      />
      <slot name="textFieldTitleAppend" />
    </div>

    <e-select
      v-model="localActivity.category"
      path="category"
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

    <e-text-field v-if="!hideLocation" v-model="localActivity.location" path="location" />

    <FormScheduleEntryList
      v-if="activity.scheduleEntries"
      :schedule-entries="activity.scheduleEntries"
      :period="period"
      :periods="camp.periods().items"
    />
  </e-form>
</template>

<script>
import CategoryChip from '@/components/generic/CategoryChip.vue'
import FormScheduleEntryList from './FormScheduleEntryList.vue'
import EForm from '@/components/form/base/EForm.vue'

export default {
  name: 'DialogActivityForm',
  components: {
    EForm,
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
      type: Object,
      required: true,
    },
    autoselectTitle: {
      type: Boolean,
      default: false,
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
      return this.period.camp()
    },
  },
}
</script>
