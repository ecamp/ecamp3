import { ColorSpace, sRGB } from 'colorjs.io/fn'

export default {
  install: () => {
    ColorSpace.register(sRGB)
  },
}
