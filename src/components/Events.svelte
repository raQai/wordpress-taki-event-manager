<script context="module">
  // TODO preload filters and filter options
  // not necessary yet but should be implemented for
  // my own sake to learn it..
</script>

<script>
  import { onMount } from "svelte";

  import { fetchEvents } from "../modules/event-utils.mjs";
  import { asFilterSettings } from "../modules/filter-utils.mjs";

  import Loader from "./loader/Loader.svelte";
  import EventItem from "./EventItem.svelte";
  import Pagination from "./pagination/Pagination.svelte";
  import Filters from "./filters/Filters.svelte";

  export let params = {};
  export let filters = [];

  let events = [];

  let filterSettings = [];

  let activePage = -1;
  let totalPages = -1;

  let state = {
    filterSettings: filterSettings,
    activePage: activePage,
  };

  let loading = true;

  onMount(() => {
    // parse filter settings on mount
    filterSettings = asFilterSettings(filters);
  });

  // update event and pagination related settings if filter or page changed.
  $: {
    const pageChanged = state.activePage !== activePage,
      filterString = JSON.stringify(filterSettings),
      filtersChanged = JSON.stringify(state.filterSettings) != filterString;

    // reset active page if the filter settings changed
    if (filtersChanged) {
      activePage = 1;
    }

    // only fetch new events if the page or filter settings changed
    if (pageChanged || filtersChanged) {
      loading = true;

      state.filterSettings = JSON.parse(filterString);
      state.activePage = activePage;

      fetchEvents(params, activePage, filterSettings)
        .then((result) => {
          events = result.events;
          totalPages = result.totalPages;
          loading = false;
        })
        .catch((error) => {
          events = [];
          totalPages = 1;
          loading = false;
        });
    }
  }
</script>

<Filters bind:filters={filterSettings} />
<Pagination {totalPages} bind:activePage />
<Loader {loading} />
{#if !Array.isArray(events) || !events.length}
  <p>Keine Veranstaltungen angekündigt.</p>
{/if}
{#each events as event}
  <EventItem {event} />
{/each}
<Loader {loading} />
<Pagination {totalPages} bind:activePage />
