// eslint-disable-next-line no-unused-vars
import React from 'react'
import { Text } from '@react-pdf/renderer'
import styles from './styles.js'
import { contrastColor } from '@/common/helpers/colors.js'
import Color from 'colorjs.io'

function textColor(color) {
  return contrastColor(new Color(color)).toString({ format: 'hex' })
}

function CategoryLabel({ activity, style }) {
  return (
    <Text
      style={{
        ...styles.categoryLabel,
        color: textColor(activity.category().color),
        backgroundColor: activity.category().color,
        ...style,
      }}
    >
      {activity.category().short}
    </Text>
  )
}

export default CategoryLabel
