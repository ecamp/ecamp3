export default (option, dayjsClass, dayjsFactory) => {
  // Callable as follows: dayjs.formatDatePeriod(start, end, format, locale)
  dayjsFactory.formatDatePeriod = (start, end, format, _ /* locale */) => {
    if (format === '') return ''
    // TODO implement intelligent shortening, e.g. in German, you can write
    //   Fr 14. - So 16.04.2023 instead of Fr 14.04.2023 - So 16.04.2023.
    //   But careful, the same may or may not be possible in other locales.
    //   E.g. in English dates like 4/17/2023, it may not be possible to leave
    //   out the date. Proper research on this topic is needed.
    const startFormatted = start.format(format)
    const endFormatted = end.format(format)
    return `${startFormatted} - ${endFormatted}`
  }
}
