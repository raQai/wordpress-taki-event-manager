export default class SelectFilter {
    /**
     * @param {string}           id       The id.
     * @param {(string|Promise)} label    The label to display.
     * @param {*}                selected The value of an option.
     * @param {Promise}          options  The options for this filter.
     */
    constructor({ id, label, selected, options } = {}) {
        if (!id || typeof id !== "string") {
            throw new Error("SelectFilter property 'id' is required.");
        }
        if (!label) {
            throw new Error("SelectFilter property 'label' is required.");
        }
        if (!(options instanceof Promise)) {
            throw new Error("SelectFilter property 'options' has be of type 'Promise'");
        }

        this.id = id;
        this.label = label;
        this.selected = selected;
        this.options = options;
    }

    /**
     * Selects the given value and stores it in this object.
     *
     * @param {*} value 
     */
    select(value) {
        this.selected = value;
    }
}
