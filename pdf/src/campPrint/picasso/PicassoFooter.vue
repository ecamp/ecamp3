<template>
  <View class="picasso-footer">
    <View class="picasso-footer-column">
      <Text v-if="camp.courseKind || camp.kind" class="picasso-footer-field">{{
        joinWithoutBlanks([camp.courseKind, camp.kind])
      }}</Text>
      <Text v-if="camp.courseNumber" class="picasso-footer-field">{{
        $tc('print.picasso.picassoFooter.courseNumber', {
          courseNumber: camp.courseNumber,
        })
      }}</Text>
      <Text v-if="camp.motto" class="picasso-footer-field">{{ camp.motto }}</Text>
    </View>
    <View class="picasso-footer-column">
      <Text v-if="address" class="picasso-footer-field">{{ address }}</Text>
      <Text v-if="dates" class="picasso-footer-field">{{ dates }}</Text>
    </View>
    <View class="picasso-footer-column">
      <Text class="picasso-footer-field">{{
        $tc('print.picasso.picassoFooter.leaders', { leaders })
      }}</Text>
      <Text v-if="camp.coachName" class="picasso-footer-field">{{
        $tc('print.picasso.picassoFooter.coach', { coach: camp.coachName })
      }}</Text>
      <Text v-if="camp.trainingAdvisorName" class="picasso-footer-field">{{
        $tc('print.picasso.picassoFooter.trainingAdvisor', {
          trainingAdvisor: camp.trainingAdvisorName,
        })
      }}</Text>
    </View>
  </View>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import campCollaborationLegalName from '../../../common/helpers/campCollaborationLegalName.js'

export default {
  name: 'PicassoFooter',
  extends: PdfComponent,
  props: {
    period: { type: Object, required: true },
    locale: { type: String, default: 'en' },
  },
  computed: {
    camp() {
      return this.period.camp()
    },
    address() {
      return this.joinWithoutBlanks([
        this.camp.addressName,
        this.camp.addressStreet,
        this.joinWithoutBlanks([this.camp.addressZipcode, this.camp.addressCity], ' '),
      ])
    },
    dates() {
      const startDate = this.$date.utc(this.period.start).hour(0).minute(0).second(0)
      const endDate = this.$date.utc(this.period.end).hour(0).minute(0).second(0)
      return this.$date.formatDatePeriod(
        startDate,
        endDate,
        this.$tc('global.datetime.dateLong'),
        this.locale
      )
    },
    leaders() {
      const leaders = this.camp.campCollaborations().items.filter((campCollaboration) => {
        return (
          campCollaboration.status === 'established' &&
          campCollaboration.role === 'manager'
        )
      })
      const leaderNames = leaders.map((campCollaboration) => {
        return campCollaborationLegalName(campCollaboration)
      })
      return new Intl.ListFormat(this.locale, { style: 'short' }).format(leaderNames)
    },
  },
  methods: {
    joinWithoutBlanks(list, separator = ', ') {
      return list.filter((element) => !!element).join(separator)
    },
  },
}
</script>
<pdf-style>
.picasso-footer {
  width: 100%;
  font-size: 9pt;
  display: flex;
  flex-direction: row;
  margin-top: 6pt;
  border: 1pt solid grey;
  padding: 0 0 3pt;
  justify-content: space-between;
}
.picasso-footer-column {
  max-width: 33%;
  align-items: flex-start;
  justify-content: flex-start;
  gap: 6pt;
  line-height: 1.1;
  padding: 2pt 3pt 3pt;
}
.picasso-footer-field {
  margin-bottom: 6pt;
}
</pdf-style>
