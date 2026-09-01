<template>
  <span
    class="ec-comment-count-icon"
    :class="{ 'ec-comment-count-icon--filled': filled }"
    :style="{ fontSize: size }"
  >
    <v-icon :icon="filled ? 'mdi-comment' : 'mdi-comment-outline'" size="1.2em" />
    <span v-if="count" class="ec-comment-count-icon__count">{{ count }}</span>
  </span>
</template>

<script>
export default {
  name: 'CommentCountIcon',
  props: {
    count: { type: Number, default: 0 },
    filled: { type: Boolean, default: false },
    size: { type: String, default: '22px' },
  },
}
</script>

<style scoped>
.ec-comment-count-icon {
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 1em;
  height: 1em;
  vertical-align: -0.28em;
  line-height: 1;
}

.ec-comment-count-icon :deep(.v-icon) {
  opacity: 0.55;
}

.ec-comment-count-icon--filled :deep(.v-icon) {
  opacity: 1;
}

.ec-comment-count-icon--filled .ec-comment-count-icon__count {
  color: rgb(var(--v-theme-surface));
}

.ec-comment-count-icon__count {
  position: absolute;
  inset: 0 0 0.4em;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.46em;
  font-weight: 700;
  line-height: 1;
}
</style>
