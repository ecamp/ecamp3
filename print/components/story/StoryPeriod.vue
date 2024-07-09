<template>
  <div class="tw-break-after-page">
    <h1
      :id="`content_${index}_period_${period.id}`"
      class="tw-text-center tw-font-semibold tw-mb-6"
    >
      {{ $t('print.story.title') }}: {{ period.description }}
    </h1>

    <generic-error-message v-if="error" :error="error" />
    <story-day
      v-for="day in data.days"
      :key="day._meta.self"
      :index="index"
      :day="day"
      :period-story-chapters="data.periodStoryChapters"
    />
  </div>
</template>

<script setup>
const props = defineProps({
  camp: { type: Object, required: true },
  period: {
    type: Object,
    required: true,
  },
  index: { type: Number, required: true },
})

const { $api } = useNuxtApp()

const { data, error } = await useAsyncData(
  `StoryPeriod-${props.period._meta.self}`,
  async () => {
    const contentTypeStorycontext = (
      await $api.get().contentTypes().$loadItems()
    ).items.find((contentType) => contentType.name === 'Storycontext')

    const [periodStoryChapters] = await Promise.all([
      $api
        .get()
        .contentNodes({
          period: props.period._meta.self,
          contentType: contentTypeStorycontext._meta.self,
        })
        .$loadItems(),
      props.period.days().$loadItems(),
      props.period.scheduleEntries().$loadItems(),
      props.period.camp().categories().$loadItems(),
    ])

    return {
      days: props.period.days().items,
      periodStoryChapters: periodStoryChapters.items,
    }
  }
)
</script>
