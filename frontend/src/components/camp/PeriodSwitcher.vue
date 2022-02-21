<template>
  <v-toolbar-items v-if="period().camp().periods().items.length > 1">
    <v-menu offset-y>
      <template #activator="{ on, attrs, value }">
        <v-btn
          text large
          class="justify-start pl-0"
          height="auto" block
          v-bind="attrs"
          v-on="on">
          <h1 class="text-h6">
            {{ period().description }}
          </h1>
          <v-icon v-if="value" right>mdi-menu-up</v-icon>
          <v-icon v-else right>mdi-menu-down</v-icon>
        </v-btn>
      </template>
      <v-list>
        <v-list-item v-for="item in period().camp().periods().items" :key="item._meta.self" :to="periodRoute(item)">
          {{ item.description }}
        </v-list-item>
      </v-list>
    </v-menu>
  </v-toolbar-items>
  <v-toolbar-title v-else>
    <h1 class="text-h6">
      {{ period().description }}
    </h1>
  </v-toolbar-title>
</template>
<script>
import { periodRoute } from '@/router.js'

export default {
  name: 'PeriodSwitcher',
  props: {
    period: {
      type: Function,
      required: true
    }
  },
  methods: {
    periodRoute
  }
}
</script>
<style lang="scss" scoped>

</style>
