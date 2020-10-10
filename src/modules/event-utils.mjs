import { asQueryParams } from "../modules/filter-utils.mjs";
import {
    datesEqual,
    getTimeValue,
    isBetween
} from "../modules/date-time-utils.mjs";

const apiUrl = __eventsApp.env.API_URL;

/**
 * Returns the request url for the given parameters.
 *
 * @param {object} params     The base params to build the request url for.
 * @param {number} activePage The page to query.
 * @param {Array}  filters    The filter settings.
 *
 * @return {!string} The request url.
 */
const getRequestUrl = (params = {}, activePage = -1, filters = []) => {
    const esc = encodeURIComponent,
        url = `${apiUrl}taki_events`,
        queryParams = [];

    for (let key in params) {
        queryParams[key] = params[key];
    }
    if (activePage > 1) {
        queryParams["paged"] = activePage;
    }

    // add filters to parameters
    Object.assign(queryParams, asQueryParams(filters));

    const query = Object.keys(queryParams)
        .filter((k) => queryParams[k])
        .map((k) => `${esc(k)}=${queryParams[k]}`)
        .join("&");
    return `${url}${query ? `?${query}` : ""}`;
};

/**
 * Fetches the events for the given parameters.
 * 
 * @param {object} params     The base params to build the request url for.
 * @param {number} activePage The page to query.
 * @param {Array}  filters    The filter settings.
 * 
 * @return {Promise} A promise returning an object of {events, totalPages}.
 */
export const fetchEvents = (
    params = {},
    activePage = -1,
    filterSettings = []) => {
    let totalPages = -1;
    return fetch(getRequestUrl(params, activePage, filterSettings), {
        headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
        },
    })
        .then((response) => {
            if (!response.ok) {
                return Promise.reject(Error(response.statusText));
            }
            return response;
        })
        .then((response) => {
            totalPages = parseInt(
                response.headers.get("X-WP-TotalPages")
            );
            return response;
        })
        .then((response) => response.json())
        .then((data) => {
            return {
                events: data,
                totalPages: totalPages
            }
        })
}

/**
 * Returns relevant date metadata for a given event.
 *
 * @param {object} event A fetched event.
 * 
 * @return {object} An object containing start, end and today dates.
 */
export const getDates = (event) => {
    if (!event.biws__datetime_meta) {
        return;
    }

    const startDate = event.biws__datetime_meta.datetime__start_date;
    const endDate = event.biws__datetime_meta.datetime__end_date;

    return {
        start: startDate ? new Date(Date.parse(startDate)) : undefined,
        end: endDate ? new Date(Date.parse(endDate)) : undefined,
    };
};

/**
 * Returns relevant time metadata for a given event.
 *
 * @param {object} event A fetched event.
 * 
 * @return {object} An object containing start, end and now times.
 */
export const getTimes = (event) => {
    const today = new Date();
    const now = `${today.getHours()}:${today.getMinutes()}`;

    if (!event.biws__datetime_meta) {
        return { now: now };
    }

    const startTime = event.biws__datetime_meta.datetime__start_time;
    const endTime = event.biws__datetime_meta.datetime__end_time;

    return {
        start: startTime,
        end: endTime,
        now: now,
    };
};

/**
 * Builds an array containgin all given location information
 * 
 * The Information is split based on the following groups
 * - group 1: name
 * - group 2: building
 * - group 3: street, street_nr
 * - group 4: zip, town
 *
 * @param {object} location The location to be deserialized.
 * 
 * @return {!string[]} An array containing all existing location information.
 */
const deserializeLocation = (location = {}) => {
    const output = [];
    output.push(location.name);
    output.push(location.building);
    output.push(
        [location.street, location.street_nr].filter(Boolean).join(" ")
    );
    output.push([location.zip, location.town].filter(Boolean).join(" "));

    if (!output.filter(Boolean).length) {
        return [];
    }

    return output.filter(Boolean).join(", $").split("$");
};

/**
 * Returns an array of location objects containing elements to be displayed.
 * 
 * @see deserializeLocation()
 * 
 * @param {object} event 
 * 
 * @return {Array} An array containing the location information to be displayed.
 */
export const getLocations = (event) => {
    if (!Array.isArray(event.biws__location_tax)) {
        return;
    }

    return event.biws__location_tax
        .map(location => {
            return {
                elements: deserializeLocation(location)
            }
        })
        .filter(location => location.elements.length);
}

/**
 * Determines whether the given start and end dates are today.
 * 
 * @param {Date} start The start date.
 * @param {Date} end   The end date.
 * 
 * @return {boolean} True if the start is today and no end is specified or if
 *                   both dates are today. False otherwise.
 */
export const isToday = ({ start, end } = {}) => {
    const today = new Date();
    return datesEqual(start, today) && (!end || datesEqual(end, today));
}

/**
 * Determines whether the current time is between the provided start and end
 * times.
 * 
 * @param {string} start The start time. 
 * @param {string} end   The end time.
 * 
 * @return {boolean} True if the current time is between start and end.
 *                   False otherwise.
 */
const isOngoingTime = ({ start, end } = {}) => {
    const now = new Date();
    const nowTimeString = `${now.getHours()}:${now.getMinutes()}`;

    return isBetween(
        getTimeValue(nowTimeString),
        getTimeValue(start),
        getTimeValue(end)
    );
}

/**
 * Determines whether the current time is beween the provided start and end
 * dates and times.
 * 
 * @param {object} dates The dates to check.
 * @param {object} times The times to check.
 * 
 * @return {boolean} True if the now is between the provided parameters.
 */
export const isOngoing = (dates = {}, times = {}) => {
    return (isToday(dates) && isOngoingTime(times)) ||
        (!isToday(dates) && isBetween(new Date(), dates.start, dates.end))
}