<template>
  <!-- edit mode: normal div with drag & drop -->
  <div
    v-if="editable"
    class="e-picasso-entry"
    :class="{
      'e-picasso-entry--editable': isEditable,
      'e-picasso-entry--temporary elevation-4 v-event--temporary': scheduleEntry.tmpEvent,
      'e-picasso-entry--resizing': isResizing,
      'e-picasso-entry--moving': isMoving,
      'e-picasso-entry--filtered': !scheduleEntry.filterMatch,
    }"
    :style="colorStyles"
    v-on="listeners"
  >
    <!-- Copy -->
    <v-btn
      v-if="isEditable"
      x-small
      text
      class="e-picasso-entry__copy-url rounded-sm pr-0"
      @click.prevent="copyUrlToClipboard"
      @mousedown.stop=""
      @mouseup.stop=""
    >
      <v-icon x-small color="white">mdi-content-copy</v-icon>
    </v-btn>

    <CopyActivityInfoDialog v-if="isEditable" ref="copyInfoDialog" />

    <!-- edit button & dialog -->
    <DialogActivityEdit
      v-if="isEditable"
      ref="editDialog"
      :schedule-entry="scheduleEntry"
      @activity-updated="$emit('finish-edit')"
      @error="$emit('finish-edit')"
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
    </DialogActivityEdit>

    <h4 class="e-picasso-entry__title">
      {{ activityName }}
    </h4>

    <template v-if="location">
      <br v-if="!isTinyDuration" />
      <small class="e-picasso-entry__location">
        <span class="d-sr-only">{{
          $tc('components.program.picasso.picassoEntry.location')
        }}</span>
        {{ location }}
      </small>
    </template>
    <template v-if="campCollaborationText">
      <template v-if="clientWidth < 200">
        <span v-if="!isLongDuration && location && campCollaborations"> &middot; </span>
        <br v-if="isLongDuration" />
        <small class="e-picasso-entry__responsible">
          <span class="d-sr-only">{{
            $tc('components.program.picasso.picassoEntry.responsible')
          }}</span
          >{{ campCollaborationText }}</small
        >
      </template>
      <AvatarRow v-else :camp-collaborations="campCollaborations" />
    </template>

    <!-- resize handle -->
    <div
      v-if="!scheduleEntry.tmpEvent"
      class="e-picasso-entry__drag-bottom"
      @mousedown.stop="$emit('start-resize')"
    />

    <!-- Duration Display -->
    <div class="e-picasso-entry__duration">
      {{ durationText }}
    </div>
  </div>

  <!-- readonly mode: component is a HTML link -->
  <router-link
    v-else
    class="e-picasso-entry e-piasso-entry--link"
    :to="scheduleEntryRoute"
    :class="{
      'e-picasso-entry--filtered': !scheduleEntry.filterMatch,
    }"
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
        <small>
          <span class="d-sr-only">{{
            $tc('components.program.picasso.picassoEntry.responsible')
          }}</span
          >{{ campCollaborationText }}
        </small>
      </template>
      <AvatarRow v-else :camp-collaborations="campCollaborations" />
    </template>
  </router-link>
</template>
<script>
import { ref, toRefs, computed } from 'vue'
import DialogActivityEdit from '../DialogActivityEdit.vue'
import campCollaborationDisplayName from '@/common/helpers/campCollaborationDisplayName.js'
import { timestampToUtcString } from './dateHelperVCalendar.js'
import { dateHelperUTCFormatted } from '@/mixins/dateHelperUTCFormatted.js'
import { scheduleEntryRoute } from '@/router.js'
import router from '@/router.js'
import { contrastColor } from '@/common/helpers/colors.js'
import { useClickDetector } from './useClickDetector.js'
import AvatarRow from '@/components/generic/AvatarRow.vue'
import { ONE_MINUTE_IN_MILLISECONDS } from '@/helpers/vCalendarDragAndDrop.js'
import CopyActivityInfoDialog from '@/components/activity/CopyActivityInfoDialog.vue'

export default {
  name: 'PicassoEntry',
  components: { AvatarRow, DialogActivityEdit, CopyActivityInfoDialog },
  mixins: [dateHelperUTCFormatted],
  props: {
    editable: { type: Boolean, required: true },
    scheduleEntry: { type: Object, required: true },
  },
  emits: ['start-resize', 'finish-edit'],
  setup(props) {
    const { editable, scheduleEntry } = toRefs(props)
    const editDialog = ref(null)

    const enabled = computed(() => editable.value && scheduleEntry.value.filterMatch)

    // open edit dialog when clicking, but only if it wasn't a drag motion
    const { listeners } = useClickDetector(enabled, 5, () => {
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
    isEditable() {
      return this.scheduleEntry.filterMatch && !this.scheduleEntry.tmpEvent
    },
    isResizing() {
      return this.scheduleEntry.isResizing
    },
    isMoving() {
      return this.scheduleEntry.isMoving
    },
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
        .map((item) => campCollaborationDisplayName(item, this.$tc.bind(this)))
        .join(', ')
    },
    duration() {
      return this.scheduleEntry.endTimestamp - this.scheduleEntry.startTimestamp
    },
    isTinyDuration() {
      return this.duration <= 40 * ONE_MINUTE_IN_MILLISECONDS
    },
    isLongDuration() {
      return this.scrollHeight <= this.clientHeight
    },
    durationText() {
      const start = timestampToUtcString(this.scheduleEntry.startTimestamp)
      const end = timestampToUtcString(this.scheduleEntry.endTimestamp)
      if (this.dateShort(start) === this.dateShort(end)) {
        return this.rangeTime(start, end)
      } else {
        return this.rangeShort(start, end)
      }
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
    async copyUrlToClipboard() {
      try {
        const res = await navigator.permissions.query({ name: 'clipboard-read' })
        if (res.state === 'prompt') {
          this.$refs.copyInfoDialog.open()
        }
      } catch {
        console.warn('clipboard permission not requestable')
      }

      const url = window.location.origin + router.resolve(this.scheduleEntryRoute).href
      await navigator.clipboard.writeText(url)

      this.$toast.info(
        this.$tc('global.toast.copied', null, { source: this.activityName }),
        {
          timeout: 2000,
        }
      )
    },
  },
}
</script>

<style scoped lang="scss">
.e-picasso-entry {
  user-select: none;
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
}

.e-picasso-entry--filtered {
  opacity: 0.3;
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
    translate: -1.5px -1.5px;
    width: calc(100% + 3px);
    height: calc(100% + 3px);
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

.e-picasso-entry__copy-url {
  display: none;
  position: absolute;
  right: 20px;
  top: 0;
  max-height: calc(100% + 4px);
  padding: 0 !important;
  min-width: 20px !important;
  border-radius: 0 0px 0 4px !important;
  z-index: 100;
}

.e-picasso-entry:hover .e-picasso-entry__copy-url {
  display: inline-block;
  background-color: rgba(0, 0, 0, 0.75);
  &:hover {
    background-color: black;
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
  border-radius: 0 3px 0 0 !important;
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

.e-picasso-entry {
  .e-picasso-entry__title,
  .e-picasso-entry__location,
  .e-picasso-entry__responsible,
  .e-avatarrow {
    transition: opacity 0.25s ease-in-out;
  }
}
.e-picasso-entry--resizing,
.e-picasso-entry--moving,
.e-picasso-entry--temporary {
  .e-picasso-entry__title,
  .e-picasso-entry__location,
  .e-picasso-entry__responsible,
  .e-avatarrow {
    opacity: 20%;
  }
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
// Duration-Display
.e-picasso-entry__duration {
  pointer-events: none;
  display: flex;
  align-items: center;
  justify-content: center;
  inset: 0;
  position: absolute;
  opacity: 0;
  transition: opacity 0.25s ease-in-out;
}
.e-picasso-entry--resizing,
.e-picasso-entry--moving,
.e-picasso-entry--temporary {
  .e-picasso-entry__duration {
    opacity: 1;
  }
}

.e-picasso-entry--temporary {
  cursor: default;
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
