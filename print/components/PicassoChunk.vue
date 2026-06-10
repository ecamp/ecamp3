<template>
  <div
    class="tw-break-after-page"
    :class="{ 'landscape-page': landscape, [pageSize]: true }"
  >
    <div
      class="tw-flex tw-flex-col"
      :class="[landscape ? 'landscape' : 'portrait', pageSize]"
    >
      <div
        class="tw-flex-initial tw-flex tw-flex-row tw-items-center tw-gap-2 tw-mb-3 tw-font-semibold"
      >
        <YSLogo v-if="camp.printYSLogoOnPicasso" class="tw-self-center" width="7mm" />
        <h1 :id="`content_${index}_period_${period.id}`" class="tw-text-2xl tw-flex-grow">
          {{ $t('print.picasso.title') }}
          {{ period.description }}
        </h1>
        <p class="tw-text-md tw-text-end">
          {{ camp.organizer }}
        </p>
      </div>

      <div class="tw-flex-auto fullwidth">
        <picasso-calendar
          :days="days"
          :times="times"
          :schedule-entries="scheduleEntries"
        />
      </div>
      <div class="tw-flex-initial categories fullwidth text-sm-relative">
        <div v-for="category in categories" :key="category.id" class="categories">
          <div class="category">
            <category-label :category="category" />
            {{ category.name }}
          </div>
        </div>
      </div>
      <div class="tw-flex-initial footer fullwidth text-sm-relative">
        <div class="footer-column">
          <span v-if="camp.courseKind || camp.kind">
            {{ joinWithoutBlanks([camp.courseKind, camp.kind], ', ') }}
          </span>
          <i18n-t
            v-if="camp.courseNumber"
            tag="span"
            keypath="print.picasso.picassoFooter.courseNumber"
            scope="global"
          >
            <template #courseNumber>
              {{ camp.courseNumber }}
            </template>
          </i18n-t>
          <span v-if="camp.motto" class="tw-self-start">{{ camp.motto }}</span>
        </div>
        <div class="footer-column">
          <span v-if="address">{{ address }}</span>
          <span v-if="dates">{{ dates }}</span>
        </div>
        <div class="footer-column">
          <i18n-t tag="span" keypath="print.picasso.picassoFooter.leaders" scope="global">
            <template #leaders>
              {{ leaderNameList }}
            </template>
          </i18n-t>
          <i18n-t
            v-if="camp.coachName"
            tag="span"
            keypath="print.picasso.picassoFooter.coach"
            scope="global"
          >
            <template #coach>
              {{ camp.coachName }}
            </template>
          </i18n-t>
          <i18n-t
            v-if="camp.trainingAdvisorName"
            tag="span"
            keypath="print.picasso.picassoFooter.trainingAdvisor"
            scope="global"
          >
            <template #trainingAdvisor>
              {{ camp.trainingAdvisorName }}
            </template>
          </i18n-t>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import CategoryLabel from './generic/CategoryLabel.vue'
import dayjs from '@/common/helpers/dayjs.js'
import campCollaborationLegalName from '@/common/helpers/campCollaborationLegalName.js'
import YSLogo from '@/components/generic/YSLogo.vue'

export default {
  components: { YSLogo, CategoryLabel },
  props: {
    period: { type: Object, required: true },
    scheduleEntries: { type: Array, required: true },
    index: { type: Number, required: true },
    landscape: { type: Boolean, required: true },
    days: { type: Array, required: true },
    times: { type: Array, required: true },
    pageSize: { type: String, default: 'a4' },
  },
  computed: {
    camp() {
      return this.period.camp()
    },
    categories() {
      const usedCategoryUris = [
        ...new Set(
          this.scheduleEntries.map(
            (scheduleEntry) => scheduleEntry.activity().category()._meta.self
          )
        ),
      ]
      return this.camp.categories().items.filter((category) => {
        return usedCategoryUris.includes(category._meta.self)
      })
    },
    address() {
      return this.joinWithoutBlanks(
        [
          this.camp.addressName,
          this.camp.addressStreet,
          this.joinWithoutBlanks([this.camp.addressZipcode, this.camp.addressCity], ' '),
        ],
        ', '
      )
    },
    dates() {
      const startDate = dayjs.utc(this.period.start).hour(0).minute(0).second(0)
      const endDate = dayjs.utc(this.period.end).hour(0).minute(0).second(0)
      return dayjs.formatDatePeriod(
        startDate,
        endDate,
        this.$t('global.datetime.dateLong'),
        this.$i18n.locale
      )
    },
    leaderNameList() {
      const leaders = this.camp.campCollaborations().items.filter((campCollaboration) => {
        return (
          campCollaboration.status === 'established' &&
          campCollaboration.role === 'manager'
        )
      })
      const leaderNames = leaders.map((campCollaboration) => {
        return campCollaborationLegalName(campCollaboration)
      })
      return new Intl.ListFormat(this.$i18n.locale, { style: 'short' }).format(
        leaderNames
      )
    },
  },
  methods: {
    joinWithoutBlanks(list, separator) {
      return list.filter((element) => !!element).join(separator)
    },
  },
}
</script>

<!-- these styles seem to effect the whole picasso, thus we don't want the vue-scoped-css/enforce-style-type warning here -->
<!-- eslint-disable-next-line -->
<style lang="scss">
$a3-portrait-content-width: 1009; /* 1123px minus 114px (=2*15mm margin) */
$a3-portrait-content-height: 1474; /* 1588px minus 114px (=2*15mm margin) */
$a4-portrait-content-width: 680; /* 794px minus 114px (=2*15mm margin) */
$a4-portrait-content-height: 1009; /* 1123px minus 114px (=2*15mm margin) */
$a5-portrait-content-width: 391; /* 505px minus 114px (=2*15mm margin) */
$a5-portrait-content-height: 680; /* 794px minus 114px (=2*15mm margin) */

@page a3landscape {
  size: a3 landscape;
}
@page a4landscape {
  size: a4 landscape;
}
@page a5landscape {
  size: a5 landscape;
}

.a3.landscape-page {
  page: a3landscape;
}
.a4.landscape-page {
  page: a4landscape;
}
.a5.landscape-page {
  page: a5landscape;
}

.a3.landscape {
  width: #{$a3-portrait-content-height}px;
  height: #{$a3-portrait-content-width}px;
}
.a4.landscape {
  width: #{$a4-portrait-content-height}px;
  height: #{$a4-portrait-content-width}px;
}
.a5.landscape {
  width: #{$a5-portrait-content-height}px;
  height: #{$a5-portrait-content-width}px;
}

.landscape,
.portrait {
  font-size: 10pt;
  overflow: visible;
}

.a3.portrait {
  width: #{$a3-portrait-content-width}px;
  height: #{$a3-portrait-content-height}px;
}
.a4.portrait {
  width: #{$a4-portrait-content-width}px;
  height: #{$a4-portrait-content-height}px;
}
.a5.portrait {
  width: #{$a5-portrait-content-width}px;
  height: #{$a5-portrait-content-height}px;
}

.v-calendar-daily_head-day {
  background-color: #cfd8dc;
}

.v-calendar .v-event-timed-container {
  margin-right: 4px;
}

.a3.fullwidth {
  width: $a3-portrait-content-width;
}
.a4.fullwidth {
  width: $a4-portrait-content-width;
}
.a5.fullwidth {
  width: $a5-portrait-content-width;
}

.v-calendar {
  overflow: visible;
}

.v-calendar .v-event-timed {
  font-size: 0.8em;
  padding: 0 1px;
  hyphens: auto;
  white-space: normal;
  overflow-wrap: break-word;
  overflow-y: hidden;
  word-wrap: break-word;
  word-break: break-all;

  a {
    color: black;
  }
}

.v-theme--light.v-calendar-events .v-event-timed {
  border: none !important;
  outline: 0.1mm solid black !important;
  line-height: 1.3;
}

.v-calendar-daily__interval-text {
  font-size: 0.8em;
  font-feature-settings: 'tnum';
}
.a5 {
  .v-calendar-daily__interval {
    position: relative;
  }

  .v-calendar-daily__interval-text {
    position: absolute;
    font-size: 0.6em;
  }
}

.v-calendar-daily__day-interval:nth-child(2n) {
  background-color: #eceff1;
}

.v-calendar-daily_head-day-label {
  font-size: 1em;
}

.categories {
  display: flex;
  flex-direction: row;
  flex-wrap: wrap;
  margin-top: 1em;
  gap: 3px;

  .category {
    display: flex;
    flex-direction: row;
    align-items: baseline;
    gap: 3px;
    margin-right: 3px;
  }
}

.footer {
  display: flex;
  flex-direction: row;
  margin-top: 0.75em;
  border: 1px solid grey;
  padding: 0 0 4px;

  .footer-column {
    flex-grow: 1;
    max-width: 33%;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: flex-start;
    line-height: 1.1;
    gap: 6px;
    padding: 3px 4px 4px;
  }
}

/**
 * the following classes use the same naming pattern & values as tailwind
 * however using em instead of rem
 */
.text-2xl-relative {
  font-size: 1.5em;
  line-height: 2em;
}

.text-sm-relative {
  font-size: 0.875em;
  line-height: 1.25em;
}

.text-xs-relative {
  font-size: 0.75em;
  line-height: 1em;
}
</style>
