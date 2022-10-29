<template>
  <div class="avatarrow" :style="avatarrow">
    <div v-for="user in users" :key="user.id" class="avataritem">
      <UserAvatar :size="Number(size)" :user="user" />
    </div>
  </div>
</template>

<script>
export default {
  name: 'AvatarRow',
  props: {
    users: { type: Array, default: () => [] },
    size: { type: [Number, String], default: 20 },
  },
  computed: {
    maxWidth() {
      return (this.users?.length - 1) * (Number(this.size) * 0.25) + Number(this.size)
    },
    avatarrow() {
      return {
        'max-width': `${this.maxWidth}px`,
        'font-size': `${this.size}px`,
      }
    },
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
