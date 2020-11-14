<!--
Admin screen of a camp: Displays details & periods of a single camp and allows to edit them.
-->

<template>
  <content-card>
    <v-toolbar>
      <v-card-title>{{ $tc('views.camp.story.title') }}</v-card-title>
      <v-spacer></v-spacer>
      <e-switch v-model="editing" :label="editing ? $tc('views.camp.story.editModeOn') : $tc('views.camp.story.editModeOff')" />
    </v-toolbar>
    <v-card-text>
      <v-expansion-panels v-model="openPeriods" multiple>
        <story-period v-for="period in camp().periods().items"
                      :key="period._meta.self"
                      :period="period"
                      :editing="editing" />
      </v-expansion-panels>
    </v-card-text>
  </content-card>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard'
import StoryPeriod from '@/components/camp/StoryPeriod'
import ESwitch from '@/components/form/base/ESwitch'

export default {
  name: 'Story',
  components: {
    ESwitch,
    StoryPeriod,
    ContentCard
  },
  props: {
    camp: { type: Function, required: true }
  },
  data () {
    return {
      editing: false,
      openPeriods: []
    }
  },
  mounted () {
    this.camp().periods()._meta.load.then(periods => {
      this.openPeriods = periods.items
        .map((period, idx) => Date.parse(period.end) >= new Date() ? idx : null)
        .filter(idx => idx !== null)
    })
  }
}
</script>
