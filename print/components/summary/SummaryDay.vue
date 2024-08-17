<template>
  <div class="tw-mb-4">
    <div
      class="tw-text-xl tw-mb-2 tw-break-after-avoid tw-border-b-2 tw-border-b-gray-400"
    >
      <h2 class="tw-py-1 tw-flex tw-gap-2 tw-justify-between">
        <span class="tw-font-semibold">
          {{ $t('entity.day.name') }} {{ day.number }}
        </span>
        <span class="tw-tabular-nums">{{ dateLong(day.start) }}</span>
      </h2>
    </div>

    <template v-if="entriesWithContentNode.length">
      <!-- eslint-disable-next-line vue/no-v-for-template-key -->
      <template v-for="{ scheduleEntry, contentNodes } in entriesWithContentNode">
        <div
          v-for="contentNode in contentNodes"
          :key="contentNode._meta.self"
          class="tw-mb-3"
        >
          <h4
            class="tw-text-lg tw-font-semibold tw-break-after-avoid tw-flex tw-align-baseline tw-gap-2"
          >
            <span class="tw-inline-flex tw-items-baseline tw-gap-2">
              <category-label :category="scheduleEntry.activity().category()" />
              <span class="tw-tabular-nums">{{ scheduleEntry.number }}</span>
            </span>

            <span>{{ scheduleEntry.activity().title }}</span>
            <template v-if="contentNode.instanceName">
              - {{ contentNode.instanceName }}
            </template>
          </h4>

          <rich-text :rich-text="contentNode.data.html" />
        </div>
      </template>
    </template>
    <p v-else>
      {{
        $t('components.summary.summaryDay.noContent', {
          contentType: $t(`contentNode.${camelCase(contentType)}.name`),
        })
      }}
    </p>
  </div>
</template>

<script>
import CategoryLabel from '@/components/generic/CategoryLabel.vue'
import RichText from '@/components/generic/RichText.vue'
import { dateHelperUTCFormatted } from '@/mixins/dateHelperUTCFormatted.js'
import camelCase from 'lodash/camelCase.js'

function isEmptyHtml(html) {
  if (html === null) {
    return true
  }

  return html.trim() === '' || html.trim() === '<p></p>'
}

export default {
  components: { CategoryLabel, RichText },
  mixins: [dateHelperUTCFormatted],
  props: {
    day: { type: Object, required: true },
    index: { type: Number, required: true },
    allContentNodes: { type: Array, required: true },
    contentType: { type: String, required: true },
    filter: { type: String, default: '' },
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
        contentNodes: this.allContentNodes.filter(
          (contentNode) =>
            contentNode.root()._meta.self ===
              scheduleEntry.activity().rootContentNode()._meta.self &&
            !isEmptyHtml(contentNode.data.html) &&
            (!this.filter ||
              contentNode.instanceName
                ?.toLowerCase()
                .includes(this.filter.toLowerCase()) ||
              this.$t(`contentNode.${camelCase(this.contentType)}.name`)
                .toLowerCase()
                .includes(this.filter.toLowerCase()))
        ),
      }))
    },
    entriesWithContentNode() {
      return this.entries.filter(({ contentNodes }) => contentNodes.length)
    },
  },
  methods: { camelCase },
}
</script>
