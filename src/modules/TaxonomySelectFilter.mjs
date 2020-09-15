import TaxonomyUtils from "./TaxonomyUtils.mjs";

export default class TaxonomySelectFilter {
    constructor({ taxonomy, label, selected }) {
        if (!taxonomy && typeof taxonomy !== "string") {
            throw new Error(
                "Please add the taxonomy slug as 'taxonomy' to the parameter object"
            );
        }
        if (!label && typeof label !== "string") {
            throw new Error(
                "Please add the display label as 'label' to the parameter object"
            );
        }
        this.id = taxonomy;
        this.label = label;
        this.selected = selected;
        this.options = this.fetchOptions(taxonomy)
    }

    async fetchOptions(id) {
        const options = [];
        await TaxonomyUtils.fetch(id)
            .then((data) => {
                data.forEach((term) => {
                    if (term && term.name && term.slug) {
                        options.push({ value: term.slug, label: term.name });
                    }
                });
            })
            .catch((error) => console.error(error));
        return options;
    }
}
