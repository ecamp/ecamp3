<template>
  <tr class="row">
    <th style="text-align: left" class="tabular-nums" scope="row">
      <span class="baseline-center"
        ><span class="smaller">{{ scheduleEntry.number }}</span></span
      >
      <br />
      <CategoryChip small dense :category="category" class="d-sm-none" />
    </th>
    <td class="d-none d-sm-table-cell">
      <CategoryChip small dense :category="category" />
    </td>
    <td class="nowrap">
      {{ start }}<br />
      <span class="e-subtitle">{{ duration }}</span>
    </td>
    <td style="width: 100%" class="contentrow">
      <router-link :to="routerLink" class="text-decoration-none black--text">
        {{ title }}<br />
      </router-link>
      <span class="e-subtitle">{{ location }}</span>
    </td>
    <td class="contentrow avatarrow overflow-visible">
      <AvatarRow :camp-collaborations="collaborators" size="28" class="ml-auto" />
    </td>
  </tr>
</template>

<script>
import AvatarRow from './AvatarRow.vue'
import CategoryChip from '@/components/generic/CategoryChip.vue'
import {
  hourShort,
  timeDurationShort,
} from '../../common/helpers/dateHelperUTCFormatted.js'

export default {
  name: 'ActivityRow',
  components: { CategoryChip, AvatarRow },
  props: {
    scheduleEntry: { type: Object, required: true },
  },
  computed: {
    collaborators() {
      return this.scheduleEntry
        .activity()
        .activityResponsibles()
        .items.map((responsible) => responsible.campCollaboration())
    },
    category() {
      return this.scheduleEntry.activity().category()
    },
    title() {
      return this.scheduleEntry.activity().title
    },
    location() {
      return this.scheduleEntry.activity().location
    },
    start() {
      return hourShort(this.scheduleEntry.start)
    },
    duration() {
      return timeDurationShort(this.scheduleEntry.start, this.scheduleEntry.end)
    },
    routerLink() {
      return {
        name: 'activity',
        params: {
          campId: this.scheduleEntry.period().camp().id,
          scheduleEntryId: this.scheduleEntry.id,
        },
      }
    },
  },
}
</script>

<style scoped>
.row {
  display: table-row;
  gap: 1rem;
  vertical-align: baseline;
}

tr + tr :is(td, th) {
  border-top: 1px solid #ddd;
}

:is(td, th) {
  padding-top: 0.25rem;
  padding-bottom: 0.25rem;
}

:is(td, th) + :is(td, th) {
  padding-left: 0.5rem;
}

.contentrow {
  max-width: 100px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.avatarrow {
  vertical-align: middle;
}

.e-subtitle {
  font-size: 0.9em;
  color: #666;
}

.nowrap {
  white-space: nowrap;
}

.smaller {
  font-size: 0.75em;
}

.baseline-center {
  display: inline-flex;
  vertical-align: baseline;
  align-items: center;
}
</style>
