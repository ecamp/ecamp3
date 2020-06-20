<template>
  <div v-if="allowed">
    <div v-for="activityContent in activityContents" :key="activityContent._meta.self">
      <activity-content :activity-content="activityContent" />
      <br>
    </div>

    <v-btn
      v-if="empty || contentType.allowMultiple"
      color="primary"
      outlined
      :loading="isAdding"
      block
      @click="addActivityContent">
      <v-icon :left="$vuetify.breakpoint.smAndUp" size="150%">mdi-plus</v-icon>

      {{ $tc(addContentKey, activityContents.length + 1) }}
    </v-btn>
  </div>
</template>

<script>
import ActivityContent from './ActivityContent'
import camelCase from 'lodash/camelCase'

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
    addContentKey () {
      return `activityContent.${camelCase(this.contentTypeName)}.add`
    },
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
      return this.activity.activityCategory().activityType().activityTypeContentTypes().items.find(atct => atct.contentType().name === this.contentTypeName)
    },

    // number of content instances
    numberOfContents () {
      return this.activityContents.length
    },

    // true if content instance exists
    empty () {
      return this.numberOfContents === 0
    },

    // true if content type is allowed on this activity type
    allowed () {
      return this.activityTypeContentType !== null
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
</style>
