import SelectFilter from "../components/filters/select-filter.mjs";
import Option from "../components/filters/option.mjs";

const apiUrl = __eventsApp.env.API_URL;

/**
 * Returns the request url for a given taxonomy using the apiUrl provided
 * by the environment.
 * 
 * @param {!string} taxonomy The taxonomy to build the request url for.
 * @return {!string} The request url.
 */
const getRequestUrl = (taxonomy) => taxonomy && `${apiUrl}${taxonomy}`;

/**
 * Fetches the label for a given taxonomy.
 *
 * @param {!string} taxonomy The taxonomies slug to feetch the label for.
 * @param {?string} label    The label if one is provided.
 *
 * @return {(string|Promise)} The promise to resolve the taxonomies label.
 */
const fetchLabel = (taxonomy, label) => {
    if (typeof label === "string" && !!label) {
        return label;
    }

    return fetch(getRequestUrl(taxonomy), {
        headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
        },
    })
        .then((response) =>
            response.ok
                ? response
                : Promise.reject(new Error(response.statusText))
        )
        .then((response) => response.json())
        .then(data => {
            return (data.slug == taxonomy && !!data.label)
                ? data.label
                : taxonomy;
        })
        .catch((error) => console.error(error));
}

/**
 * Fetches the terms of a given taxonomy and returns a Promise for an array of
 * options.
 * 
 * @see Option
 * 
 * @param {!string}   taxonomy The taxonomies slug to fetch the options for.
 * @param {?string[]} options  An array of slugs or null/undefined.
 *                             If slugs are provided, the feteched options
 *                             will be filtered by the provided slugs.
 * 
 * @return {Promise} The promise to resolve the terms as options.
 */
const fetchTerms = (taxonomy, options) => {
    return fetch(getRequestUrl(taxonomy), {
        headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
        },
    })
        .then((response) =>
            response.ok
                ? response
                : Promise.reject(new Error(response.statusText))
        )
        .then((response) => response.json())
        .then(data => {
            if (!Array.isArray(data.terms)) {
                return;
            }
            return data.terms
                .filter(term => term.slug && term.name)
                .filter(term => !Array.isArray(options) ||
                    options.includes(term.slug))
                .map(term => new Option({
                    value: term.slug,
                    label: term.name
                }))
        })
        .catch((error) => console.error(error));
}

export default class TaxonomySelectFilter extends SelectFilter {
    /**
     * @param {!string}   taxonomy The taxonomy this filter is referenced to.
     * @param {?string}   label    The label to display. May be null/undefined.
     * @param {*}         selected The value of an option.
     * @param {?string[]} options  An array of slugs or null/undefined.
     *                             If slugs are provided, the feteched options
     *                             will be filtered by the provided slugs.
     */
    constructor({ taxonomy, label, selected, options } = {}) {
        super({
            id: taxonomy,
            label: fetchLabel(taxonomy, label),
            selected: selected,
            options: fetchTerms(taxonomy, options)
        });

        this.filterOptions = options;
    }
}
