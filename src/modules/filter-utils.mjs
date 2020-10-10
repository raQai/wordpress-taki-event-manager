import TaxonomySelectFilter from "./taxonomy-select-filter.mjs";
import SelectFilter from "../components/filters/select-filter.mjs";

export const FilterTypes = {
    SELECT_TAXONOMY: "selectTaxonomy"
}

/**
 * Parses the filters and creates corresponding filter objects.
 *
 * @param {string[]} filters The filters as encoded json strings.
 * 
 * @return {!Array} An array of the parsed filter objects.
 * 
 * @throws {Error} If any filter element references a filter of unknown type.
 */
export const asFilterSettings = (filters = []) => {
    if (!Array.isArray(filters)) {
        return [];
    }

    return filters.map(filter => {
        let filterObject = {};
        try {
            filterObject = JSON.parse(filter);
        } catch {
            filterObject.taxonomy = filter;
        }

        // set default filter type
        if (!filterObject.type) {
            filterObject.type = FilterTypes.SELECT_TAXONOMY;
        }

        switch (filterObject.type) {
            case FilterTypes.SELECT_TAXONOMY:
                return new TaxonomySelectFilter(filterObject);
            default:
                throw new Error(`Filter type '${filterObject.type}' not supported.`);
        }
    });
}

/**
 * Creates a query parameter array for the given filters.
 * 
 * @see asFilterSettings()
 * 
 * @param {Array} filterSettings An array of filter objects.
 *
 * @return {!Array} An array usable for rest queries.
 */
export const asQueryParams = (filterSettings) => {
    const params = [];

    if (!Array.isArray(filterSettings)) {
        return params;
    }

    filterSettings.forEach(settings => {
        if (settings instanceof SelectFilter) {
            if (settings.selected) {
                params[settings.id] = settings.selected;
            }
        }
        if (settings instanceof TaxonomySelectFilter) {
            if (!settings.selected && Array.isArray(settings.filterOptions)) {
                params[settings.id] = settings.filterOptions.join(',');
            }
        }
    });

    return params;
}