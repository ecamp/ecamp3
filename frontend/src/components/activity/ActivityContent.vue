<template>
  <v-card
    outlined>
    <v-btn
      color="error"
      class="float-right"

      icon
      @click="removeActivityContent">
      <v-icon>mdi-delete</v-icon>
    </v-btn>
    <v-card-title>
      {{ activityContent.instanceName || $t('activity.content.' + activityContent.contentTypeName + '.name') }}
    </v-card-title>
    <v-card-text>
      <component :is="activityContent.contentTypeName" :activity-content="activityContent" />
    </v-card-text>
  </v-card>
</template>

<script>

import Storycontext from '@/components/activity/content/Storycontext'
import Storyboard from '@/components/activity/content/Storyboard'

export default {
  name: 'ActivityContent',
  components: {
    Storycontext,
    Storyboard
  },
  props: {
    activityContent: { type: Object, required: true }
  },
  data () {
    return {
      isDeleting: false
    }
  },
  methods: {
    async removeActivityContent () {
      this.api.del(this.activityContent)
    }
  }
}
</script>

<style scoped>
  .activity-content-container {
    border: 1px grey dashed;
    padding:5px;
    background-color:lightgrey;
  }
</style>
