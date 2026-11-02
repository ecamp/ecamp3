<template>
  <View class="picasso-footer">
    <View class="picasso-footer-column">
      <Text v-if="camp.courseKind || camp.kind">{{
        joinWithoutBlanks([camp.courseKind, camp.kind])
      }}</Text>
      <Text v-if="camp.courseNumber">{{
        $tc('print.picasso.picassoFooter.courseNumber', {
          courseNumber: camp.courseNumber,
        })
      }}</Text>
      <Text v-if="camp.motto">{{ camp.motto }}</Text>
    </View>
    <View class="picasso-footer-column">
      <Text v-if="address">{{ address }}</Text>
      <Text v-if="dates">{{ dates }}</Text>
    </View>
    <View class="picasso-footer-column">
      <Text v-if="leaders">{{
        $tc('print.picasso.picassoFooter.leaders', { leaders })
      }}</Text>
      <Text v-if="camp.coachName">{{
        $tc('print.picasso.picassoFooter.coach', { coach: camp.coachName })
      }}</Text>
      <Text v-if="camp.trainingAdvisorName">{{
        $tc('print.picasso.picassoFooter.trainingAdvisor', {
          trainingAdvisor: camp.trainingAdvisorName,
        })
      }}</Text>
    </View>
  </View>
</template>
<script>
import PdfComponent from '@/pdf/PdfComponent.js'
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
      const leaderNames = leaders
        .map((campCollaboration) => {
          try {
            return campCollaborationLegalName(campCollaboration)
          } catch {
            return ''
          }
        })
        .filter((leaderName) => leaderName.length)
      if ('Intl' in self && 'ListFormat' in Intl) {
        return new Intl.ListFormat(this.locale, { style: 'short' }).format(leaderNames)
      }
      return leaderNames.join(', ')
    },
  },
  methods: {
    joinWithoutBlanks(list, separator = ', ') {
      return list.filter((element) => !!element).join(separator)
    },
  },
}
</script>
<style lang="react-pdf">
.picasso-footer {
  width: 100%;
  font-size: 9pt;
  display: flex;
  flex-direction: row;
  border: 1pt solid grey;
  padding: 1pt 2pt 2pt;
  justify-content: space-between;
  gap: 4pt;
}
.picasso-footer-column {
  max-width: 33%;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  justify-content: flex-start;
  gap: 2pt;
  line-height: 0.5;
}
</style>
