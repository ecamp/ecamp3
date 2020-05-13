<template>
  <div v-if="activityTypeContentType" class="contentType-container">
    <div v-for="activityContent in activityContents" :key="activityContent._meta.self">
      <activity-content :activity-content="activityContent" />
      <br>
    </div>

    <v-btn color="primary" :loading="isAdding"
           block
           @click="addActivityContent">
      + Add another {{ contentTypeName }}
    </v-btn>
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
      isAdding: false
    }
  },
  computed: {
    activityContents () {
      // TODO: should we add the deleting-filter already to the store?
      return this.activity.activityContents().items.filter(ep => !ep._meta.deleting && ep.contentTypeName === this.contentTypeName)
    },
    contentType () {
      return this.activityTypeContentType.contentType()
    },
    // try to find the ActivityTypeContentType of given name `contentTypeName`
    // otherwise returns undefined and this component should not be shown
    activityTypeContentType () {
      return this.activity.activityCategory().activityType().activityTypeContentTypes().items.find(etp => etp.contentType().name === this.contentTypeName)
    }
  },
  methods: {
    async addActivityContent () {
      this.isAdding = true
      await this.api.post('/activity-contents', {
        activityId: this.activity.id,
        activityTypeContentTypeId: this.activityTypeContentType.id // POSSIBLE ALTERNATIVE: post with contentTypeId of activityTypeContentTypeId
      })
      await this.refreshActivity()
      this.isAdding = false
    },
    async refreshActivity () {
      await this.api.reload(this.activity._meta.self)
    }
  }
}
</script>

<style scoped>
  .contentType-container {
    padding:5px;
    background-color: grey;
  }
</style>
