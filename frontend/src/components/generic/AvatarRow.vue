<template>
  <div class="e-avatarrow" :class="{ 'e-avatarrow--narrow': narrow }" :style="styles">
    <div
      v-for="campCollaboration in campCollaborations"
      :key="campCollaboration && campCollaboration._meta.self"
      class="e-avataritem"
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
      return Math.min(Number(this.maxSize), this.maxHeight)
    },
    maxWidth() {
      return this.campCollaborations?.length * (this.size * 0.5) + this.size
    },
    narrow() {
      return this.campCollaborations?.length > 3
    },
    styles() {
      return {
        'max-width': `${this.maxWidth}px`,
        'font-size': `${this.size}px`,
      }
    },
  },
  mounted() {
    this.maxHeight = this.$el.getBoundingClientRect().height
  },
}
</script>

<style scoped lang="scss">
.e-avatarrow {
  display: flex;
  flex-direction: row-reverse;
  gap: 0.8em;
  @media #{map-get($display-breakpoints, 'md-and-up')} {
    gap: 1.1em;
  }

  padding-left: 0.5em;
  padding-right: 0.5em;
  transition: gap 0.25s ease;
}

.e-avatarrow--narrow {
  @media #{map-get($display-breakpoints, 'md-and-up')} {
    gap: 0.8em;
  }
}

.e-avatarrow:hover {
  gap: 1.1em;
}

.e-avataritem {
  display: grid;
  width: 0;
  place-content: center;
  text-decoration: none;
}
</style>
