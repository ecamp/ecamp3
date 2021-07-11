<!--
Admin screen of a camp: Displays details & periods of a single camp and allows to edit them.
-->

<template>
  <content-card :title="$tc('views.camp.story.title')" toolbar>
    <template #title-actions>
      <template v-if="$vuetify.breakpoint.smAndUp">
        <e-switch v-model="editing" :label="$tc('global.button.editable')"
                  class="ec-story-editable ml-auto"
                  @click="$event.preventDefault()" />
        <v-btn
          icon
          @click="expandAll">
          <v-icon>mdi-arrow-expand-vertical</v-icon>
        </v-btn>
        <v-btn
          icon
          @click="collapseAll">
          <v-icon>mdi-arrow-collapse-vertical</v-icon>
        </v-btn>
      </template>
      <v-menu v-else offset-y>
        <template #activator="{ on, attrs }">
          <v-btn
            text icon
            class="ml-auto"
            v-bind="attrs"
            v-on="on">
            <v-icon>mdi-dots-vertical</v-icon>
          </v-btn>
        </template>
        <v-list>
          <v-list-item :href="previewUrl">
            <v-list-item-icon>
              <v-icon>mdi-printer</v-icon>
            </v-list-item-icon>
            <v-list-item-content>
              {{ $tc('views.camp.print.title') }}
            </v-list-item-content>
          </v-list-item>
          <v-list-item>
            <e-switch v-model="editing" :label="$tc('global.button.editable')"
                      class="ec-story-editable"
                      @click.stop="$event.preventDefault()" />
          </v-list-item>
        </v-list>
      </v-menu>
    </template>
    <v-expansion-panels v-model="expandedDays" multiple
                        flat accordion>
      <story-day v-for="day in period().days().items" :key="day._meta.self"
                 :day="day"
                 :editing="editing" />
    </v-expansion-panels>
    <v-card-actions v-if="$vuetify.breakpoint.smAndUp">
      <v-btn
        class="ml-auto"
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
import ContentCard from '@/components/layout/ContentCard.vue'
import StoryDay from '@/components/camp/StoryDay.vue'

const PRINT_SERVER = window.environment.PRINT_SERVER

export default {
  name: 'Story',
  components: {
    StoryDay,
    ContentCard
  },
  props: {
    camp: { type: Function, required: true },
    period: { type: Function, required: true }
  },
  data () {
    return {
      editing: false,
      expandedDays: []
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
  watch: {
    period () {
      this.setInitialExpandedDays()
    }
  },
  mounted () {
    this.setInitialExpandedDays()
  },
  methods: {
    expandAll () {
      this.expandedDays = [...Array(this.period().days().items.length).keys()]
    },
    collapseAll () {
      this.expandedDays = []
    },
    setInitialExpandedDays () {
      this.period().days()._meta.load.then(days => {
        this.expandedDays = days.items
          .map((day, idx) => this.$date((this.period().start)).add(day.dayOffset + 1, 'd').isAfter(this.$date()) ? idx : null)
          .filter(idx => idx !== null)
        console.log('expanded days: ', this.expandedDays)
      })
    }
  }
}
</script>

<style lang="scss" scoped>
.ec-story-editable ::v-deep .v-input--selection-controls {
  margin-top: 0;
}
</style>
