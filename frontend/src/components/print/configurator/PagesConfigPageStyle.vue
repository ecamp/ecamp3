<template>
  <div class="e-pages-config__wrapper">
    <div
      class="e-pages-config"
      :class="{
        'e-pages-config--multiple': multiple,
        'e-pages-config--landscape': landscape,
        'e-pages-config--portrait': !landscape,
        'e-pages-config--button-variant': buttonVariant,
      }"
    >
      <ButtonDelete
        v-if="!buttonVariant"
        class="e-pages-config__delete px-2 invisible"
        tabindex="-1"
        icon-only
      />
      <div class="e-pages-config__inner e-pages-config__paper" v-bind="$attrs">
        <v-btn
          v-if="canRotate"
          class="e-pages-config__rotate"
          :icon="
            landscape
              ? 'mdi-rotate-left-variant'
              : 'mdi-rotate-right-variant mdi-rotate-90'
          "
          :aria-label="
            $t(
              landscape
                ? 'components.print.config.picassoConfig.portrait'
                : 'components.print.config.picassoConfig.landscape'
            )
          "
          color="transparent"
          variant="flat"
          :class="{ 'rounded-s-0': !landscape, 'rounded-b-0': landscape }"
          @click="$emit('rotate')"
        />
        <div class="e-pages-config__scroller">
          <v-icon v-if="buttonVariant" class="ma-auto" size="x-large">mdi-plus</v-icon>
          <h3
            class="e-pages-config__title text-h6 py-4 bg-white sticky"
            :class="{ handle: !buttonVariant }"
          >
            <TextAlignBaseline v-if="!buttonVariant"
              ><v-icon>mdi-drag</v-icon></TextAlignBaseline
            >
            <span class="flex-grow-1"> {{ title }} </span>
            <TextAlignBaseline v-if="!buttonVariant"
              ><v-icon>mdi-drag</v-icon></TextAlignBaseline
            >
          </h3>
          <slot />
        </div>
      </div>
      <ButtonDelete
        v-if="!buttonVariant"
        :text="false"
        icon-only
        color="transparent"
        class="e-pages-config__delete px-2"
        variant="flat"
        :class="{ 'rounded-s-0': !landscape, 'rounded-t-0': landscape }"
        @click="$emit('remove')"
      />
    </div>
  </div>
</template>

<script>
import TextAlignBaseline from '@/components/layout/TextAlignBaseline.vue'
import ButtonDelete from '@/components/buttons/ButtonDelete.vue'

export default {
  name: 'PagesConfigPageStyle',
  components: { ButtonDelete, TextAlignBaseline },
  inheritAttrs: false,
  props: {
    multiple: Boolean,
    landscape: Boolean,
    canRotate: Boolean,
    buttonVariant: Boolean,
    title: { type: String, required: true },
  },
  emits: ['remove', 'rotate'],
}
</script>

<style scoped lang="scss">
@use 'vuetify/settings';
@use 'sass:map';

.e-pages-config__wrapper {
  display: flow-root;
  aspect-ratio: 1 / 1;
}

.e-pages-config {
  aspect-ratio: 1 / 1;
  --aspect: 210 / 297;
  position: relative;
  z-index: 1;
  display: flex;
  justify-content: center;
}

.e-pages-config--multiple .e-pages-config__paper {
  position: relative;

  &:before,
  &:after {
    content: '';
    aspect-ratio: var(--aspect);
    background: white;
    inset: 0;
    position: absolute;
    z-index: -1;
  }

  &:after {
    rotate: 3deg;
    transform-origin: 70% 70%;
    box-shadow:
      0 1px 3px rgba(0, 0, 0, 0.12),
      0 1px 2px rgba(0, 0, 0, 0.24);
  }

  &:before {
    rotate: -2deg;
    transform-origin: 90% center;
    box-shadow:
      0 1px 3px rgba(0, 0, 0, 0.12),
      0 1px 2px rgba(0, 0, 0, 0.24);
  }
}

.e-pages-config__inner {
  position: relative;
  aspect-ratio: var(--aspect);
}

.e-pages-config--landscape .e-pages-config__inner {
  width: 100%;
}

.e-pages-config--portrait .e-pages-config__inner {
  height: 100%;
}

.e-pages-config__scroller {
  display: flex;
  flex-direction: column;
  overflow: hidden auto;
  aspect-ratio: var(--aspect);
  max-height: 100%;
}

.e-pages-config__title {
  text-align: center;
  position: sticky;
  top: 0;
  z-index: 1;
  display: flex;
}

.e-pages-config__rotate {
  position: absolute;
  opacity: 0;
  top: 0;
  right: -48px;
  z-index: 2;
}

.e-pages-config--landscape .e-pages-config__rotate {
  top: -48px;
  right: 0;
}

.e-pages-config:not(.e-pages-config--button-variant) .e-pages-config__title {
  .v-icon {
    opacity: 0;
  }

  &:hover {
    cursor: move;
    cursor: grab;
    cursor: -moz-grab;
    cursor: -webkit-grab;

    .v-icon {
      opacity: 1;
    }
  }

  &:active {
    cursor: move;
    cursor: -moz-grabbing;
    cursor: -webkit-grabbing;
  }
}

.e-pages-config__delete {
  min-width: 48px !important;
  opacity: 0;
  align-self: end;
}

.e-pages-config__delete.invisible {
  visibility: hidden;
}

.e-pages-config:hover .e-pages-config__rotate,
.e-pages-config:hover .e-pages-config__delete,
.e-pages-config:focus-within .e-pages-config__delete {
  opacity: 1;
}

.e-pages-config__delete:hover {
  color: map.get(settings.$red, 'base') !important;
}

.e-pages-config .handle {
  cursor: grab;
}

.e-pages-config :deep(.v-list-item__prepend),
.e-pages-config :deep(.v-list-item__append) {
  align-self: baseline;
}

.e-pages-config :deep(.v-list-item__content) {
  overflow: visible;
}
</style>
