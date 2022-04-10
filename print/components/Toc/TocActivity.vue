<template>
  <li>
    <div class="toc-element toc-element-level-1">
      <a :href="`#content_${index}_scheduleEntry_${scheduleEntry.id}`"
        >{{ scheduleEntry.number }} {{ scheduleEntry.activity().title }}</a
      >
    </div>
  </li>
</template>

<script>
export default {
  name: 'TocActivity',
  props: {
    options: { type: Object, required: false, default: null },
    camp: { type: Object, required: true },
    index: { type: Number, required: true },
  },
  data() {
    return {
      scheduleEntry: null,
    }
  },
  async fetch() {
    const [scheduleEntry] = await Promise.all([
      this.$api.get(this.options.scheduleEntry)._meta.load,
      this.$api.get(this.options.activity)._meta.load,
    ])

    this.scheduleEntry = scheduleEntry
  },
}
</script>
