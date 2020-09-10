import Events from './Events.svelte';

const app = new Events({
	target: document.querySelector("#biws__events-list"),
	props: {
		params: {
			perPage: 5,
		},
		taxonomies: {
			"biws__cat_tax": "treffpunkt"
		},
	}
});

export default app;