import { StyleSheet } from '@react-pdf/renderer'

const pageBorder = 40
const marginFontSize = 10

const styles = StyleSheet.create({
  page: {
    fontFamily: 'OpenSans',
    padding: pageBorder,
    fontSize: 12,
    display: 'flex',
    flexDirection: 'column',
  },
  section: {
    margin: 10,
    padding: 10,
  },
  h1: {
    fontSize: 16,
    fontWeight: 'semibold',
    marginBottom: 4,
  },
  h2: {
    fontSize: 14,
    fontWeight: 'semibold',
    marginBottom: 4,
  },
  h3: {
    fontSize: 12,
    fontWeight: 'semibold',
    marginBottom: 4,
  },
  header: {
    fontSize: marginFontSize,
    textAlign: 'center',
    width: '100%',
    position: 'absolute',
    top: 0,
    left: 0,
    right: 0,
    marginTop: pageBorder / 2,
    marginHorizontal: pageBorder,
  },
  footer: {
    fontSize: marginFontSize,
    textAlign: 'center',
    width: '100%',
    position: 'absolute',
    bottom: 0,
    left: 0,
    right: 0,
    marginBottom: pageBorder / 2,
    marginHorizontal: pageBorder,
  },
  categoryLabel: {
    padding: '0 8pt 2pt',
    borderRadius: '50%',
    marginBottom: 0,
    alignSelf: 'center',
  },
})

export default styles
