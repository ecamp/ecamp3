<!--
Admin screen of a camp: Displays details & periods of a single camp and allows to edit them.
-->

<template>
  <content-card :title="$tc('views.camp.story.title')">
    <v-card-text>
      <story-period v-for="period in camp().periods().items"
                    :key="period._meta.self"
                    :period="period" />
    </v-card-text>
  </content-card>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard'
import StoryPeriod from '@/components/camp/StoryPeriod'

export default {
  name: 'Story',
  components: {
    StoryPeriod,
    ContentCard
  },
  props: {
    camp: { type: Function, required: true }
  },
  data () {
    return {}
  }
}
</script>
