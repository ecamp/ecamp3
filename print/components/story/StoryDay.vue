<template>
  <div class="tw-mb-4">
    <div class="tw-text-2xl tw-mb-2 tw-break-after-avoid">
      <h1>{{ $tc('entity.day.name') }} {{ day.number }} ({{ dateLong(day.start) }})</h1>
    </div>

    <template v-if="entriesWithStory.length">
      <template v-for="{ scheduleEntry, storyChapters } in entriesWithStory">
        <div v-for="chapter in storyChapters" :key="chapter._meta.uri" class="tw-mb-3">
          <h4 class="tw-text-lg tw-font-bold tw-break-after-avoid">
            <span class="d-inline-flex align-center">
              <span>{{ scheduleEntry.number }}</span>
              <category-label
                :category="scheduleEntry.activity().category()"
                class="tw-ml-1"
              />
            </span>

            <span>{{ scheduleEntry.activity().title }}</span>
            <template v-if="chapter.instanceName">
              - {{ chapter.instanceName }}
            </template>
          </h4>

          <rich-text :rich-text="chapter.data.text" />
        </div>
      </template>
    </template>
    <p v-else>
      {{ $tc('story.storyDay.noStory') }}
    </p>

    <hr />
  </div>
</template>

<script>
import CategoryLabel from '@/components/generic/CategoryLabel.vue'
import RichText from '@/components/generic/RichText.vue'
import { dateLong } from '@/../common/helpers/dateHelperUTCFormatted.js'

function isEmptyHtml(html) {
  if (html === null) {
    return true
  }

  return html.trim() === '' || html.trim() === '<p></p>'
}

export default {
  components: { CategoryLabel, RichText },
  props: {
    day: { type: Object, required: true },
    index: { type: Number, required: true },
    periodStoryChapters: { type: Array, required: true },
  },
  data() {
    return {}
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
        scheduleEntry,
        storyChapters: this.periodStoryChapters.filter(
          (contentNode) =>
            contentNode.root()._meta.self ===
              scheduleEntry.activity().rootContentNode()._meta.self &&
            !isEmptyHtml(contentNode.data.text)
        ),
      }))
    },
    entriesWithStory() {
      return this.entries.filter(({ storyChapters }) => storyChapters.length)
    },
  },
  methods: {
    dateLong,
  },
}
</script>
