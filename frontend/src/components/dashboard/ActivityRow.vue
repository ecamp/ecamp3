<template>
  <tr class="row" :class="{ 'row--skeleton': scheduleEntry._meta.loading }">
    <th class="tabular-nums text-left" scope="row">
      <CategoryChip
        v-if="!loadingEndpoints?.categories && !scheduleEntry._meta.loading"
        small
        dense
        :category="category"
        class="d-sm-none"
      />
      <CategoryChip v-else class="d-sm-none" small dense skeleton />
      <br class="d-sm-none" />
      <TextAlignBaseline
        ><span v-if="!scheduleEntry._meta.loading" class="smaller">{{
          scheduleEntry.number
        }}</span>
        <v-skeleton-loader v-else type="text" width="2ch" class="mb-0 my-6px" />
      </TextAlignBaseline>
    </th>
    <td class="d-none d-sm-table-cell">
      <CategoryChip
        v-if="!loadingEndpoints?.categories && !scheduleEntry._meta.loading"
        small
        dense
        :category="category"
      />
      <CategoryChip v-else small dense skeleton />
    </td>
    <td v-if="!scheduleEntry._meta.loading" class="nowrap">
      {{ start }}<br />
      <span class="e-subtitle">{{ duration }}</span>
    </td>
    <td v-else class="nowrap">
      <v-skeleton-loader type="text" width="6ch" class="my-6px" />
      <v-skeleton-loader type="text" width="4ch" />
    </td>
    <td v-if="!scheduleEntry._meta.loading" class="w-100 contentrow">
      <router-link
        :to="routerLink"
        class="text-decoration-none text-decoration-hover-underline black--text font-weight-medium"
      >
        {{ title }}
      </router-link>

      <span
        v-if="!loadingEndpoints?.progressLabels && $vuetify.breakpoint.mdAndUp"
        class="e-subtitle e-subtitle--smaller"
      >
        {{ progressLabel }}
      </span>

      <template v-if="location">
        <br />
        <span class="e-subtitle">{{ location }}</span>
      </template>

      <template v-if="!loadingEndpoints?.progressLabels && !$vuetify.breakpoint.mdAndUp">
        <br />
        <span class="e-subtitle e-subtitle--smaller">
          {{ progressLabel }}
        </span>
      </template>
    </td>
    <td v-else class="w-100 contentrow">
      <v-skeleton-loader type="text" width="20ch" class="my-6px" />
      <v-skeleton-loader
        type="text"
        width="15ch"
        class="v-skeleton-loader--no-margin my-6px"
      />
    </td>
    <td class="contentrow avatarrow overflow-visible">
      <AvatarRow
        v-if="!loadingEndpoints?.campCollaborations && !scheduleEntry._meta.loading"
        :camp-collaborations="collaborators"
        max-size="28"
        class="ml-auto"
      />
      <v-skeleton-loader
        v-else
        type="avatar"
        width="28"
        height="28"
        class="v-skeleton-loader--inherit-size"
      />
    </td>
  </tr>
</template>

<script>
import AvatarRow from '@/components/generic/AvatarRow.vue'
import CategoryChip from '@/components/generic/CategoryChip.vue'
import { dateHelperUTCFormatted } from '@/mixins/dateHelperUTCFormatted.js'
import TextAlignBaseline from '@/components/layout/TextAlignBaseline.vue'

export default {
  name: 'ActivityRow',
  components: { CategoryChip, AvatarRow, TextAlignBaseline },
  mixins: [dateHelperUTCFormatted],
  props: {
    scheduleEntry: { type: Object, default: () => ({ _meta: { loading: true } }) },
    loadingEndpoints: {
      type: Object,
      default: () => ({
        categories: true,
        periods: true,
        campCollaborations: true,
        progressLabels: true,
      }),
    },
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
    progressLabel() {
      return this.scheduleEntry.activity().progressLabel?.().title
    },
    location() {
      return this.scheduleEntry.activity().location
    },
    start() {
      return this.hourShort(this.scheduleEntry.start)
    },
    duration() {
      return this.timeDurationShort(this.scheduleEntry.start, this.scheduleEntry.end)
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
  vertical-align: baseline;
}

.row--skeleton {
  vertical-align: top;
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
  max-width: 64px;
  overflow: hidden;
  text-overflow: ellipsis;
}

.avatarrow {
  vertical-align: middle;
}

.e-subtitle {
  font-size: 0.9em;
  color: #666;
}

.e-subtitle--smaller {
  font-size: 0.7em;
}

.nowrap {
  white-space: nowrap;
}

.smaller {
  font-size: 0.75em;
}

.my-6px {
  margin-top: 6px;
  margin-bottom: 6px;
}

.text-decoration-hover-underline:hover {
  text-decoration: underline !important;
}
</style>
