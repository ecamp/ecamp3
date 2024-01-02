<template>
  <div>
    <div class="e-form-container d-flex gap-2">
      <e-text-field
        v-model="localActivity.title"
        :name="$tc('entity.activity.fields.title')"
        vee-rules="required"
        class="flex-grow-1"
      />
      <slot name="textFieldTitleAppend" />
    </div>

    <e-select
      v-model="localActivity.category"
      :name="$tc('entity.activity.fields.category')"
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
      v-if="!hideHeaderFields"
      v-model="localActivity.location"
      :name="$tc('entity.activity.fields.location')"
    />

    <e-select
      v-if="!hideHeaderFields"
      v-model="localActivity.activityResponsibles"
      :items="availableCampCollaborations"
      item-value="campCollaboration._meta.self"
      return-object
      :name="$tc('entity.activity.fields.responsible')"
      multiple
      chips
      deletable-chips
      small-chips
      class="camp-collaboration-select"
    >
      <template #selection="{ item }">
        <v-chip :key="item.campCollaboration._meta.self" small class="mx-0">
          <UserAvatar
            :camp-collaboration="item.campCollaboration"
            left
            size="20"
            class="ml-n3"
          />
          <span>{{ campCollaborationDisplayName(item.campCollaboration, $tc) }}</span>
        </v-chip>
      </template>
    </e-select>

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
import UserAvatar from '@/components/user/UserAvatar.vue'
import campCollaborationDisplayName from '@/common/helpers/campCollaborationDisplayName.js'

export default {
  name: 'DialogActivityForm',
  components: {
    UserAvatar,
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
    hideHeaderFields: {
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
    availableCampCollaborations() {
      return this.camp
        .campCollaborations()
        .items.filter((cc) => {
          return cc.status !== 'inactive'
        })
        .map((campCollaboration) => ({
          campCollaboration,
          text: campCollaborationDisplayName(campCollaboration, this.$tc.bind(this)),
        }))
    },
  },
  methods: {
    campCollaborationDisplayName,
  },
}
</script>

<style scoped>
.camp-collaboration-select :deep(.v-select__selections) {
  gap: 4px;

  & input {
    padding: 0;
  }
}
</style>
