<script context="module">
  const numberOr0 = (value) =>
    value != null && !isNaN(value) ? parseInt(value) : 0;
  export const getWeekDayName = (day = 0) =>
    [
      "Sonntag",
      "Montag",
      "Dienstag",
      "Mittwoch",
      "Donnerstag",
      "Freitag",
      "Samstag",
      "Sonntag",
    ][day];
  export const getShortMonthName = (month = 0) =>
    [
      "Jan",
      "Feb",
      "Mär",
      "Apr",
      "Mai",
      "Jun",
      "Jul",
      "Aug",
      "Sep",
      "Okt",
      "Nov",
      "Dez",
    ][month];
  export const getDateString = (date) =>
    date instanceof Date
      ? `${getWeekDayName(date.getDay())}, ${Intl.DateTimeFormat(
          "de-DE"
        ).format(date)}`
      : "";
  export const getTimeString = (time) =>
    time ? time.toString().replace(":", ".") : "";
  export const getStartToEnd = (start, end) =>
    `${start ? start : ""}${end ? ` - ${end}` : ""}`;
  export const getStartToEndDateString = (start, end) =>
    getStartToEnd(
      getDateString(start),
      end && end > start ? getDateString(end) : undefined
    );
  export const getStartToEndTimeString = (start, end) =>
    start
      ? `${getStartToEnd(
          getTimeString(start),
          end && end > start ? getTimeString(end) : undefined
        )} Uhr`
      : "";
  export const getTimeValue = (timeString) => {
    if (!(typeof timeString === "string" && timeString.includes(":"))) {
      return undefined;
    }
    let timeValues = timeString.split(":");
    if (timeValues.length != 2) {
      return undefined;
    }
    return numberOr0(timeValues[0]) * 3600 + numberOr0(timeValues[1]) * 60;
  };
  export const isBetween = (check, from, to) => from <= check && check <= to;
  export const datesEqual = (date, other) =>
    date &&
    other &&
    date instanceof Date &&
    other instanceof Date &&
    date.getFullYear() === other.getFullYear() &&
    date.getMonth() === other.getMonth() &&
    date.getDay() === other.getDay();
  export const isToday = (today, startDate, endDate) =>
    datesEqual(startDate, today) && (!endDate || datesEqual(endDate, today));
  export const isOngoingTime = (now, start, end) =>
    now &&
    start &&
    end &&
    isBetween(getTimeValue(now), getTimeValue(start), getTimeValue(end));
  export const isOngoing = (
    today,
    startDate,
    endDate,
    now,
    startTime,
    endTime
  ) =>
    (isToday(today, startDate, endDate) &&
      startTime &&
      endTime &&
      isOngoingTime(now, startTime, endTime)) ||
    (!isToday(today, startDate, endDate) &&
      isBetween(today, startDate, endDate));
</script>

<script>
  import { log } from "console";
</script>
