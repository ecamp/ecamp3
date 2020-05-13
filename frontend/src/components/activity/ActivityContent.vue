<template>
  <div class="activity-content-container">
    <v-btn
      color="error"
      class="float-right"
      small
      @click="removeActivityContent">
      Remove this {{ activityContent.contentTypeName }}
    </v-btn>
    <h3> {{ activityContent.instanceName || activityContent.contentTypeName }}</h3>
    <component :is="activityContent.contentTypeName" :activity-content="activityContent" />
    <br>
  </div>
</template>

<script>

import Textarea from '@/components/activity/content/Textarea'
import Storyboard from '@/components/activity/content/Storyboard'
import Richtext from '@/components/activity/content/Richtext'

export default {
  name: 'ActivityContent',
  components: {
    Textarea,
    Storyboard,
    Richtext
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
