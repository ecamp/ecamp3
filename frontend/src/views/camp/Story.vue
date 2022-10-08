<template>
  <content-card :title="$tc('views.camp.story.title')" toolbar>
    <template #title-actions>
      <period-switcher :period="period" :route-name="'camp/period/story'" />
      <v-spacer />
      <template v-if="$vuetify.breakpoint.smAndUp">
        <e-switch
          v-model="editing"
          :disabled="!isContributor"
          :label="$tc('global.button.editable')"
          class="ec-story-editable ml-auto"
          @click="$event.preventDefault()"
        />
      </template>
      <v-menu v-else offset-y>
        <template #activator="{ on, attrs }">
          <v-btn class="ml-auto" text icon v-bind="attrs" v-on="on">
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
            <e-switch
              v-model="editing"
              :label="$tc('global.button.editable')"
              class="ec-story-editable"
              @click.stop="$event.preventDefault()"
            />
          </v-list-item>
        </v-list>
      </v-menu>
    </template>
    <story-period :editing="editing" :period="period()" />
    <v-card-actions v-if="$vuetify.breakpoint.smAndUp">
      <v-btn :href="previewUrl" class="ml-auto" color="primary" target="_blank">
        <v-icon left>mdi-printer</v-icon>
        {{ $tc('views.camp.print.title') }}
      </v-btn>
    </v-card-actions>
  </content-card>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import StoryPeriod from '@/components/story/StoryPeriod.vue'
import { campRoleMixin } from '@/mixins/campRoleMixin'

const PRINT_SERVER = window.environment.PRINT_SERVER

export default {
  name: 'Story',
  components: {
    StoryPeriod,
    ContentCard,
  },
  mixins: [campRoleMixin],
  props: {
    period: { type: Function, required: true },
    camp: { type: Function, required: true },
  },
  data() {
    return {
      editing: false,
    }
  },
  computed: {
    previewUrl() {
      const config = {
        showStoryline: true,
      }
      const configGetParams = Object.entries(config)
        .map(([key, val]) => `${key}=${val}`)
        .join('&')
      return `${PRINT_SERVER}/?camp=${this.camp().id}&pagedjs=true&${configGetParams}`
    },
  },
}
</script>

<style lang="scss" scoped>
.ec-story-editable ::v-deep .v-input--selection-controls {
  margin-top: 0;
}
</style>
