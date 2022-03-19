// eslint-disable-next-line no-unused-vars
import React from 'react'
import { Svg, Circle, Polygon } from '../reactPdf.js'

function Checkmark ({ size = 12 }) {
  const scale = size / 8
  return <Svg style={{ transform: `scale(${scale})`, marginTop: '1pt' }} height="8" width="8">
    <Circle
      cx="4"
      cy="4"
      r="4"
      fill="green"
    />
    <Polygon
      points="3.3,4.725 2.25,3.65 1.6,4.275 3.3,5.975 6.4,2.9 5.75,2.275"
      fill="white"
    />
  </Svg>
}

export default Checkmark
