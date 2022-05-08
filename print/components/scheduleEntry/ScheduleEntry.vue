<template>
  <div class="tw-mb-20 tw-break-inside-avoid">
    <div class="schedule-entry-title tw-float-left">
      <h2
        :id="`content_${index}_scheduleEntry_${scheduleEntry.id}`"
        class="tw-text-xl tw-font-bold tw-pt-1"
      >
        {{ scheduleEntry.number }}
        <category-label :category="scheduleEntry.activity().category()" />
        {{ scheduleEntry.activity().title }}
      </h2>
    </div>
    <div class="tw-float-right">
      {{ rangeShort(scheduleEntry.start, scheduleEntry.end) }}
    </div>
    <div class="header tw-clear-both tw-mb-4">
      <table class="header-table">
        <tr>
          <th class="header-row left-col">
            {{ $tc('entity.activity.fields.location') }}
          </th>
          <td class="header-row">
            {{ scheduleEntry.activity().location }}
          </td>
        </tr>
        <tr>
          <th class="header-row left-col">
            {{ $tc('entity.activity.fields.responsible') }}
          </th>
          <td class="header-row">
            {{ responsiblesListed }}
            <!-- <user-avatar
              v-for="responsible in responsibles"
              :key="responsible.id"
              :user="responsible.campCollaboration().user()"
              :size="16"
            /> -->
          </td>
        </tr>
      </table>
    </div>
    <content-node :content-node="scheduleEntry.activity().rootContentNode()" />
  </div>
</template>

<script>
import CategoryLabel from './CategoryLabel.vue'
import ContentNode from './contentNode/ContentNode.vue'
import { rangeShort } from '@/../common/helpers/dateHelperUTCFormatted.js'
import campCollaborationDisplayName from '@/../common/helpers/campCollaborationDisplayName.js'

export default {
  components: { CategoryLabel, ContentNode },
  props: {
    scheduleEntry: { type: Object, required: true },
    index: { type: Number, required: true },
  },
  async fetch() {
    await Promise.all([
      this.scheduleEntry._meta.load,
      this.scheduleEntry.activity()._meta.load,
      this.scheduleEntry.activity().rootContentNode()._meta.load,
      this.scheduleEntry.activity().category()._meta.load,
      this.scheduleEntry.period().camp().materialLists().$loadItems(),
      // prettier-ignore
      this.scheduleEntry.activity().activityResponsibles().$loadItems().then(
        (activityResponsibles) => {
          return Promise.all(activityResponsibles.items.map((activityResponsible) => activityResponsible.campCollaboration().user()._meta.load))
        }  
      ),
    ])
  },
  computed: {
    responsibles() {
      return this.scheduleEntry.activity().activityResponsibles().items
    },
    responsiblesListed() {
      return this.scheduleEntry
        .activity()
        .activityResponsibles()
        .items.map((activityResponsible) =>
          campCollaborationDisplayName(activityResponsible.campCollaboration())
        )
        .join(', ')
    },
  },
  methods: {
    rangeShort,
  },
}
</script>

<style lang="scss" scoped>
.schedule-entry-title {
  display: flex;
  flex-direction: row;
  margin-bottom: 0.5rem;

  h2 {
    flex-grow: 1;
  }
}

.header-table {
  border-spacing: 0;
  width: 100%;
}

.header-row {
  border-bottom: 1px solid black;
  padding: 0.2rem;
  padding-left: 0.4rem;
  width: 90%;
}

.left-col {
  border-right: 1px solid black;
  font-weight: bold;
  text-align: left;
  width: 10%;
  padding-left: 0;
  padding-right: 0.4rem;
}
</style>
