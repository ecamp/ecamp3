<template>
  <div>
    <h6>{{ activity.title }}</h6>
    <p v-for="chapter in story" :key="chapter" v-html="chapter" />
  </div>
</template>
<script>
export default {
  name: 'StoryActivity',
  props: {
    activity: { type: Object, required: true }
  },
  computed: {
    story () {
      if (!this.activity.activityContents) return ''
      return this.activity.activityContents().items
        .filter(activityContent => activityContent.contentTypeName === 'Storycontext')
        .map(storyContent => storyContent.singleText().text)
    }
  }
}
</script>
