<template>
  <div class="tw-break-after-page">
    <h1
      :id="`content_${index}_period_${period.id}`"
      class="tw-text-center tw-font-semibold tw-mb-6"
    >
      {{ $t('print.activityList.title') }}:
      {{ period.description }}
    </h1>

    <generic-error-message v-if="error" :error="error" />

    <activity-list-schedule-entry
      v-for="scheduleEntry in data.scheduleEntries"
      :key="scheduleEntry.id"
      :schedule-entry="scheduleEntry"
      :content-types="data.contentTypes"
      :content-nodes="data.contentNodes"
      :index="index"
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
  contentTypeNames: {
    type: Array,
    required: true,
  },
  index: { type: Number, required: true },
})

const { $api } = useNuxtApp()

const { data, error } = await useAsyncData(
  `ActivityListPeriod-${props.period._meta.self}`,
  async () => {
    const allContentTypes = (await $api.get().contentTypes().$loadItems()).items

    const contentTypes = props.contentTypeNames.map((contentTypeName) =>
      allContentTypes.find((contentType) => contentType.name === contentTypeName)
    )

    const contentNodePromises = contentTypes.map((contentType) =>
      $api
        .get()
        .contentNodes({
          period: props.period._meta.self,
          contentType: contentType._meta.self,
        })
        .$loadItems()
    )
    const contentNodes = await Promise.all(contentNodePromises)

    const [scheduleEntries] = await Promise.all([
      props.period.scheduleEntries().$loadItems(),
    ])

    return {
      scheduleEntries: scheduleEntries.items,
      contentNodes,
      contentTypes,
    }
  }
)
</script>
