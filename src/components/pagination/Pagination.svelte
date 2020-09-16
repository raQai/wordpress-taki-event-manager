<script>
  import FullPagination from "./FullPagination.svelte";
  import SmallPagination from "./SmallPagination.svelte";

  const defaults = {
    total: 1,
    active: 1,
    maxItems: 7,
  };

  export let totalPages = defaults.total; // num total pages
  export let activePage = defaults.active; // current active page
  export let maxItems = defaults.maxItems; // maximum pagination items to display

  $: if (activePage > totalPages) {
    activePage = totalPages;
  } else if (activePage < 1) {
    activePage = 1;
  }
</script>

<style>
  .pagination {
    margin: 1rem;
  }

  .small-pagination {
    display: inline-block;
  }

  .only-show-on-mobile {
    display: block;
  }

  .full-pagination {
    display: none;
  }

  @media (min-width: 350px) {
    .only-show-on-mobile {
      display: none;
    }

    .full-pagination {
      display: block;
    }
  }
</style>

<div class="pagination">
  <!--simplified mobile pagination-->
  <div class="small-pagination" class:only-show-on-mobile={maxItems > 5}>
    <SmallPagination {totalPages} bind:activePage />
  </div>
  <!--regular pagination-->
  {#if maxItems > 5}
    <div class="full-pagination">
      <FullPagination {totalPages} {maxItems} bind:activePage />
    </div>
  {/if}
</div>
