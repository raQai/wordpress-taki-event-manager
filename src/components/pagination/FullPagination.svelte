<script>
  import { getPages } from "./pagination.mjs";

  export let totalPages;
  export let activePage;
  export let maxItems;

  const setActivePage = (pageNum) => {
      activePage = pageNum;
    },
    previousPage = () => {
      if (isNaN(activePage)) {
        return;
      }
      setActivePage(activePage - 1);
    },
    nextPage = () => {
      if (isNaN(activePage)) {
        return;
      }
      setActivePage(activePage + 1);
    };

  $: pages = getPages(totalPages, activePage, maxItems);
</script>

<style>
  .full-pagination {
    font-size: 1rem;
    display: flex;
    flex-wrap: nowrap;
    list-style: none;
    margin: 0;
    padding: 0;
    height: 3.2rem;
    gap: 0.3em;
  }

  .navigation-item {
    flex: 0 0;
    margin: 0;
  }

  .navigation-button {
    font-weight: normal;
    padding: 0;
    margin: 0;
    background: #eee;
    color: initial;
    font-size: 1em;
    overflow: hidden;
    overflow-wrap: normal;
    word-break: keep-all;
    text-align: center;
    width: 2.6rem;
    height: 100%;

    -webkit-transition: background-color 0.25s;
    transition: background-color 0.25s;
  }

  .navigation-button:disabled {
    opacity: 50%;
    background-color: initial !important;
    text-decoration: none !important;
    cursor: default;
  }

  .navigation-button:not(:disabled):hover,
  .active {
    color: #fff;
    background-color: #db3b0f;
    text-decoration: none;
  }

  .arrow-button {
    font-size: 2em;
  }
</style>

<ul class="full-pagination">
  <li class="navigation-item">
    <button
      aria-label="Eine Seite zurück"
      class="arrow-button navigation-button"
      disabled={activePage <= 1}
      on:click={previousPage}>&lsaquo;</button>
  </li>
  {#each pages as pageNum}
    <li class="navigation-item">
      <button
        aria-label="Gehe zu Seite {pageNum}"
        disabled={typeof pageNum === 'string'}
        class="navigation-button"
        class:active={activePage === pageNum}
        on:click={setActivePage(pageNum)}>{pageNum}</button>
    </li>
  {/each}
  <li class="navigation-item">
    <button
      aria-label="Eine Seite vor"
      class="arrow-button navigation-button"
      disabled={activePage >= totalPages}
      on:click={nextPage}>&rsaquo;</button>
  </li>
</ul>
