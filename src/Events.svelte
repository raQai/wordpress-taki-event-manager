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
  export let params = { perPage: -1 };
  export let taxonomies = {};

  const apiUrl = __eventsApp.env.API_URL;
  let events = [];
  let paginationSettings = {};

  const getRequestUrl = (
    { perPage = -1 } = {},
    { active = -1 } = {},
    taxonomies = {}
  ) => {
    const esc = encodeURIComponent,
      url = `${apiUrl}biws__events`,
      queryParams = [];
    queryParams["posts_per_page"] = perPage;
    queryParams["paged"] = active;
    if (taxonomies instanceof Object) {
      Object.keys(taxonomies)
        .filter((k) => taxonomies[k])
        .forEach((k) => {
          let values = taxonomies[k];
          if (typeof values === "string") {
            queryParams[k] = values;
          } else if (values.hasOwnProperty("length")) {
            queryParams[k] = values.join(",");
          }
        });
    }
    const query = Object.keys(queryParams)
      .filter((k) => queryParams[k])
      .map((k) => `${esc(k)}=${queryParams[k]}`)
      .join("&");
    return `${url}${query ? `?${query}` : ""}`;
  };

  $: fetch(getRequestUrl(params, paginationSettings, taxonomies), {
    headers: {
      "Content-Type": "application/json",
      Accept: "application/json",
    },
  })
    .then((response) => {
      if (!response.ok) {
        throw Error(response.statusText);
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
    .then((data) => (events = data));
</script>

<Pagination
  bind:totalPages={paginationSettings.total}
  bind:activePage={paginationSettings.active} />
{#each events as event}
  <EventItem {event} />
{/each}
