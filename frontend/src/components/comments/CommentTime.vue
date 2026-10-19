<template>
  <v-tooltip location="bottom" :text="long">
    <template #activator="{ props }">
      <time
        :datetime="value"
        data-testid="comment-time"
        v-bind="{ ...$attrs, ...props }"
        >{{ short }}</time
      >
    </template>
  </v-tooltip>
</template>

<script>
export default {
  name: 'CommentTime',
  inheritAttrs: false,
  props: {
    value: { type: String, required: true },
  },
  computed: {
    long() {
      return this.$date(this.value).format(this.$t('global.datetime.dateTimeLong'))
    },
    short() {
      const date = this.$date(this.value)
      const now = this.$date()
      const time = date.format(this.$t('global.datetime.hourLong'))
      if (date.isSame(now, 'day')) {
        return this.$t('components.comments.commentTime.today', { time })
      }
      if (date.isSame(now.subtract(1, 'day'), 'day')) {
        return this.$t('components.comments.commentTime.yesterday', { time })
      }
      if (date.isSame(now, 'year')) {
        return `${date.format(this.$t('global.datetime.dateShort'))} ${time}`
      }
      return `${date.format('L')} ${time}`
    },
  },
}
</script>
