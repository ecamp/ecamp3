<template>
  <div ref="row" class="avatarrow" :style="avatarrow">
    <div
      v-for="campCollaboration in campCollaborations"
      :key="campCollaboration && campCollaboration._meta.self"
      class="avataritem"
    >
      <UserAvatar :size="size" :camp-collaboration="campCollaboration" />
    </div>
  </div>
</template>

<script>
import UserAvatar from '@/components/user/UserAvatar.vue'

export default {
  name: 'AvatarRow',
  components: { UserAvatar },
  props: {
    campCollaborations: { type: Array, default: () => [] },
    maxSize: { type: [Number, String], default: 20 },
  },
  data: () => ({
    maxHeight: 1000,
  }),
  computed: {
    size() {
      return Number(this.maxSize) < this.maxHeight ? Number(this.maxSize) : this.maxHeight
    },
    maxWidth() {
      return this.campCollaborations?.length * (this.size * 0.5) + this.size
    },
    avatarrow() {
      return {
        'max-width': `${this.maxWidth}px`,
        'font-size': `${this.size}px`,
      }
    },
  },
  mounted() {
    this.maxHeight = this.$el.clientHeight
  },
}
</script>

<style scoped lang="scss">
.avatarrow {
  display: flex;
  flex-direction: row-reverse;
  gap: 0.75em;
  padding-left: 0.5em;
  padding-right: 0.5em;
  transition: gap 0.25s ease;
}

@media #{map-get($display-breakpoints, 'md-and-up')} {
  .avatarrow {
    gap: 1.1em;
  }
}

.avatarrow:hover {
  gap: 1.1em;
}

.avataritem {
  display: grid;
  width: 0;
  place-content: center;
  text-decoration: none;
}
</style>
