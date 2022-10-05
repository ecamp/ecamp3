// eslint-disable-next-line no-unused-vars
import React from 'react'
import { Document, Font } from '@react-pdf/renderer'
import TableOfContents from '../../components/tableOfContents/TableOfContents.jsx'
import Picasso from '../../components/picasso/Picasso.jsx'
import Program from '../../components/program/Program.jsx'
import Activity from '../../components/activity/Activity.jsx'
import Cover from '../../components/cover/Cover.jsx'
import Story from '../../components/story/Story.jsx'
import OpenSans from '@/assets/fonts/OpenSans/OpenSans-Regular.ttf'
import OpenSansItalic from '@/assets/fonts/OpenSans/OpenSans-Italic.ttf'
import OpenSansSemiBold from '@/assets/fonts/OpenSans/OpenSans-SemiBold.ttf'
import OpenSansSemiBoldItalic from '@/assets/fonts/OpenSans/OpenSans-SemiBoldItalic.ttf'
import OpenSansBold from '@/assets/fonts/OpenSans/OpenSans-Bold.ttf'
import OpenSansBoldItalic from '@/assets/fonts/OpenSans/OpenSans-BoldItalic.ttf'

const components = {
  Cover,
  Toc: TableOfContents,
  Picasso,
  Program,
  Activity,
  Story,
}

function PDFDocument(props) {
  return (
    <Document>
      {props.config.contents.map((content, idx) => {
        const component = components[content.type]
        if (component) {
          return React.createElement(component, {
            ...props,
            content,
            id: `entry-${idx}`,
            key: idx,
          })
        }
        return <React.Fragment key={idx} />
      })}
    </Document>
  )
}

const registerFonts = async () => {
  Font.register({
    family: 'OpenSans',
    fonts: [
      // For now it seems that only ttf is supported, not woff or woff2
      { src: OpenSans },
      { src: OpenSansSemiBold, fontWeight: 'semibold' },
      { src: OpenSansBold, fontWeight: 'bold' },
      { src: OpenSansItalic, fontStyle: 'italic' },
      { src: OpenSansSemiBoldItalic, fontWeight: 'semibold', fontStyle: 'italic' },
      { src: OpenSansBoldItalic, fontWeight: 'bold', fontStyle: 'italic' },
    ],
  })

  return await Promise.all([
    Font.load({ fontFamily: 'OpenSans' }),
    Font.load({ fontFamily: 'OpenSans', fontWeight: 600 }),
    Font.load({ fontFamily: 'OpenSans', fontWeight: 700 }),
    Font.load({ fontFamily: 'OpenSans', fontStyle: 'italic' }),
    Font.load({ fontFamily: 'OpenSans', fontWeight: 600, fontStyle: 'italic' }),
    Font.load({ fontFamily: 'OpenSans', fontWeight: 700, fontStyle: 'italic' }),
  ])
}

PDFDocument.prepare = async (config) => {
  return await registerFonts(config)
}

export default PDFDocument
