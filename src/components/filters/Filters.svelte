<script>
  import SelectFilter from "./SelectFilter.svelte";
  export let filters = {};
</script>

<style>
  .filters {
    opacity: 0.7;
    margin: 0;
    width: 100%;
    display: grid;
    grid-template-columns: max-content auto;
    row-gap: 1rem;
    column-gap: 2rem;
    padding: 1rem;
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
  {#each Object.keys(filters) as filterType}
    {#if filterType === 'selectTaxonomy'}
      {#each filters[filterType] as settings}
        <SelectFilter
          id={settings.id}
          label={settings.label}
          options={settings.options}
          bind:selected={settings.selected} />
      {/each}
    {/if}
  {/each}
</div>
