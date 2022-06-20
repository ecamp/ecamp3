import dayjs from '@/../common/helpers/dayjs.js'

export default (_, inject) => {
  inject('date', dayjs)
}
