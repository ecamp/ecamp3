<template>
  <v-expansion-panel :value="panelValue">
    <v-expansion-panel-title>
      <h3>{{ dateLong(day.start) }}</h3>
    </v-expansion-panel-title>
    <v-expansion-panel-text>
      <template v-if="entriesWithStory.length">
        <template v-for="{ scheduleEntry, storyChapters } in entriesWithStory">
          <div
            v-for="chapter in storyChapters"
            :key="scheduleEntry._meta.self + chapter._meta.self"
          >
            <h4 class="mt-3 mt-sm-5">
              <span class="d-inline-flex align-center">
                <span v-if="scheduleEntry.number" class="tabular-nums">{{
                  scheduleEntry.number
                }}</span>
                <CategoryChip
                  :schedule-entry="scheduleEntry"
                  class="mr-1"
                  :class="{ 'ml-1': scheduleEntry.number }"
                  dense
                />
              </span>
              <router-link :to="scheduleEntryRoute(scheduleEntry)" class="e-title-link">
                <span>{{ scheduleEntry.activity().title }}</span>
                <template v-if="chapter.instanceName">
                  - {{ chapter.instanceName }}
                </template>
              </router-link>
            </h4>
            <api-form :entity="chapter">
              <api-richtext
                class="e-story-day"
                :class="{ 'e-story-day--textmode': !editMode }"
                :variant="editMode ? 'underlined' : 'plain'"
                :solo="false"
                auto-grow
                density="compact"
                :readonly="!editMode"
                path="data.html"
                label=""
              />
            </api-form>
          </div>
        </template>
      </template>
      <p v-else>
        {{ $t('components.story.storyDay.noStory') }}
      </p>
    </v-expansion-panel-text>
  </v-expansion-panel>
</template>
<script>
import ApiForm from '@/components/form/api/ApiForm.vue'
import { dateHelperUTCFormatted } from '@/mixins/dateHelperUTCFormatted.js'
import CategoryChip from '@/components/generic/CategoryChip.vue'
import { scheduleEntryRoute } from '@/router.js'

export default {
  name: 'StoryDay',
  components: {
    CategoryChip,
    ApiForm,
  },
  mixins: [dateHelperUTCFormatted],
  props: {
    day: { type: Object, required: true },
    editMode: { type: Boolean, default: false },
    periodStoryChapters: { type: Array, required: true },
  },
  computed: {
    // returns scheduleEntries of current day without the need for an additional API call
    scheduleEntries() {
      return this.day
        .period()
        .scheduleEntries()
        .items.filter((scheduleEntry) => {
          return scheduleEntry.day()._meta.self === this.day._meta.self
        })
    },
    entries() {
      return this.scheduleEntries.map((scheduleEntry) => ({
        scheduleEntry: scheduleEntry,
        storyChapters: this.periodStoryChapters.filter(
          (contentNode) =>
            contentNode.root()._meta.self ===
            scheduleEntry.activity().rootContentNode()._meta.self
        ),
      }))
    },
    entriesWithStory() {
      return this.entries.filter(({ storyChapters }) => storyChapters.length)
    },
    panelValue() {
      // Mark the component with the date of the day.
      // This allows the use of the date in the parent component.
      // See StoryPeriod.vue: v-expansion-panels.v-model
      return this.day.start.substr(0, 10)
    },
  },
  methods: {
    scheduleEntryRoute,
  },
}
</script>

<style scoped>
:deep(.v-skeleton-loader__list-item-three-line) {
  padding: 0;
  height: auto;
}

.e-story-day :deep(.v-text-field) {
  margin-top: 0;
  padding-top: 0;
}

/* this disables the bottom border which is displayed for VTextField in "regular" style */
.e-story-day--textmode :deep(.v-input__slot)::before {
  display: none;
}
</style>
