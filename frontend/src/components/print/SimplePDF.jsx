// eslint-disable-next-line no-unused-vars
import React from 'react'
import pdf from '@react-pdf/renderer'
import OpenSans from '../../assets/fonts/OpenSans/OpenSans-Regular.ttf'
import OpenSansSemiBold from '../../assets/fonts/OpenSans/OpenSans-SemiBold.ttf'
import OpenSansBold from '../../assets/fonts/OpenSans/OpenSans-Bold.ttf'

const { Document, Page, Text, Font } = pdf

function SimplePDF (props) {
  const camp = props.store.get(props.config.camp)
  return <Document>
    <Page>
      { Array.from(new Array(10).keys()).map(i => {
        return <Text key={i} style={{ fontFamily: 'OpenSans' }}>
          { camp.title } { props.$tc('entity.user.fields.email') } { camp.periods().items.length } { camp.periods().items.map(period => period.description) }
          Hello world! I was rendered in a background worker! ... or not? I don't care, I'm just a simple react component. showFrontpage: {props.config.showFrontpage ? 'true' : 'false'}
        </Text>
      }) }
    </Page>
  </Document>
}

export const loadData = async (config) => {
  // Load any data necessary based on the print config
  return await Promise.all([
    config.camp()._meta.load,
    config.camp().periods().$loadItems()
    /*
  return this.camp()._meta.loading ||
    this.camp().periods()._meta.loading ||
    this.camp().periods().items.some(period => {
      return period._meta.loading ||
        period.scheduleEntries()._meta.loading ||
        period.scheduleEntries().items.some(scheduleEntry => {
          return scheduleEntry._meta.loading ||
            scheduleEntry.activity()._meta.loading ||
            scheduleEntry.activity().category()._meta.loading ||
            scheduleEntry.activity().activityResponsibles()._meta.loading ||
            scheduleEntry.activity().activityResponsibles().items.some(responsible => {
              return responsible._meta.loading ||
                responsible.campCollaboration()._meta.loading ||
                (responsible.campCollaboration().user() !== null && responsible.campCollaboration().user()._meta.loading)
            }) ||
            scheduleEntry.activity().contentNodes()._meta.loading ||
            scheduleEntry.activity().contentNodes().items.some(contentNode => {
              return contentNode._meta.loading ||
                contentNode.contentType()._meta.loading
            })
        })
    }) ||
    this.camp().materialLists()._meta.loading ||
    this.camp().materialLists().items.some(materialList => {
      return materialList._meta.loading
    })
     */
  ])
}

const registerFonts = async () => {
  Font.register({
    family: 'OpenSans',
    fonts: [
      // For now it seems that only ttf is supported, not woff or woff2 :(
      { src: OpenSans },
      { src: OpenSansSemiBold, fontWeight: 'semibold' },
      { src: OpenSansBold, fontWeight: 'bold' }
    ]
  })

  return await Promise.all([
    Font.load({ fontFamily: 'OpenSans' }),
    Font.load({ fontFamily: 'OpenSans', fontWeight: 600 }),
    Font.load({ fontFamily: 'OpenSans', fontWeight: 700 })
  ])
}

SimplePDF.prepareInMainThread = async (config) => {
  return await loadData(config)
}

SimplePDF.prepare = async (config) => {
  return await registerFonts(config)
}

export default SimplePDF
