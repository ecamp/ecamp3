<template>
  <v-app-bar app clipped-left color="blue-grey-darken-4" dark>
    <Logo text />

    <v-toolbar-items>
      <AppBarItem icon="mdi-tent" text="PBS CH 361-25" />
      <AppBarItem icon="mdi-view-dashboard" text="Programm" />
      <AppBarItem icon="mdi-clipboard-list-outline" text="Checkliste" />
      <AppBarItem icon="mdi-book-open-variant" text="Geschichte" />
      <AppBarItem icon="mdi-package-variant" text="Material" />
      <AppBarItem icon="mdi-cogs" text="Admin" />
    </v-toolbar-items>
    <v-spacer />
    <v-toolbar-items v-if="$vuetify.display.lgAndUp">
      <v-btn target="_blank" text>
        Help
        <span class="blue-grey--text"><v-icon right small>mdi-open-in-new</v-icon></span>
      </v-btn>
    </v-toolbar-items>
  </v-app-bar>
</template>

<script setup>
  //
import AppBarItem from '@/components/AppBarItem.vue'
</script>

<style scoped lang="scss">
@use "vuetify/settings";
@use "sass:map";

@media #{map.get(settings.$display-breakpoints, 'sm-and-down')} {
  .sr-only-sm-and-down {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    clip-path: inset(50%);
    border: 0;
  }
}
</style>
