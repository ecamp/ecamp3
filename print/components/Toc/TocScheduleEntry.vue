<template>
  <li>
    <generic-error-message v-if="$fetchState.error" :error="$fetchState.error" />
    <div v-else class="toc-element toc-element-level-2">
      <a :href="`#content_${index}_scheduleEntry_${scheduleEntry.id}`"
        >{{ scheduleEntry.number }} {{ scheduleEntry.activity().title }}</a
      >
    </div>
  </li>
</template>

<script>
export default {
  name: 'TocScheduleEntry',
  props: {
    index: { type: Number, required: true },
    scheduleEntry: { type: Object, required: true },
  },
  data() {
    return {}
  },
  async fetch() {
    await Promise.all([
      this.scheduleEntry._meta.load,
      this.scheduleEntry.activity()._meta.load,
    ])
  },
}
</script>
