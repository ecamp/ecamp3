<template>
  <div v-if="$vuetify.breakpoint.mdAndUp" class="e-pages-config__wrapper">
    <div
      class="e-pages-config"
      :class="{
        'e-pages-config--multiple': multiple,
        'e-pages-config--landscape': landscape,
        'e-pages-config--template': template,
      }"
    >
      <ButtonDelete
        v-if="!template"
        class="e-pages-config__delete px-2 invisible"
        tabindex="-1"
        icon-only
      />
      <div class="e-pages-config__inner" v-bind="$attrs" v-on="$listeners">
        <div class="e-pages-config__scroller">
          <v-icon v-if="template" x-large class="ma-auto">mdi-plus</v-icon>
          <h3
            class="e-pages-config__title title py-4 white sticky"
            :class="{ handle: !template }"
          >
            <TextAlignBaseline v-if="!template"
              ><v-icon>mdi-drag</v-icon></TextAlignBaseline
            >
            <span class="flex-grow-1"> {{ title }} </span>
            <TextAlignBaseline v-if="!template"
              ><v-icon>mdi-drag</v-icon></TextAlignBaseline
            >
          </h3>
          <div class="mx-4">
            <slot />
          </div>
        </div>
      </div>
      <ButtonDelete
        v-if="!template"
        :text="false"
        icon-only
        color="grey"
        dark
        class="e-pages-config__delete px-2"
        :class="{ 'rounded-l-0': !landscape, 'rounded-t-0': landscape }"
        @click="$emit('remove')"
      />
    </div>
  </div>
  <v-list-item v-else-if="template" class="py-2 px-0" v-bind="$attrs" v-on="$listeners">
    <v-list-item-icon class="mr-0 my-0 px-4 py-2 align-self-baseline">
      <TextAlignBaseline><v-icon>mdi-plus</v-icon></TextAlignBaseline>
    </v-list-item-icon>
    <v-list-item-content class="align-self-baseline">
      <v-list-item-title
        ><h3>{{ title }}</h3></v-list-item-title
      >
    </v-list-item-content>
  </v-list-item>
  <div v-else>
    <v-list-item class="py-2 px-0">
      <v-list-item-icon v-if="!template" class="mr-0 my-0 px-4 py-2 align-self-baseline">
        <TextAlignBaseline><v-icon class="handle">mdi-drag</v-icon></TextAlignBaseline>
      </v-list-item-icon>
      <v-list-item-content class="align-self-baseline">
        <v-list-item-title class="mb-2"
          ><h3>{{ title }}</h3></v-list-item-title
        >
        <slot />
      </v-list-item-content>
      <v-list-item-action v-if="!template" class="align-self-baseline my-0">
        <TextAlignBaseline
          ><v-btn icon class="mr-6" @click="$emit('remove')"
            ><v-icon color="red">mdi-delete</v-icon></v-btn
          ></TextAlignBaseline
        >
      </v-list-item-action>
    </v-list-item>
    <v-divider class="mx-4" />
  </div>
</template>

<script>
import TextAlignBaseline from '@/components/layout/TextAlignBaseline.vue'
import ButtonDelete from '@/components/buttons/ButtonDelete.vue'

export default {
  name: 'PagesConfig',
  components: { ButtonDelete, TextAlignBaseline },
  props: {
    multiple: Boolean,
    landscape: Boolean,
    template: Boolean,
    title: { type: String, required: true },
  },
}
</script>

<style scoped lang="scss">
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

.e-pages-config--landscape {
  --aspect: 297 / 210;
  flex-direction: column;
  align-items: flex-end;
}

.e-pages-config--template {
  opacity: 0.7;

  &:hover {
    opacity: 1;
  }
}

.e-pages-config--multiple .e-pages-config__inner {
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
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
  }

  &:before {
    rotate: -2deg;
    transform-origin: 90% center;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
  }
}

.e-pages-config__inner {
  position: relative;
  aspect-ratio: var(--aspect);
  background: white;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
}

.e-pages-config__scroller {
  display: flex;
  flex-direction: column;
  overflow: hidden auto;
  aspect-ratio: var(--aspect);
}

.e-pages-config__title {
  text-align: center;
  position: sticky;
  top: 0;
  z-index: 1;
  display: flex;
}

.e-pages-config:not(.e-pages-config--template) .e-pages-config__title {
  .v-icon {
    opacity: 0;
  }

  &:hover {
    cursor: move; /* fallback if grab cursor is unsupported */
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

.e-pages-config:hover .e-pages-config__delete {
  opacity: 1;
}

.e-pages-config__delete:hover {
  color: map-get($red, 'base') !important;
}
</style>
