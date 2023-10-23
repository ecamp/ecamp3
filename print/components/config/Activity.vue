<template>
  <div class="tw-break-after-page">
    <generic-error-message v-if="error" :error="error" />
    <schedule-entry v-else :schedule-entry="scheduleEntry" :index="index" />
  </div>
</template>

<script setup>
const props = defineProps({
  options: { type: Object, required: false, default: null },
  camp: { type: Object, required: true },
  config: { type: Object, required: true },
  index: { type: Number, required: true },
})

const { $api } = useNuxtApp()

const { data: scheduleEntry, error } = useAsyncData('Activity', async () => {
  if (props.options.scheduleEntry === null || props.options.activity === null) {
    throw new Error('No activity and scheduleEntry provided provided')
  }

  const [scheduleEntry] = await Promise.all([
    $api.get(props.options.scheduleEntry)._meta.load, // TODO prevent specifying arbitrary absolute URLs that the print container should fetch...
    $api.get(props.options.activity)._meta.load, // TODO prevent specifying arbitrary absolute URLs that the print container should fetch...
    $api.get().contentTypes().$loadItems(),

    // might not be needed for every activity, but safer to do eager loading instead of n+1 later on
    props.camp.materialLists().$loadItems(),
    props.camp.campCollaborations().$loadItems(),
  ])

  return markRaw(scheduleEntry)
})
</script>
