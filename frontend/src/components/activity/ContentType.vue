<template>
  <div v-if="activityContents.length > 0">
    <v-card outlined elevation="1" class="mb-3">
      <v-expansion-panels v-model="openPanels" multiple flat>
        <activity-content v-for="activityContent in activityContents"
                          :key="activityContent._meta.self"
                          :activity-content="activityContent" />
      </v-expansion-panels>
    </v-card>
  </div>
</template>

<script>
import ActivityContent from './ActivityContent'

export default {
  name: 'ContentType',
  components: {
    ActivityContent
  },
  props: {
    contentTypeName: { type: String, required: true },
    activity: { type: Object, required: true }
  },
  data () {
    return {
      openPanels: [0]
    }
  },
  computed: {
    activityContents () {
      return this.activity.activityContents().items.filter(ep => ep.contentTypeName === this.contentTypeName)
    }
  }
}
</script>

<style scoped>

div.v-expansion-panel:not(:first-child) {
  border-top-left-radius: 0 !important;
  border-top-right-radius: 0 !important;
  border-top: solid 1px lightgray;
  margin-top: 0;
  padding-top: 0;
}

div.v-expansion-panel >>> > .v-expansion-panel-header {
  min-height: 48px;
}

div.v-expansion-panel >>> > .v-expansion-panel-header > header {
  border-radius: 6px;
}

div.v-expansion-panel >>> > .v-expansion-panel-content .v-expansion-panel-content__wrap {
  padding: 0 16px;
}

</style>
