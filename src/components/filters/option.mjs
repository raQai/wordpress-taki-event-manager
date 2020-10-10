export default class Option {
    /**
     * @param {*}      value The value for this option
     * @param {string} label The label to display.
     *                       Will be == value if not provided.
     */
    constructor({ value, label } = {}) {
        if (!value && value !== 0) {
            throw new Error("Option 'value' is required.");
        }
        this.value = value;
        this.label = label ? label.toString() : value.toString();
    }
}