// eslint-disable-next-line no-unused-vars
import React from 'react'
import { Text } from '@react-pdf/renderer'
import styles from './styles.js'

function CategoryLabel({ activity, style }) {
  return (
    <Text
      style={{
        ...styles.categoryLabel,
        backgroundColor: activity.category().color,
        ...style,
      }}
    >
      {activity.category().short}
    </Text>
  )
}

export default CategoryLabel
