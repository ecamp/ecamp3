<template>
  <v-card
    class="d-flex align-center elevation-4--light px-0 e-pages-config__paper e-pages-config--list-style"
    v-bind="$attrs"
    :class="{
      'e-pages-config--multiple': multiple,
      'e-pages-config--landscape': landscape,
      'e-pages-config--portrait': !landscape,
      'e-pages-config--button-variant': buttonVariant,
    }"
  >
    <div class="d-grid justify-items-center text-medium-emphasis">
      <v-btn
        v-if="!buttonVariant"
        size="x-small"
        icon="mdi-chevron-up"
        variant="text"
        :aria-label="$t('components.print.printConfigurator.moveUp')"
        @click="$emit('up')"
      />
      <v-btn
        :icon="buttonVariant ? 'mdi-plus' : 'mdi-drag'"
        size="small"
        class="handle"
        variant="plain"
        :aria-label="buttonVariant ? $t('global.button.add') : $t('global.button.move')"
      />
      <v-btn
        v-if="!buttonVariant"
        size="x-small"
        icon="mdi-chevron-down"
        variant="text"
        :aria-label="$t('components.print.printConfigurator.moveDown')"
        @click="$emit('down')"
      />
    </div>
    <div class="d-flex flex-column flex-grow-1 w-0">
      <h3 class="my-4">{{ title }}</h3>
      <slot />
    </div>
    <v-btn
      v-if="!buttonVariant"
      icon="mdi-delete"
      color="red"
      variant="text"
      class="mx-1"
      :aria-label="$t('global.button.remove')"
      @click="$emit('remove')"
    />
  </v-card>
</template>

<script>
export default {
  name: 'PagesConfigListStyle',
  inheritAttrs: false,
  props: {
    multiple: Boolean,
    landscape: Boolean,
    buttonVariant: Boolean,
    title: { type: String, required: true },
  },
  emits: ['remove', 'up', 'down'],
}
</script>

<style scoped lang="scss">
.e-pages-config--list-style .handle {
  cursor: grab;
}
</style>
