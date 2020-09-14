import Events from './Events.svelte';

/*
example usage
const app = new Events({
	target: document.querySelector("#biws__events-list"),
	props: {
		params: {
			perPage: 5,
		},
		taxonomies: {
			biws__cat_tax: [],
		},
		filters: {
			selectTaxonomy: [
				{
					label: "Region",
					taxonomy: "biws__region_tax",
					selected: "",
				}
			]
		}
	}
});
*/

export default Events;