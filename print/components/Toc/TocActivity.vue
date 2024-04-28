<template>
  <li>
    <generic-error-message v-if="error" :error="error" />
    <div v-else class="toc-element toc-element-level-1">
      <a :href="`#content_${index}_scheduleEntry_${scheduleEntry.id}`"
        >{{ scheduleEntry.number }} {{ scheduleEntry.activity().title }}</a
      >
    </div>
  </li>
</template>

<script setup>
const props = defineProps({
  options: { type: Object, required: false, default: null },
  camp: { type: Object, required: true },
  index: { type: Number, required: true },
})

const { $api } = useNuxtApp()

const { data: scheduleEntry, error } = await useAsyncData(
  `TocActivity-${props.index}`,
  async () => {
    const [scheduleEntry] = await Promise.all([
      $api.get(props.options.scheduleEntry)._meta.load,
      $api.get(props.options.activity)._meta.load,
    ])

    return scheduleEntry
  }
)
</script>
