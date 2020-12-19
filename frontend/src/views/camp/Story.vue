<!--
Admin screen of a camp: Displays details & periods of a single camp and allows to edit them.
-->

<template>
  <content-card :title="$tc('views.camp.story.title')">
    <template v-slot:title-actions>
      <e-switch v-model="editing" :label="$tc('global.button.editable')"
                class="ec-story-editable"
                @click="$event.preventDefault()" />
    </template>
    <v-card-actions v-if="$vuetify.breakpoint.smAndUp">
      <v-spacer />
      <e-switch v-model="editing" :label="$tc('global.button.editable')"
                class="ec-story-editable"
                @click="$event.preventDefault()" />
    </v-card-actions>
    <v-expansion-panels v-model="openPeriods" multiple
                        flat accordion>
      <story-period v-for="period in camp().periods().items"
                    :key="period._meta.self"
                    :period="period"
                    :editing="editing" />
    </v-expansion-panels>
    <v-card-actions>
      <v-btn
        color="primary"
        :href="previewUrl"
        target="_blank">
        <v-icon left>mdi-printer</v-icon>
        {{ $tc('views.camp.print.title') }}
      </v-btn>
    </v-card-actions>
  </content-card>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard'
import StoryPeriod from '@/components/camp/StoryPeriod'

const PRINT_SERVER = window.environment.PRINT_SERVER

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
    return {
      editing: false,
      openPeriods: []
    }
  },
  computed: {
    previewUrl () {
      const config = {
        showStoryline: true
      }
      const configGetParams = Object.entries(config).map(([key, val]) => `${key}=${val}`).join('&')
      return `${PRINT_SERVER}/?camp=${this.camp().id}&pagedjs=true&${configGetParams}`
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

<style lang="scss" scoped>
.ec-story-editable ::v-deep .v-input--selection-controls {
  margin-top: 0;
}
</style>
