<!--
Admin screen of a camp: Displays details & periods of a single camp and allows to edit them.
-->

<template>
  <content-card :title="$tc('views.camp.story.title')">
    <v-card-text>
      <e-checkbox
        v-model="editing"
        :label="$tc('views.camp.story.edit')" />
      <story-period v-for="period in camp().periods().items"
                    :key="period._meta.self"
                    :period="period"
                    :editing="editing" />
    </v-card-text>
  </content-card>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard'
import StoryPeriod from '@/components/camp/StoryPeriod'
import ECheckbox from '@/components/form/base/ECheckbox'

export default {
  name: 'Story',
  components: {
    ECheckbox,
    StoryPeriod,
    ContentCard
  },
  props: {
    camp: { type: Function, required: true }
  },
  data () {
    return {
      editing: false
    }
  }
}
</script>
