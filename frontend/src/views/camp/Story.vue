<!--
Admin screen of a camp: Displays details & periods of a single camp and allows to edit them.
-->

<template>
  <content-card :title="$tc('views.camp.story.title')" toolbar>
    <template #title-actions>
      <template v-if="$vuetify.breakpoint.smAndUp">
        <e-switch v-model="editing" :label="$tc('global.button.editable')"
                  class="ec-story-editable ml-auto"
                  :disabled="!isContributor"
                  @click="$event.preventDefault()" />
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
    <v-expansion-panels v-model="openPeriods" multiple
                        flat accordion>
      <story-period v-for="period in camp().periods().items"
                    :key="period._meta.self"
                    :period="period"
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
import StoryPeriod from '@/components/camp/StoryPeriod.vue'
import { campRoleMixin } from '@/mixins/campRoleMixin'

const PRINT_SERVER = window.environment.PRINT_SERVER

export default {
  name: 'Story',
  components: {
    StoryPeriod,
    ContentCard
  },
  mixins: [campRoleMixin],
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
