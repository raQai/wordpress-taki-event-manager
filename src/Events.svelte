<script context="module">
  // TODO not necessary yet but should be implemented for
  // my own sake to learn it..
  /*
  export async function preload({ per_page = 0, page = 0 } = {}) {
    const requestUrl = getRequestUrl({
      per_page: per_page,
      page: page,
    });
    const response = await fetch(requestUrl,
      {
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
        },
      }
    ).catch((error) =>
      console.error("Error:", error)
    );
    const data = await response.json();
    return {
      events: data,
      numPages: response.headers.get("X-QP-TotalPages"),
      totalEvents: response.headers.get("X-WP-Total"),
    };
  }
  */
</script>

<script>
  import { onMount } from "svelte";

  import Cache from "./modules/Cache.mjs";
  import TaxonomyUtils from "./modules/TaxonomyUtils.mjs";
  import FilterUtils from "./modules/FilterUtils.mjs";

  import EventItem from "./EventItem.svelte";
  import Pagination from "./pagination/Pagination.svelte";
  import Filters from "./filters/Filters.svelte";

  export let params = { perPage: -1 };
  export let taxonomies = {};
  export let filters = {};

  const apiUrl = __eventsApp.env.API_URL;

  let events = [];
  let filterSettings = {};
  let paginationSettings = {};
  let cache = new Cache();

  const getRequestUrl = (
    { perPage = -1 } = {},
    { active = -1 } = {},
    taxonomies = {},
    filters = []
  ) => {
    const esc = encodeURIComponent,
      url = `${apiUrl}biws__events`,
      queryParams = [];
    queryParams["posts_per_page"] = perPage;
    queryParams["paged"] = active;

    // add taxonomies to parameters
    Object.assign(queryParams, TaxonomyUtils.asQueryParams(taxonomies));

    // add filters to parameters
    Object.assign(queryParams, FilterUtils.asQueryParams(filters));

    const query = Object.keys(queryParams)
      .filter((k) => queryParams[k])
      .map((k) => `${esc(k)}=${queryParams[k]}`)
      .join("&");
    return `${url}${query ? `?${query}` : ""}`;
  };

  onMount(() => {
    filterSettings = FilterUtils.deserialize(filters);
  });

  $: if (
    cache.hasChanged("pagination", paginationSettings) ||
    cache.hasChanged("filters", filterSettings)
  ) {
    if (cache.hasChanged("filters", filterSettings)) {
      paginationSettings.active = 1;
      cache.put("pagination", paginationSettings);
    }
    cache.put("filters", filterSettings);

    fetch(
      getRequestUrl(params, paginationSettings, taxonomies, filterSettings),
      {
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
        },
      }
    )
      .then((response) => {
        if (!response.ok) {
          return Promise.reject(Error(response.statusText));
        }
        return response;
      })
      .then((response) => {
        paginationSettings.total = parseInt(
          response.headers.get("X-WP-TotalPages")
        );
        cache.put("pagination", paginationSettings);
        return response;
      })
      .then((response) => response.json())
      .then((data) => (events = data))
      .catch((error) => {
        events = [];
        paginationSettings.total = 1;
        cache.put("pagination", paginationSettings);
        cache.put("filters", filterSettings);
      });
  }
</script>

<Filters bind:filters={filterSettings} />
<Pagination
  bind:totalPages={paginationSettings.total}
  bind:activePage={paginationSettings.active} />
{#if !Array.isArray(events) || !events.length}
  <p>Keine Veranstaltungen angekündigt.</p>
{/if}
{#each events as event}
  <EventItem {event} />
{/each}
<Pagination
  bind:totalPages={paginationSettings.total}
  bind:activePage={paginationSettings.active} />
