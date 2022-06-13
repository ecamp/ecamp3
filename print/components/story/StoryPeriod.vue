<template>
  <div class="tw-break-after-page">
    <h1
      :id="`content_${index}_period_${period.id}`"
      class="tw-text-2xl tw-font-bold tw-mb-6"
    >
      {{ $tc('print.story.title') }}: {{ $tc('entity.period.name') }}
      {{ period.description }}
    </h1>

    <story-day
      v-for="day in days"
      :key="day._meta.self"
      :index="index"
      :day="day"
      :period-story-chapters="periodStoryChapters"
    />
  </div>
</template>

<script>
export default {
  props: {
    camp: { type: Object, required: true },
    period: {
      type: Object,
      required: true,
    },
    index: { type: Number, required: true },
  },
  data() {
    return {
      days: null,
      periodStoryChapters: null,
    }
  },
  async fetch() {
    const contentTypeStorycontext = (
      await this.$api.get().contentTypes().$loadItems()
    ).items.find((contentType) => contentType.name === 'Storycontext')

    const [periodStoryChapters] = await Promise.all([
      this.$api
        .get()
        .contentNodes({
          period: this.period._meta.self,
          contentType: contentTypeStorycontext._meta.self,
        })
        .$loadItems(),
      this.period.days().$loadItems(),
      this.period.scheduleEntries().$loadItems(),
      this.$api
        .get()
        .contentNodes({ period: this.period._meta.self })
        .$loadItems(),
    ])

    this.days = this.period.days().items
    this.periodStoryChapters = periodStoryChapters.items
  },
}
</script>
