<script>
  export let totalPages;
  export let activePage;

  const setActivePage = (pageNum) => {
      activePage = pageNum;
    },
    firstPage = () => {
      setActivePage(1);
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
    },
    lastPage = () => {
      if (isNaN(totalPages)) {
        return;
      }
      setActivePage(totalPages);
    };
</script>

<style>
  .small-pagination {
    font-size: 1rem;
    display: flex;
    flex-wrap: nowrap;
    list-style: none;
    margin: 0;
    padding: 0;
    height: 2.8rem;
    gap: 0.3em;
    justify-content: center;
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
    font-size: 1.5em;
  }

  .xoftotal {
    font-family: sans-serif;
    font-size: 1em;
    white-space: nowrap !important;
    word-wrap: normal !important;
    width: auto;
    padding: 0 1rem;
    display: inline-block;
    height: 100%;
    line-height: 3.2rem;
  }
</style>

{#if totalPages > 1}
  <ul class="small-pagination">
    <li class="navigation-item">
      <button
        aria-label="Erste Seite"
        class="arrow-button navigation-button"
        disabled={activePage <= 1}
        on:click={firstPage}>&laquo;</button>
    </li>
    <li class="navigation-item">
      <button
        aria-label="Eine Seite zurück"
        class="arrow-button navigation-button"
        disabled={activePage <= 1}
        on:click={previousPage}>&lsaquo;</button>
    </li>
    <li class="navigation-item">
      <span class="active xoftotal">{activePage} von {totalPages}</span>
    </li>
    <li class="navigation-item">
      <button
        aria-label="Eine Seite vor"
        class="arrow-button navigation-button"
        disabled={activePage >= totalPages}
        on:click={nextPage}>&rsaquo;</button>
    </li>
    <li class="navigation-item">
      <button
        aria-label="Letzte Seite"
        class="arrow-button navigation-button"
        disabled={activePage >= totalPages}
        on:click={lastPage}>&raquo;</button>
    </li>
  </ul>
{/if}
