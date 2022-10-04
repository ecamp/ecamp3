// eslint-disable-next-line no-unused-vars
import React from 'react'
import { Text, View } from '@react-pdf/renderer'
import styles from './styles.js'

function CategoryLabel({ activity, style }) {
  return (
    <View
      style={{
        ...styles.categoryLabel,
        backgroundColor: activity.category().color,
        ...style,
      }}
    >
      <Text style={{ ...styles.h1, marginBottom: 0 }}>{activity.category().short}</Text>
    </View>
  )
}

export default CategoryLabel
