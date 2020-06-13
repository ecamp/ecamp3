<!--
Displays a single activity
-->

<template>
  <div>
    <v-expansion-panels
      v-model="panel"
      multiple>
      <v-expansion-panel>
        <v-expansion-panel-header>Header</v-expansion-panel-header>
        <v-expansion-panel-content>
          <content-type
            content-type-name="Storycontext"
            :activity="activity" />
        </v-expansion-panel-content>
      </v-expansion-panel>

      <v-expansion-panel>
        <v-expansion-panel-header>Storyboards</v-expansion-panel-header>
        <v-expansion-panel-content>
          <content-type
            content-type-name="Storyboard"
            :activity="activity" />
        </v-expansion-panel-content>
      </v-expansion-panel>

      <v-expansion-panel>
        <v-expansion-panel-header>Footer</v-expansion-panel-header>
        <v-expansion-panel-content />
      </v-expansion-panel>
    </v-expansion-panels>
  </div>
</template>

<script>

import ContentType from '@/components/activity/ContentType'

export default {
  name: 'General',
  components: {
    ContentType
  },
  props: {
    activity: { type: Object, required: true }
  },
  data () {
    return {
      panel: [0, 1, 2]
    }
  },
  computed: {
    activityType () {
      return this.activity.activityCategory().activityType()
    },
    activityTypeContentTypes () {
      return this.activityType.activityTypeContentTypes()
    }
  }
}
</script>

<style scoped>
</style>
