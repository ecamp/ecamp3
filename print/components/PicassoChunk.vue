<template>
  <div class="tw-break-after-page">
    <div :class="landscape ? 'landscape' : ''">
      <div class="tw-flex tw-flex-row tw-items-baseline fullwidth">
        <h1
          :id="`content_${index}_period_${period.id}`"
          class="tw-text-2xl tw-font-bold tw-mb-6 tw-flex-grow tw-d-inline"
        >
          {{ $tc('print.picasso.title') }}
          {{ period.description }}
        </h1>
        <span>{{ camp.organizer }}</span>
        <img
          v-if="camp.printYSLogoOnPicasso"
          height="35"
          width="35"
          :src="ysLogoUrl"
          class="tw-self-start tw-ml-2"
        />
      </div>

      <v-sheet :width="landscape ? 960 : 680">
        <v-calendar
          ref="calendar"
          :events="events"
          event-start="startTimestamp"
          event-end="endTimestamp"
          :event-color="getActivityColor"
          :start="start"
          :end="end"
          type="custom-daily"
          event-overlap-mode="column"
          first-interval="0"
          interval-count="24"
          :interval-height="landscape ? 22 : 33"
          interval-width="46"
          event-text-color="black"
          :locale="$i18n.locale"
          :interval-format="intervalFormat"
          :day-format="dayFormat"
          :weekday-format="weekdayFormat"
        >
          <!-- day header -->
          <template #day-label-header="{ date }">
            <span class="tw-block">
              {{ $date.utc(date).format($tc('global.datetime.dateLong')) }}
            </span>

            <span v-if="hasDayResponsibles(date)" class="tw-text-sm tw-italic">
              {{ $tc('entity.day.fields.dayResponsibles') }}:
              {{ dayResponsiblesCommaSeparated(date) }}
            </span>
          </template>

          <template #event="{ event }">
            <div class="tw-float-left tw-text-xs tw-font-weight-medium">
              <!-- link jumps to first instance of scheduleEntry within the document -->
              <a
                :href="`#scheduleEntry_${event.id}`"
                :style="{ color: getActivityTextColor(event) }"
              >
                ({{ event.number }})&nbsp; {{ event.activity().category().short }}:&nbsp;
                {{ event.activity().title }}
              </a>
            </div>
            <span
              class="tw-float-right tw-text-xs tw-italic ml-1"
              :style="{ color: getActivityTextColor(event) }"
            >
              {{ activityResponsiblesCommaSeparated(event) }}
            </span>
          </template>
        </v-calendar>
      </v-sheet>
      <div class="categories fullwidth">
        <div
          v-for="category in camp.categories().items"
          :key="category.id"
          class="categories"
        >
          <div class="category">
            <category-label :category="category"></category-label>
            {{ category.name }}
          </div>
        </div>
      </div>
      <div class="footer fullwidth">
        <div class="footer-column">
          <span v-if="camp.courseKind || camp.kind">
            {{ joinWithoutBlanks([camp.courseKind, camp.kind], ', ') }}
          </span>
          <i18n
            v-if="camp.courseNumber"
            tag="span"
            path="print.picasso.picassoFooter.courseNumber"
          >
            <template #courseNumber>{{ camp.courseNumber }}</template>
          </i18n>
          <span v-if="camp.motto" class="tw-self-start">{{ camp.motto }}</span>
        </div>
        <div class="footer-column">
          <span v-if="address">{{ address }}</span>
          <span v-if="dates">{{ dates }}</span>
        </div>
        <div class="footer-column">
          <i18n tag="span" path="print.picasso.picassoFooter.leaders">
            <template #leaders>{{ leaderNameList }}</template>
          </i18n>
          <i18n v-if="camp.coachName" tag="span" path="print.picasso.picassoFooter.coach">
            <template #coach>{{ camp.coachName }}</template>
          </i18n>
          <i18n
            v-if="camp.trainingAdvisorName"
            tag="span"
            path="print.picasso.picassoFooter.trainingAdvisor"
          >
            <template #trainingAdvisor>{{ camp.trainingAdvisorName }}</template>
          </i18n>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { activityResponsiblesCommaSeparated } from '@/../common/helpers/activityResponsibles.js'
import { dayResponsiblesCommaSeparated } from '@/../common/helpers/dayResponsibles.js'
import { contrastColor } from '@/../common/helpers/colors.js'
import CategoryLabel from './generic/CategoryLabel.vue'
import dayjs from '@/../common/helpers/dayjs.js'
import campCollaborationLegalName from '../../common/helpers/campCollaborationLegalName.js'

export default {
  components: { CategoryLabel },
  props: {
    period: { type: Object, required: true },
    start: { type: String, required: true },
    end: { type: String, required: true },
    events: { type: Array, required: true },
    index: { type: Number, required: true },
    landscape: { type: Boolean, required: true },
  },
  computed: {
    camp() {
      return this.period.camp()
    },
    ysLogoUrl() {
      return this.$i18n.locale.match(/it/i) ? './gs-logo.svg' : './js-logo.svg'
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
        this.$tc('global.datetime.dateLong'),
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
    getActivityColor(scheduleEntry) {
      return scheduleEntry.activity().category().color
    },
    getActivityTextColor(scheduleEntry) {
      const color = this.getActivityColor(scheduleEntry)
      return contrastColor(color)
    },
    intervalFormat(time) {
      return this.$date
        .utc(time.date + ' ' + time.time)
        .format(this.$tc('global.datetime.hourLong'))
    },
    dayFormat(day) {
      return this.$date.utc(day.date).format(this.$tc('global.datetime.dateLong'))
    },
    weekdayFormat() {
      return ''
    },
    activityResponsiblesCommaSeparated(scheduleEntry) {
      const responsibles = activityResponsiblesCommaSeparated(
        scheduleEntry.activity(),
        this.$tc.bind(this)
      )

      if (responsibles === '') {
        return ''
      }

      return `[${responsibles}]`
    },

    dayResponsiblesCommaSeparated(date) {
      const day = this.getDayByDate(date)
      if (!day) return null
      return dayResponsiblesCommaSeparated(day, this.$tc.bind(this))
    },

    hasDayResponsibles(date) {
      const day = this.getDayByDate(date)
      if (!day) return false
      return day.dayResponsibles().items.length > 0
    },

    getDayByDate(date) {
      return this.period.days().items.find((day) => {
        return this.$date.utc(date).isSame(this.$date.utc(day.start), 'day')
      })
    },
    joinWithoutBlanks(list, separator) {
      return list.filter((element) => !!element).join(separator)
    },
  },
}
</script>

<style lang="scss">
.landscape {
  transform: rotate(-90deg);
  position: relative;
  top: 320px;

  .fullwidth {
    width: 960px;
  }
}

.fullwidth {
  width: 680px;
}

.v-calendar {
  overflow: visible;
}

.v-calendar-daily__body {
  overflow: visible;
}

.v-calendar-daily__pane {
  overflow-y: visible;
}

.v-calendar-daily__scroll-area {
  overflow-y: visible;
}

.v-calendar .v-event-timed {
  padding: 2px;
  white-space: normal;
  overflow-wrap: break-word;
  overflow-y: hidden;
  word-wrap: break-word;
  word-break: break-all;

  a {
    color: black;
  }
}

.categories {
  font-size: 12px;
  display: flex;
  flex-direction: row;
  flex-wrap: wrap;
  margin: 3px 0 0;
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
  font-size: 12px;
  display: flex;
  flex-direction: row;
  margin-top: 8px;
  border: 1px solid grey;
  padding: 0 0 4px;

  .footer-column {
    flex-grow: 1;
    max-width: 33%;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: flex-start;
    line-height: 1.2;
    gap: 6px;
    padding: 3px 4px 4px;
  }
}
</style>
