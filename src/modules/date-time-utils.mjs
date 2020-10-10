/**
 * Returns the integer value of the given value or 0 if it is not a number.
 *
 * @param {*} value The value to be parsed.
 * 
 * @return {number} The value as number or 0.
 */
const numberOr0 = (value) =>
  value == null || isNaN(value) ? 0 : parseInt(value),

  /**
   * Formats the given date to date string of format "weekday, dd.mm.yyyy"
   *
   * @param {Date} date The date to format.
   * 
   * @return {string} The formated date string.
   */
  getDateString = (date, locale = 'de-DE') =>
    date instanceof Date
      ? Intl.DateTimeFormat(locale, {
        weekday: 'long',
        year: 'numeric',
        month: 'numeric',
        day: 'numeric'
      }).format(date)
      : "",

  /**
   * Replaces the ":" in a time string by "." because of design choices...
   *
   * @param {string} time The time.
   * 
   * @return {string} The edited string.
   * 
   * @todo Consider checking the time string format and throwing an exception
   */
  getTimeString = (time) =>
    time ? time.toString().replace(":", ".") : "";


/**
 * Calculates a simple time value from a given time string by calculating
 * the corresponding seconds.
 * 
 * @param {string} timeString A time string in the format "HH:MM".
 * 
 * @return {(number|undefined)} The time strings total seconds.
 *                              Or undefined if timeString does not match the
 *                              expected format.
 */
export const getTimeValue = (timeString) => {
  // not string and not the right input format
  if (typeof timeString !== "string" || !/^\d{2}:\d{2}$/.test(timeString)) {
    return undefined;
  }
  const timeValues = timeString.split(":");
  return numberOr0(timeValues[0]) * 3600 + numberOr0(timeValues[1]) * 60;
};


/**
 * Returns a string in the format "<start><glue><end>".
 * Will omit start and/or end if not present.
 * 
 * @param {*}      start The start.
 * @param {*}      end   The end.
 * @param {string} glue  The string to glue start and end if end is present.
 * 
 * @return {string} A trimmed string in the format "<start><glue><end>".
 */
export const getStartToEnd = (start, end, glue = ' - ') =>
  `${start ? start : ""}${end ? `${glue}${end}` : ""}`.trim();

/**
 * Formats the given date to a shortened german month name.
 *
 * @param {Date} date The date to format.
 * 
 * @return {string} The shortened german month name.
 */
export const getShortMonthName = (date, locale = 'de-DE') =>
  date instanceof Date
    ? Intl.DateTimeFormat(locale, { month: 'short' }).format(date)
    : "";

/**
 * Helps to return a "nicely" formatted start to end date string.
 * 
 * @see getStartToEnd
 * @see getDateString
 * 
 * @param {Date}   start The start date.
 * @param {Date}   end   The end date.
 * @param {string} glue  The string to glue start and end if end is present.
 *
 * @return {string} A trimmed string in the format "<start><glue><end>".
 */
export const getStartToEndDateString = (start, end, glue = ' - ') =>
  getStartToEnd(
    getDateString(start),
    end && end > start ? getDateString(end) : undefined,
    glue
  );

/**
 * Helps to return a "nicely" formatted start to end time string.
 * 
 * @see getStartToEnd
 * @see getTimeString
 * 
 * @param {string} start The start time.
 * @param {string} end   The end time.
 * @param {string} glue  The string to glue start and end if end is present.
 *
 * @return {string} A string in the format "<start><glue><end>".
 */
export const getStartToEndTimeString = (start, end, glue = ' - ') =>
  start
    ? `${getStartToEnd(
      getTimeString(start),
      end && end > start ? getTimeString(end) : undefined
    )} Uhr`
    : "";

/**
 * Checks whether a given value is inclusively bewteen two other values.
 *
 * @param {*} check The value to check.
 * @param {*} from  The minimum value.
 * @param {*} to    The maximum value.
 * 
 * @return {boolean} true if check value is inclusively between from and to,
 *                   false otherwise
 */
export const isBetween = (check, from, to) => from <= check && check <= to;

/**
 * Checks wheter two given dates are equal based on the date values only.
 *
 * @param {Date} date  The date to check.
 * @param {Date} other The other date to check.
 * 
 * @return {boolean} True if the given dates are dates and the year, month and
 *                   day match, false otherwise.
 */
export const datesEqual = (date, other) =>
  date instanceof Date &&
  other instanceof Date &&
  date.getFullYear() === other.getFullYear() &&
  date.getMonth() === other.getMonth() &&
  date.getDate() === other.getDate();