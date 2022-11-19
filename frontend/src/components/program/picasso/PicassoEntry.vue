<template>
  <div
    v-if="editable"
    class="e-picasso-entry e-picasso-entry--editable"
    :class="{
      'e-picasso-entry--temporary': scheduleEntry.tmpEvent,
    }"
  >
    <!-- edit button & dialog -->
    <dialog-activity-edit
      v-if="editable && !scheduleEntry.tmpEvent"
      :ref="`editDialog-${scheduleEntry.id}`"
      :schedule-entry="scheduleEntry"
      @activityUpdated="$emit('finishEdit')"
      @error="$emit('finishEdit')"
    >
      <template #activator="{ on }">
        <v-btn
          x-small
          text
          color="currentColor"
          class="e-picasso-entry__quickedit rounded-sm pr-0"
          @click.prevent="on.click"
          @mousedown.stop=""
          @mouseup.stop=""
        >
          <v-icon x-small color="currentColor">mdi-pencil</v-icon>
        </v-btn>
      </template>
    </dialog-activity-edit>

    <!-- readonly mode: complete div is a HTML link -->

    <!-- edit mode: normal div with drag & drop -->
    <template v-if="editable">
      <h4 class="e-picasso-entry__title">
        {{ activityName }}
      </h4>
      <template v-if="campCollaboration">
        <br />
        <small>{{ campCollaboration }}</small>
      </template>
      <template v-if="$vuetify.breakpoint.lgAndUp && location">
        <br />
        <small>{{ location }}</small>
      </template>

      <!-- resize handle -->
      <div
        v-if="editable && timed"
        class="e-picasso-entry__drag-bottom"
        @mousedown.stop="$emit('startResize')"
      />
    </template>
  </div>
  <router-link
    v-else
    class="e-picasso-entry e-piasso-entry--link"
    :to="scheduleEntryRoute"
  >
    <h4 class="e-picasso-entry__title">
      {{ activityName }}
    </h4>
    <template v-if="campCollaboration">
      <br />
      <small>{{ campCollaboration }}</small>
    </template>
    <template v-if="$vuetify.breakpoint.lgAndUp && location">
      <br />
      <small>{{ location }}</small>
    </template>
  </router-link>
</template>
<script>
import DialogActivityEdit from '../DialogActivityEdit.vue'
import campCollaborationDisplayName from '@/common/helpers/campCollaborationDisplayName.js'

export default {
  name: 'PicassoEntry',
  components: { DialogActivityEdit },
  props: {
    editable: { type: Boolean, required: true },
    scheduleEntry: { type: Object, required: true },
    timed: { type: Boolean, required: true },
    activityName: { type: String, required: true },
    scheduleEntryRoute: { type: Object, required: true },
  },
  emits: ['startResize', 'finishEdit'],
  computed: {
    activityResponsibles() {
      if (this.scheduleEntry.tmpEvent) return []
      return this.scheduleEntry.activity().activityResponsibles().items
    },
    campCollaboration() {
      if (this.activityResponsibles.length === 0) return ''
      return `[${this.activityResponsibles
        .map((item) => campCollaborationDisplayName(item.campCollaboration()))
        .join(', ')}]`
    },
    location() {
      if (this.scheduleEntry.tmpEvent) return ''
      return this.scheduleEntry.activity().location
    },
  },
}
</script>

<style scoped lang="scss">
.e-picasso-entry {
  display: block;
  height: 100%;
  padding: 2px;
  overflow: hidden;
  border-width: 1px;
  border-color: transparent;
  border-style: solid;
  border-radius: 3px;
}

@media #{map-get($display-breakpoints, 'sm-and-up')} {
  .e-picasso-entry {
    font-size: 12px;
  }
}

.e-picasso-entry--editable {
  cursor: move; /* fallback if grab cursor is unsupported */
  cursor: grab;
  cursor: -moz-grab;
  cursor: -webkit-grab;
  border-color: black;
  border-style: dashed;
  transition: transform 0.1s;

  &:hover {
    z-index: 999;
    transform: scale(
      1.02
    ); /* (150% zoom - Note: if the zoom is too large, it will go outside of the viewport) */
  }

  &:active {
    cursor: move;
    cursor: -moz-grabbing;
    cursor: -webkit-grabbing;
  }
}

.e-piasso-entry--link {
  color: currentColor;
  text-decoration-color: transparent;
  transition: text-decoration 0.1s ease;
}

.e-picasso-entry__quickedit {
  display: none;
  float: right;
  max-height: calc(100% + 4px);
  padding: 0 !important;
  min-width: 20px !important;
  margin: -2px -2px;
  border-radius: 0 3px 0 4px !important;
  z-index: 100;
}

.e-picasso-entry:hover .e-picasso-entry__quickedit {
  display: inline-block;
}

// event title text
.e-picasso-entry__title {
  display: inline;
  hyphens: auto;
  hyphenate-limit-chars: 6 3 3;
  hyphenate-limit-lines: 2;
  hyphenate-limit-last: always;
  hyphenate-limit-zone: 8%;
}

// resize handle
.e-picasso-entry__drag-bottom {
  position: absolute;
  left: 0;
  right: 0;
  bottom: 0;
  height: 8px;
  max-height: 40%;
  cursor: ns-resize;

  &::after {
    display: none;
    position: absolute;
    left: 50%;
    top: 0;
    height: 4px;
    border-top: 1px solid currentColor;
    border-bottom: 1px solid currentColor;
    width: 16px;
    margin-left: -8px;
    opacity: 0.8;
    content: '';
  }
}

.e-picasso-entry small {
  opacity: 0.7;
}

@media #{map-get($display-breakpoints, 'sm-and-up')} {
  .e-picasso-entry:hover .e-picasso-entry__drag-bottom::after {
    display: block; // resize handle not visible on mobile
  }
}
</style>
