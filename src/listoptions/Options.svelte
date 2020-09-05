<script>
  import Pagination from "./Pagination.svelte";
  import Filters from "./filters/Filters.svelte";

  export let paginationSettings = {};
  export let filterSettings = [];
  export let selectEventCallback = ({
    paginationSettings: paginationSettings,
    filterSettings: filterSettings,
  } = {}) => {};
</script>

<style>
  .options {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }
  .options > * {
    margin: 1rem 0;
  }
  @media (min-width: 500px) {
    .options > * {
      margin: 1rem;
    }
  }
  @media (min-width: 1000px) {
    .options {
      flex-direction: row;
    }
    .options > * {
      margin: 1rem;
    }
  }
</style>

<div class="options">
  {#if paginationSettings}
    <div class="pagination">
      <Pagination {paginationSettings} {selectEventCallback} />
    </div>
  {/if}
  {#if filterSettings}
    <div class="filters">
      <Filters {filterSettings} {selectEventCallback} />
    </div>
  {/if}
</div>
