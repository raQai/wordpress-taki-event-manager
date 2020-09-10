<script>
  export let label;
  export let taxonomy;
  export let selected;

  const apiUrl = __eventsApp.env.API_URL;
  const getRequestUrl = (taxonomy) => taxonomy && `${apiUrl}${taxonomy}`;

  let values = [];

  $: fetch(getRequestUrl(taxonomy), {
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
    .then((response) => response.json())
    .then((data) => (values = data));
</script>

<style>
  label {
    white-space: nowrap;
    font-size: 1.4rem;
    line-height: 3.2rem;
    margin-right: -1rem;
  }
  .select {
    width: 100%;
    position: relative;
  }
  select {
    width: 100%;
    height: 3.2rem;
    font-size: 1.2rem;
    border: none;
    padding: 0.7rem;
    background-color: var(--taki-grey1);
    -moz-appearance: none;
    appearance: none;
    cursor: pointer;
  }
  .select::after {
    content: "\25BC";
    position: absolute;
    font-size: 1.2rem;
    top: 0.75rem;
    right: 1.3rem;
    pointer-events: none;
    opacity: 0.6;
  }
  option {
    color: #222;
  }
  @media (min-width: 1000px) {
  }
</style>

{#if values}
  <label for="select-{taxonomy}">{label}</label>
  <div class="select">
    <select id="select-{taxonomy}" bind:value={selected}>
      <option style="color:#aaa" value="" selected={!selected}>
        --- Alle ---
      </option>
      {#each values as value}
        <option value={value.slug}>{value.name}</option>
      {/each}
    </select>
  </div>
{/if}
