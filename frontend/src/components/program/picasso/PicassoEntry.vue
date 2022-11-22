<template>
  <div
    v-if="editable"
    class="e-picasso-entry e-picasso-entry--editable"
    :class="{
      'e-picasso-entry--temporary elevation-4 v-event--temporary': scheduleEntry.tmpEvent,
    }"
    :style="colorStyles"
    v-on="listeners"
  >
    <!-- edit button & dialog -->
    <dialog-activity-edit
      v-if="editable && !scheduleEntry.tmpEvent"
      ref="editDialog"
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
    :style="colorStyles"
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
import { ref, toRefs } from 'vue'
import DialogActivityEdit from '../DialogActivityEdit.vue'
import campCollaborationDisplayName from '@/common/helpers/campCollaborationDisplayName.js'
import { scheduleEntryRoute } from '../../../router.js'
import { contrastColor } from '../../../../../common/helpers/colors.js'
import { useClickDetector } from './useClickDetector.js'

export default {
  name: 'PicassoEntry',
  components: { DialogActivityEdit },
  props: {
    editable: { type: Boolean, required: true },
    scheduleEntry: { type: Object, required: true },
    timed: { type: Boolean, required: true },
  },
  emits: ['startResize', 'finishEdit'],
  setup(props) {
    const { editable } = toRefs(props)
    const editDialog = ref(null)

    // open edit dialog when clicking, but only if it wasn't a drag motion
    const { listeners } = useClickDetector(editable, 5, () => {
      editDialog.value.open()
    })

    return { listeners, editDialog }
  },
  computed: {
    activity() {
      return this.scheduleEntry.activity()
    },
    category() {
      return this.activity.category()
    },
    activityName() {
      if (this.scheduleEntry.tmpEvent) return this.$tc('entity.activity.new')

      if (this.activityLoading) return this.$tc('global.loading')

      return (
        (this.scheduleEntry.number ? this.scheduleEntry.number + ' ' : '') +
        (this.category.short ? this.category.short + ': ' : '') +
        this.activity.title
      )
    },
    activityResponsibles() {
      if (this.scheduleEntry.tmpEvent) return []
      return this.activity.activityResponsibles().items
    },
    campCollaboration() {
      if (this.activityResponsibles.length === 0) return ''
      return `[${this.activityResponsibles
        .map((item) => campCollaborationDisplayName(item.campCollaboration()))
        .join(', ')}]`
    },
    location() {
      if (this.scheduleEntry.tmpEvent) return ''
      return this.activity.location
    },
    activityLoading() {
      return (
        !this.scheduleEntry.tmpEvent &&
        (this.activity._meta.loading || this.category._meta.loading)
      )
    },
    activityColor() {
      if (this.scheduleEntry.tmpEvent) return '#9e9e9e'
      if (this.category._meta.loading) return '#bdbdbd'

      return this.category.color
    },
    activityTextColor() {
      if (this.scheduleEntry.tmpEvent) return '#000'
      if (this.category._meta.loading) return '#000'

      return contrastColor(this.category.color)
    },
    colorStyles() {
      return {
        color: this.activityTextColor,
        backgroundColor: this.activityColor,
      }
    },
    scheduleEntryRoute() {
      if (this.scheduleEntry.tmpEvent) return {}
      return scheduleEntryRoute(this.scheduleEntry)
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

  &:hover {
    text-decoration-color: currentColor;
  }
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
  font-size: 11px;
}

@media #{map-get($display-breakpoints, 'sm-and-up')} {
  .e-picasso-entry:hover .e-picasso-entry__drag-bottom::after {
    display: block; // resize handle not visible on mobile
  }
}
</style>
