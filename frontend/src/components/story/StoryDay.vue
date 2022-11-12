<template>
  <v-expansion-panel>
    <v-expansion-panel-header>
      <h3>{{ dateLong(day.start) }}</h3>
    </v-expansion-panel-header>
    <v-expansion-panel-content>
      <v-skeleton-loader
        v-if="loading"
        class="mt-2 mt-sm-3"
        type="list-item-three-line"
      />
      <template v-else-if="entriesWithStory.length">
        <template v-for="{ scheduleEntry, storyChapters } in entriesWithStory">
          <div v-for="chapter in storyChapters" :key="chapter._meta.uri">
            <h4 class="mt-3 mt-sm-5">
              <span class="d-inline-flex align-center">
                <span class="tabular-nums">{{ scheduleEntry.number }}</span>
                <CategoryChip :schedule-entry="scheduleEntry" class="mx-1" dense />
              </span>
              <router-link
                :to="{
                  name: 'activity',
                  params: {
                    campId: day.period().camp().id,
                    scheduleEntryId: scheduleEntry.id,
                  },
                }"
                class="e-title-link"
              >
                <span>{{ scheduleEntry.activity().title }}</span>
                <template v-if="chapter.instanceName">
                  - {{ chapter.instanceName }}
                </template>
              </router-link>
            </h4>
            <api-form :entity="chapter">
              <api-richtext
                class="e-story-day"
                :class="{ 'e-story-day--textmode': !editing }"
                :outlined="false"
                :solo="false"
                auto-grow
                dense
                :readonly="!editing"
                fieldname="data.html"
                aria-label="Erfassen"
                label=""
              />
            </api-form>
          </div>
        </template>
      </template>
      <p v-else>
        {{ $tc('components.story.storyDay.noStory') }}
      </p>
    </v-expansion-panel-content>
  </v-expansion-panel>
</template>
<script>
import { sortBy } from 'lodash'
import ApiForm from '@/components/form/api/ApiForm.vue'
import { dateLong } from '@/common/helpers/dateHelperUTCFormatted.js'
import CategoryChip from '@/components/generic/CategoryChip.vue'

export default {
  name: 'StoryDay',
  components: {
    CategoryChip,
    ApiForm,
  },
  props: {
    day: { type: Object, required: true },
    editing: { type: Boolean, default: false },
  },
  computed: {
    loading() {
      return (
        this.day.scheduleEntries()._meta.loading ||
        this.sortedScheduleEntries.some(
          (entry) => entry.activity().contentNodes()._meta.loading
        )
      )
    },
    sortedScheduleEntries() {
      return sortBy(
        this.day.scheduleEntries().items,
        (scheduleEntry) => scheduleEntry.start
      )
    },
    entries() {
      return this.sortedScheduleEntries.map((scheduleEntry) => ({
        scheduleEntry: scheduleEntry,
        storyChapters: (
          scheduleEntry.activity().contentNodes() || { items: [] }
        ).items.filter((contentNode) => contentNode.contentTypeName === 'Storycontext'),
      }))
    },
    entriesWithStory() {
      return this.entries.filter(({ storyChapters }) => storyChapters.length)
    },
  },
  async mounted() {
    // refresh to get new schedule entries
    await this.day.scheduleEntries().$reload()
    // refresh individual schedule entries to get new story content nodes
    await Promise.all(
      this.day.scheduleEntries().items.map((entry) =>
        entry
          .activity()
          .contentNodes()
          ._meta.load.then((contentNodes) => this.api.reload(contentNodes))
      )
    )
  },
  methods: {
    dateLong,
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
