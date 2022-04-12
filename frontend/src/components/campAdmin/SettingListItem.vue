<template>
  <v-list-item link :to="to" @click="$emit('click',$event)">
    <v-list-item-avatar>
      <v-icon>{{ icon }}</v-icon>
    </v-list-item-avatar>
    <v-list-item-content>
      <v-list-item-title>{{ title }}</v-list-item-title>
    </v-list-item-content>
    <v-list-item-action>
      <v-icon>mdi-chevron-right</v-icon>
    </v-list-item-action>
  </v-list-item>
</template>

<script>
export default {
  name: 'SettingListItem',
  props: {
    title: { type: String, required: true },
    icon: { type: String, required: true },
    to: {
      type: Object,
      default: null
    }
  }
}
</script>

<style scoped>

</style>
