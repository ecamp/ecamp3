import { StyleSheet } from '@react-pdf/renderer'

const tocStyles = StyleSheet.create({
  entry: {
    display: 'flex',
    flexDirection: 'row',
    justifyContent: 'space-between',
  },
  subEntry: {
    marginLeft: '10pt',
    display: 'flex',
    flexDirection: 'row',
    justifyContent: 'space-between',
  },
})

export default tocStyles
