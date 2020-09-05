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
  import EventItem from "./EventItem.svelte";
  import Pagination from "./listoptions/Pagination.svelte";
  import Filters from "./listoptions/filters/Filters.svelte";

  import { onMount } from "svelte";

  export let params;

  let events = [],
    pageData = {
      active: 1,
    };

  const testSelectFilter = {
    type: "select",
    label: "Region",
    items: new Map(),
  };
  testSelectFilter.items.set(4, "Lorem, ipsum dolor");
  testSelectFilter.items.set(53, "sit amet consectetur");
  testSelectFilter.items.set(2, "adipisicing elit");
  testSelectFilter.items.set(5, "Vitae molestiae earum ullam");
  const test2SelectFilter = Object.assign({}, testSelectFilter);
  test2SelectFilter.label = "Another One";
  const test3SelectFilter = Object.assign({}, testSelectFilter);
  test3SelectFilter.label = "And Another One";

  const filterSettings = [];
  filterSettings.push(testSelectFilter);
  filterSettings.push(test3SelectFilter);
  filterSettings.push(test2SelectFilter);

  const apiUrl = __eventsApp.env.API_URL,
    route = "biws__events",
    getRequestUrl = ({ per_page = 0, page = 0 } = {}) => {
      const url = `${apiUrl}${route || ""}`,
        params = {
          posts_per_page: per_page,
          paged: page,
        },
        esc = encodeURIComponent,
        query = Object.keys(params)
          .filter((k) => params[k])
          .map((k) => `${esc(k)}=${params[k]}`)
          .join("&");
      return `${url}${query ? `?${query}` : ""}`;
    },
    fetchEventsData = async ({ paginationSettings = pageData } = {}) => {
      const requestUrl = getRequestUrl({
          per_page: params.per_page,
          page: paginationSettings.active,
        }),
        response = await fetch(requestUrl, {
          headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
          },
        });
      events = await response.json();
      pageData.total = parseInt(response.headers.get("X-WP-TotalPages"));
      pageData.active = paginationSettings.active;
    };

  onMount(() => fetchEventsData({ per_page: params.per_page }));
</script>

<style>
  :root {
    --taki-border-radius: 3px;
    --taki-red: #db3b0f;
    --taki-grey0: #fafafa;
    --taki-grey1: #eee;
    --taki-grey2: #e0e0e0;
    --taki-media-break-point-0: 500px;
  }
</style>

{#if !events}
  <p>Termine werden geladen...</p>
{/if}
<Filters {filterSettings} selectEventCallback={fetchEventsData} />
<Pagination
  paginationSettings={pageData}
  selectEventCallback={fetchEventsData} />
{#each events as event}
  <EventItem {event} />
{/each}
<Pagination
  paginationSettings={pageData}
  selectEventCallback={fetchEventsData} />
