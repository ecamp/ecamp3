// eslint-disable-next-line no-unused-vars
import React from 'react'
import { Page, Text } from '@react-pdf/renderer'
import styles from '../styles.js'

function Cover(props) {
  const camp = props.store.get(props.config.camp._meta.self)
  return (
    <Page
      id={props.id}
      bookmark={props.$tc('components.cover.title')}
      style={styles.page}
    >
      <Text style={{ textAlign: 'center', margin: '40pt 0', fontSize: '40pt' }}>
        {camp.title}
      </Text>
      <Text style={{ fontSize: '20pt', marginBottom: '25pt', textAlign: 'center' }}>
        <Text style={{ fontWeight: 'bold' }}>
          {props.$tc('entity.camp.fields.title')}:{' '}
        </Text>
        {camp.title}
      </Text>
      <Text style={{ fontSize: '20pt', marginBottom: '25pt', textAlign: 'center' }}>
        <Text style={{ fontWeight: 'bold' }}>
          {props.$tc('entity.camp.fields.motto')}:{' '}
        </Text>
        {camp.motto}
      </Text>
    </Page>
  )
}

export default Cover
