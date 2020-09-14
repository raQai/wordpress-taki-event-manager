import { serializeValue } from "./TaxonomyUtils.mjs";
import TaxonomySelectFilter from "./TaxonomySelectFilter.mjs";

export const FilterTypes = {
    SELECT_TAXONOMY: "selectTaxonomy"
}

export default {
    deserialize: (filters) => {
        const filterSettings = {};
        Object.keys(filters).forEach((key) => {
            switch (key) {
                case FilterTypes.SELECT_TAXONOMY:
                    if (Array.isArray(filters[key])) {
                        filters[key].forEach((filter) => {
                            const filterObject = new TaxonomySelectFilter(filter);
                            if (filterObject) {
                                if (!Array.isArray(filterSettings.selectTaxonomy)) {
                                    filterSettings.selectTaxonomy = [];
                                }
                                filterSettings.selectTaxonomy.push(filterObject);
                            }
                        });
                    }
                    break;
                default:
                    break;
            }
        });
        return filterSettings;
    },

    asQueryParams: (filters) => {
        const params = [];
        if (!(filters instanceof Object)) {
            return params;
        }

        Object.keys(filters)
            .filter((filterType) => filters[filterType])
            .forEach((filterType) => {
                const filter = filters[filterType];
                switch (filterType) {
                    case FilterTypes.SELECT_TAXONOMY:
                        filter.forEach((item) => {
                            let param = serializeValue(item.selected);
                            if (param) {
                                params[item.id] = param;
                            }
                        });
                        break;
                    default:
                        throw Error(`Unsupported filter type ${filterType}`);
                }
            });
        return params;
    }
}