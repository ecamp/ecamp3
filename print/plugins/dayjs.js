import dayjs from '@/../common/helpers/dayjs.js'

export default ({ app }, inject) => {
  inject('date', dayjs)
}
