<template>
  <div>
    <div class="schedule-entry-title">
      <h2>
        {{ scheduleEntry.number }}
        <category-label :category="scheduleEntry.activity().category()" />
        {{ scheduleEntry.activity().title }}
      </h2>
    </div>
    <div class="header">
      <table class="header-table">
        <tr>
          <th class="header-row left-col">
            {{ $tc('entity.activity.fields.location') }}
          </th>
          <td class="header-row">{{ scheduleEntry.activity().location }}</td>
        </tr>
        <tr>
          <th class="header-row left-col">
            {{ $tc('entity.activity.fields.responsible') }}
          </th>
          <td class="header-row">
            <user-avatar
              v-for="responsible in responsibles"
              :key="responsible.id"
              :user="responsible.user()"
              :size="16"
            />
          </td>
        </tr>
      </table>
    </div>
    <content-node :content-node="scheduleEntry.activity().rootContentNode()" />
  </div>
</template>

<script>
import UserAvatar from '../UserAvatar.vue'
import CategoryLabel from './CategoryLabel.vue'
import ContentNode from './contentNode/ContentNode.vue'

export default {
  components: { UserAvatar, CategoryLabel, ContentNode },
  props: {
    scheduleEntry: { type: Object, required: true },
  },
  async fetch() {
    await Promise.all([
      this.scheduleEntry._meta.load,
      this.scheduleEntry.activity()._meta.load,
      // prettier-ignore
      this.scheduleEntry.activity().contentNodes().$loadItems().then((contentNodes) => {
        return Promise.all(contentNodes.items.map((contentNode) => Promise.all([
          contentNode._meta.load,
          contentNode.children().$loadItems(),
          contentNode.contentType()._meta.load,
        ])))
      }),
      this.scheduleEntry.activity().category()._meta.load,
      this.scheduleEntry.period().camp().materialLists().$loadItems(),
      // prettier-ignore
      this.scheduleEntry.activity().campCollaborations().$loadItems().then((campCollaborations) => {
        return Promise.all(campCollaborations.items.map(campCollaboration => campCollaboration.user()._meta.load))
      }),
    ])
  },
  computed: {
    responsibles() {
      return this.scheduleEntry.activity().campCollaborations().items
    },
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
  padding: 0.1rem;
  width: 90%;
}

.left-col {
  border-right: 1px solid black;
  font-weight: bold;
  text-align: left;
  width: 10%;
}
</style>
