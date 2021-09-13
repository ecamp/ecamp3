// eslint-disable-next-line no-unused-vars
import React from 'react'
import { Text } from '@react-pdf/renderer'
import styles from './styles.js'

function Footer ({ camp }) {
  return <Text style={styles.footer} render={({ pageNumber, totalPages }) => (
    `${pageNumber} / ${totalPages}`
  )} fixed />
}

export default Footer
