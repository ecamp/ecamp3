// eslint-disable-next-line no-unused-vars
import React from 'react'
import pdf from '@react-pdf/renderer'
import styles from './styles.js'

const { Text } = pdf

function Header ({ camp }) {
  return <Text fixed style={styles.header}>
    eCamp3: {camp.title}
  </Text>
}

export default Header
