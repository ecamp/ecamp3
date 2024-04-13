<template>
  <li>
    <generic-error-message v-if="error" :error="error" />
    <div v-else class="toc-element toc-element-level-2">
      <a :href="`#content_${index}_scheduleEntry_${scheduleEntry.id}`"
        >{{ scheduleEntry.number }} {{ scheduleEntry.activity().title }}</a
      >
    </div>
  </li>
</template>

<script setup>
const props = defineProps({
  index: { type: Number, required: true },
  scheduleEntry: { type: Object, required: true },
})

const { error } = await useAsyncData(
  `TocScheduleEntry-${props.scheduleEntry._meta.self}`,
  async () => {
    await Promise.all([
      props.scheduleEntry._meta.load,
      props.scheduleEntry.activity()._meta.load,
    ])
  }
)
</script>
