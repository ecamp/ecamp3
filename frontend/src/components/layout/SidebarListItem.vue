<template>
  <v-list-item link :to="to" @click="$emit('click', $event)">
    <slot name="pre">
      <v-list-item-avatar v-if="!hideAvatar">
        <v-icon v-if="icon">{{ icon }}</v-icon>
      </v-list-item-avatar>
    </slot>
    <v-list-item-content>
      <v-list-item-title>{{ title }}</v-list-item-title>
      <v-list-item-subtitle v-if="subtitle">{{ subtitle }}</v-list-item-subtitle>
    </v-list-item-content>
    <v-list-item-action v-if="!hideChevron">
      <v-icon>mdi-chevron-right</v-icon>
    </v-list-item-action>
  </v-list-item>
</template>

<script>
export default {
  name: 'SidebarListItem',
  props: {
    title: { type: String, required: true },
    subtitle: { type: String, required: false },
    icon: { type: String, required: false },
    hideAvatar: { type: Boolean, default: false },
    hideChevron: { type: Boolean, default: false },
    to: { type: [String, Object], default: null },
  },
}
</script>

<style scoped></style>
