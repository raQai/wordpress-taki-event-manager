<script>
  import SelectFilter from "./SelectFilter.svelte";
  import SelectFilterClass from "./select-filter.mjs";
  export let filters = [];
</script>

<style>
  .filters {
    opacity: 0.7;
    margin: 1rem;
    width: calc(100% - 2rem);
    display: grid;
    grid-template-columns: max-content auto;
    row-gap: 1rem;
    column-gap: 2rem;
  }

  @media (min-width: 600px) {
    .filters {
      grid-template-columns: max-content auto max-content auto;
    }
  }

  @media (min-width: 1200px) {
    .filters {
      grid-template-columns: max-content auto max-content auto max-content auto;
    }
  }
</style>

<div class="filters">
  {#each filters as filter}
    {#if filter instanceof SelectFilterClass}
      <SelectFilter bind:selectFilter={filter} />
    {/if}
  {/each}
</div>
