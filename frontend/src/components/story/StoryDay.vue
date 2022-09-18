<template>
  <div class="e-story-day contents">
    <h3 class="body-2 grey--text text--darken-2 e-story-day-title">
      {{ dateLong(day.start) }}
    </h3>
    <template v-if="loading">
      <v-skeleton-loader class="mt-2 mt-sm-3" type="list-item-three-line" />
    </template>
    <template v-else-if="entriesWithStory.length">
      <template v-for="{ scheduleEntry, storyChapters } in entriesWithStory">
        <div v-for="chapter in storyChapters" :key="chapter._meta.uri">
          <h4 class="mt-2 mt-sm-3">
            <span class="d-inline-flex align-center">
              <span class="tabular-nums">{{ scheduleEntry.number }}</span>
              <CategoryChip :category="scheduleEntry.activity().category" class="mx-1" />
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
          <api-form v-show="editing" :entity="chapter">
            <api-textarea
              :outlined="false"
              :solo="false"
              auto-grow
              dense
              fieldname="data.text"
              aria-label="Erfassen"
              label=""
            />
          </api-form>
          <tiptap-editor
            v-show="!editing"
            :class="{ readonly: !editing }"
            :editable="false"
            :value="chapter.data.text"
            class="v-input mb-1"
          />
        </div>
      </template>
    </template>
    <p v-else>
      {{ $tc('story.storyDay.noStory') }}
    </p>
  </div>
</template>
<script>
import { sortBy } from 'lodash'
import ApiForm from '@/components/form/api/ApiForm.vue'
import ApiTextarea from '@/components/form/api/ApiTextarea.vue'
import TiptapEditor from '@/components/form/tiptap/TiptapEditor.vue'
import { dateLong } from '@/common/helpers/dateHelperUTCFormatted.js'
import CategoryChip from '@/components/story/CategoryChip.vue'

export default {
  name: 'StoryDay',
  components: {
    CategoryChip,
    TiptapEditor,
    ApiForm,
    ApiTextarea,
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
  mounted() {
    this.day
      .scheduleEntries()
      .items.forEach((entry) => this.api.reload(entry.activity().contentNodes()))
  },
  methods: {
    dateLong,
  },
}
</script>

<style scoped>
.readonly ::v-deep .ProseMirror-trailingBreak {
  display: none;
}

.e-story-day + .e-story-day .e-story-day-title {
  border-top: 1px solid #eee;
  padding-top: 5px;
}

::v-deep .v-skeleton-loader__list-item-three-line {
  padding: 0;
  height: auto;
}
</style>
