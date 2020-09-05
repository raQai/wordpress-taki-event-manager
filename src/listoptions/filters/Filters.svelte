<script>
  import SelectFilter from "./SelectFilter.svelte";

  export let filterSettings = [];
  export let selectEventCallback = ({ filterSettings: filterSettings }) => {};
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
  {#each filterSettings as filterItem}
    {#if filterItem.type === 'select'}
      <SelectFilter selectFilterSettings={filterItem} />
    {/if}
  {/each}
</div>
