// eslint-disable-next-line no-unused-vars
import React from 'react'
import pdf from '@react-pdf/renderer'
import styles from './styles.js'

const { Text } = pdf

function Footer ({ camp }) {
  return <Text style={styles.footer} render={({ pageNumber, totalPages }) => (
    `${pageNumber} / ${totalPages}`
  )} fixed />
}

export default Footer
