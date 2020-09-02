<script context="module">
  /*
  // TODO not necessary yet but should be implemented for
  // my own sake to learn it..
  let events;

  export async function preload({ params }) {
    const response = await this.fetch(`${apiUrl}events`);
    const json = await response.json();

    events = json;
  }
  */
</script>

<script>
  import EventItem from "./EventItem.svelte";
  import { onMount } from "svelte";

  const apiUrl = __eventsApp.env.API_URL;

  let events = [];
  console.log(apiUrl);

  onMount(async () => {
    const res = await fetch(`${apiUrl}biws__events`, {
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
    });
    events = await res.json();
  });
</script>

{#if !events}
  <p>Termine werden geladen</p>
{:else}
  {#each events as event}
    {#if !event.content.protected}
      <EventItem {event} />
    {/if}
  {/each}
{/if}
