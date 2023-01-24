<template>
  <!-- edit mode: normal div with drag & drop -->
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
          class="e-picasso-entry__quickedit rounded-sm pr-0"
          @click.prevent="on.click"
          @mousedown.stop=""
          @mouseup.stop=""
        >
          <v-icon x-small color="white">mdi-pencil</v-icon>
        </v-btn>
      </template>
    </dialog-activity-edit>

    <h4 class="e-picasso-entry__title">
      {{ activityName }}
    </h4>

    <template v-if="location">
      <br v-if="!isTinyDuration" />
      <small
        ><span class="d-sr-only">{{
          $tc('components.program.picasso.picassoEntry.location')
        }}</span>
        {{ location }}
      </small>
    </template>
    <template v-if="campCollaborationText">
      <template v-if="clientWidth < 200">
        <span v-if="!isLongDuration && location && campCollaborations"> &middot; </span>
        <br v-if="isLongDuration" />
        <small
          ><span class="d-sr-only">{{
            $tc('components.program.picasso.picassoEntry.responsible')
          }}</span
          >{{ campCollaborationText }}</small
        >
      </template>
      <AvatarRow v-else :camp-collaborations="campCollaborations" />
    </template>

    <!-- resize handle -->
    <div
      v-if="editable"
      class="e-picasso-entry__drag-bottom"
      @mousedown.stop="$emit('startResize')"
    />
  </div>

  <!-- readonly mode: component is a HTML link -->
  <router-link
    v-else
    class="e-picasso-entry e-piasso-entry--link"
    :to="scheduleEntryRoute"
    :style="colorStyles"
  >
    <h4 class="e-picasso-entry__title">
      {{ activityName }}
    </h4>
    <template v-if="location">
      <br v-if="!isTinyDuration" />
      <small
        ><span class="d-sr-only">{{
          $tc('components.program.picasso.picassoEntry.location')
        }}</span>
        {{ location }}
      </small>
    </template>
    <template v-if="campCollaborationText">
      <template v-if="clientWidth < 200">
        <span v-if="!isLongDuration && location && campCollaborations"> &middot; </span>
        <br v-if="isLongDuration" />
        <small
          ><span class="d-sr-only">{{
            $tc('components.program.picasso.picassoEntry.responsible')
          }}</span
          >{{ campCollaborationText }}</small
        >
      </template>
      <AvatarRow v-else :camp-collaborations="campCollaborations" />
    </template>
  </router-link>
</template>
<script>
import { ref, toRefs } from 'vue'
import DialogActivityEdit from '../DialogActivityEdit.vue'
import campCollaborationDisplayName from '@/common/helpers/campCollaborationDisplayName.js'
import { scheduleEntryRoute } from '@/router.js'
import { contrastColor } from '@/common/helpers/colors.js'
import { useClickDetector } from './useClickDetector.js'
import AvatarRow from '@/components/generic/AvatarRow.vue'
import { ONE_MINUTE } from '@/helpers/vCalendarDragAndDrop.js'

export default {
  name: 'PicassoEntry',
  components: { AvatarRow, DialogActivityEdit },
  props: {
    editable: { type: Boolean, required: true },
    scheduleEntry: { type: Object, required: true },
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
  data: () => ({
    clientWidth: 0,
    scrollHeight: 0,
    clientHeight: 0,
  }),
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
    campCollaborations() {
      return this.activityResponsibles.map((item) => item.campCollaboration())
    },
    campCollaborationText() {
      if (this.campCollaborations.length === 0) return ''
      return this.campCollaborations
        .map((item) => campCollaborationDisplayName(item))
        .join(', ')
    },
    duration() {
      return this.scheduleEntry.endTimestamp - this.scheduleEntry.startTimestamp
    },
    isTinyDuration() {
      return this.duration <= 40 * ONE_MINUTE
    },
    isLongDuration() {
      return this.scrollHeight <= this.clientHeight
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
  mounted() {
    this.clientWidth = this.$el.clientWidth
    this.clientHeight = this.$el.clientHeight
    this.scrollHeight = this.$el.scrollHeight
    window.addEventListener('resize', this.onResize)
  },
  destroyed() {
    window.removeEventListener('resize', this.onResize)
  },
  methods: {
    onResize() {
      this.clientWidth = this.$el.clientWidth
      this.clientHeight = this.$el.clientHeight
      this.scrollHeight = this.$el.scrollHeight
      this.$nextTick(() => {
        this.clientHeight = this.$el.clientHeight
        this.scrollHeight = this.$el.scrollHeight
      })
    },
  },
}
</script>

<style scoped lang="scss">
.e-picasso-entry {
  display: block;
  height: 100%;
  padding: 1px;
  @media #{map-get($display-breakpoints, 'sm-and-up')} {
    padding: 1px 2px;
  }
  @media #{map-get($display-breakpoints, 'md-and-up')} {
    padding: 2px 3px;
    line-height: normal;
  }
  overflow: hidden;
  overflow-wrap: break-word;
  position: relative;
  border-width: 0;
  border-color: transparent;
  border-style: solid;
  border-radius: 3px;
  outline: 1px solid white;

  &:hover {
    z-index: 999;
  }
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
  position: absolute;
  right: 0;
  top: 0;
  max-height: calc(100% + 4px);
  padding: 0 !important;
  min-width: 20px !important;
  border-radius: 0 3px 0 4px !important;
  z-index: 100;
}

.e-picasso-entry:hover .e-picasso-entry__quickedit {
  display: inline-block;
  background-color: rgba(0, 0, 0, 0.75);
  &:hover {
    background-color: black;
  }
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

:deep .e-avatarrow {
  position: absolute;
  bottom: 2px;
  right: 2px;
  max-height: calc(100% - 4px);
}
</style>
