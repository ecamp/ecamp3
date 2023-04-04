// eslint-disable-next-line no-unused-vars
import React from 'react'
import { Text, View } from '@react-pdf/renderer'
import picassoStyles from './picassoStyles.js'
import CategoryLabel from '../CategoryLabel.jsx'

function Categories({ period }) {
  const camp = period.camp()
  return (
    <View style={picassoStyles.categories}>
      {camp.categories().items.map((category) => (
        <View style={picassoStyles.category}>
          <CategoryLabel category={category} />
          <Text>{category.name}</Text>
        </View>
      ))}
    </View>
  )
}

export default Categories
