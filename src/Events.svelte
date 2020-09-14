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
  import EventItem from "./EventItem.svelte";
  import Pagination from "./listoptions/Pagination.svelte";
  import Filters from "./listoptions/filters/Filters.svelte";
  export let params = { perPage: -1 };
  export let taxonomies = {};
  export let filters = {};

  const apiUrl = __eventsApp.env.API_URL;
  let events = [];
  let paginationSettings = {};
  let cache = {};

  const cacheObject = (key, obj) => {
      if (!key || !obj) {
        return;
      }
      cache[key] = JSON.stringify(obj);
    },
    cacheHasChanged = (key, obj) => {
      return JSON.stringify(obj) !== cache[key];
    },
    addTaxonomy = (obj, taxonomy, values) => {
      if (!values) {
        return;
      }
      if (Array.isArray(values)) {
        return (obj[taxonomy] = values.join(","));
      }
      return (obj[taxonomy] = values.toString());
    },
    getRequestUrl = (
      { perPage = -1 } = {},
      { active = -1 } = {},
      taxonomies = {},
      filters = {}
    ) => {
      const esc = encodeURIComponent,
        url = `${apiUrl}biws__events`,
        queryParams = [];
      queryParams["posts_per_page"] = perPage;
      queryParams["paged"] = active;

      // add taxonomies
      if (taxonomies instanceof Object) {
        Object.keys(taxonomies)
          .filter((taxonomy) => taxonomies[taxonomy])
          .forEach((taxonomy) =>
            addTaxonomy(queryParams, taxonomy, taxonomies[taxonomy])
          );
      }

      // add filters
      if (filters instanceof Object) {
        Object.keys(filters)
          .filter((filterType) => filters[filterType])
          .forEach((filterType) => {
            const filter = filters["selectTaxonomy"];
            switch (filterType) {
              case "selectTaxonomy":
                filter.forEach((item) => {
                  addTaxonomy(queryParams, item.taxonomy, item.selected);
                });
                break;
              default:
                throw Error(`Unsupported filter type ${filterType}`);
            }
          });
      }
      const query = Object.keys(queryParams)
        .filter((k) => queryParams[k])
        .map((k) => `${esc(k)}=${queryParams[k]}`)
        .join("&");
      return `${url}${query ? `?${query}` : ""}`;
    };

  $: if (
    cacheHasChanged("pagination", paginationSettings) ||
    cacheHasChanged("filters", filters)
  ) {
    if (cacheHasChanged("filters", filters)) {
      paginationSettings.active = 1;
    }
    cacheObject("pagination", paginationSettings);
    cacheObject("filters", filters);

    fetch(getRequestUrl(params, paginationSettings, taxonomies, filters), {
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
    })
      .then((response) => {
        if (!response.ok) {
          return Promise.reject(Error(response.statusText));
        }
        return response;
      })
      .then((response) => {
        const total = parseInt(response.headers.get("X-WP-TotalPages"));
        if (total != paginationSettings.total) {
          paginationSettings.total = total;
        }
        return response;
      })
      .then((response) => response.json())
      .then((data) => (events = data))
      .catch((error) => {
        events = [];
        paginationSettings.total = 1;
      });
  }
</script>

<Filters bind:filters />
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
