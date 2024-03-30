<template>
  <v-list-item link :to="to" :href="href" v-on="$listeners">
    <slot name="pre">
      <v-list-item-icon v-if="!hideAvatar">
        <v-icon v-if="icon">{{ icon }}</v-icon>
      </v-list-item-icon>
    </slot>
    <v-list-item-content>
      <v-list-item-title>{{ title }}</v-list-item-title>
      <v-list-item-subtitle v-if="subtitle">{{ subtitle }}</v-list-item-subtitle>
    </v-list-item-content>
    <v-list-item-icon v-if="!hideChevron">
      <v-icon style="opacity: 0.6">mdi-chevron-right</v-icon>
    </v-list-item-icon>
  </v-list-item>
</template>

<script>
export default {
  name: 'SidebarListItem',
  props: {
    title: { type: String, required: true },
    subtitle: { type: String, required: false, default: '' },
    icon: { type: String, required: false, default: '' },
    hideAvatar: { type: Boolean, default: false },
    hideChevron: { type: Boolean, default: false },
    to: { type: [String, Object], default: null },
    href: { type: [String, Object], default: null },
  },
}
</script>

<style scoped></style>
