<template>
  <tr class="row">
    <th align="left" class="tabular-nums" scope="row">
      <BaselineCenter smaller>{{ scheduleEntry.number }}</BaselineCenter>
      <br />
      <CategoryChip small dense :category="scheduleEntry.category" class="d-sm-none" />
    </th>
    <td class="d-none d-sm-table-cell">
      <CategoryChip small dense :category="scheduleEntry.category" />
    </td>
    <td>
      {{ scheduleEntry.start.padStart(2, '0') }}:00<br />
      <span class="e-subtitle">{{ scheduleEntry.duration }}</span>
    </td>
    <td width="100%" class="contentrow">
      <a :to="{ name: 'dashboard' }" class="black--text">
        {{ scheduleEntry.title }}<br />
      </a>
      <span class="e-subtitle">{{ scheduleEntry.location }}</span>
    </td>
    <td class="contentrow avatarrow overflow-visible">
      <AvatarRow :users="scheduleEntry.collaborators" size="28" class="ml-auto" />
    </td>
  </tr>
</template>

<script>
import AvatarRow from '@/components/user/AvatarRow.vue'
import CategoryChip from '@/components/generic/CategoryChip.vue'
import BaselineCenter from '@/components/dashboard/BaselineCenter.vue'

export default {
  name: 'ActivityRow',
  components: { BaselineCenter, CategoryChip, AvatarRow },
  props: {
    scheduleEntry: { type: Object, required: true },
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
</style>
