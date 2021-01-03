<!--
Displays a single activity
-->
<template>
  <div>
    <content-type
      content-type-name="Storycontext"
      :activity="activity" />
    <content-type
      content-type-name="Storyboard"
      :activity="activity" />
    <content-type
      content-type-name="SafetyConcept"
      :activity="activity" />
    <content-type
      content-type-name="Notes"
      :activity="activity" />
    <content-type
      content-type-name="Material"
      :activity="activity" />
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
  }
}
</script>
