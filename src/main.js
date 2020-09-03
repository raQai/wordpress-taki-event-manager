import App from './App.svelte';

const app = new App({
	target: document.querySelector("#biws__events-list"),
	props: {
		params: {
			per_page: 1,
		}
	}
});

export default app;