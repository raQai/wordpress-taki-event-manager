<script>
  const defaults = {
    total: 1,
    active: 1,
    maxItems: 7,
  };

  export let totalPages = defaults.total; // num total pages
  export let activePage = defaults.active; // current active page
  export let maxItems = defaults.maxItems; // maximum pagination items to display

  const getPagesArray = (numPages) => {
      return Array.from(Array(parseInt(numPages)), (_, i) => i + 1);
    },
    middleValue = (a, b, c) =>
      a + b + c - Math.min(a, b, c) - Math.max(a, b, c);

  const setActivePage = (pageNum) => {
      activePage = pageNum;
    },
    firstPage = () => {
      activePage = 1;
    },
    previousPage = () => {
      if (isNaN(activePage)) {
        return;
      }
      activePage -= 1;
    },
    nextPage = () => {
      if (isNaN(activePage)) {
        return;
      }
      activePage += 1;
    },
    lastPage = () => {
      if (isNaN(totalPages)) {
        return;
      }
      activePage = totalPages;
    },
    getPages = (total, active, maxItems) => {
      const pagesArray = getPagesArray(total);
      if (total <= maxItems) return pagesArray;
      const preferredNumPagesBoundary = (maxItems - 1) >> 1, // remove first and last element and the actual element and divide by 2
        leftUpperBoundary = active - total + maxItems - 1,
        leftLowerBoundary = active - 1,
        rightUpperBoundary = maxItems - active,
        rightLowerBoundary = total - active,
        leftBoundary =
          active -
          middleValue(
            preferredNumPagesBoundary,
            leftUpperBoundary,
            leftLowerBoundary
          ),
        rightBoundary =
          active +
          middleValue(
            preferredNumPagesBoundary - (maxItems % 2) + 1,
            rightUpperBoundary,
            rightLowerBoundary
          );

      const filtered = pagesArray.filter(
        (k) => k === 1 || k === total || (k > leftBoundary && k < rightBoundary)
      );
      if (active > preferredNumPagesBoundary + 1) {
        filtered[1] = "...";
      }
      if (active < total - preferredNumPagesBoundary) {
        filtered[filtered.length - 2] = "...";
      }
      return filtered;
    };

  $: ensureValidSettings = (function (total, active) {
    if (active > total) {
      active = total;
    } else if (active < 1) {
      active = 1;
    }
  })(totalPages, activePage);
  $: pages = getPages(totalPages, activePage, maxItems);
</script>

<style>
  .pagination {
    margin: 1rem 0;
  }

  ul {
    display: flex;
    flex-wrap: nowrap;
    list-style: none;
    margin: 0;
    padding: 0;
    height: 3.2rem;
  }

  .mobile {
    justify-content: center;
  }

  .full {
    display: none;
  }

  li {
    margin: 0 0.3rem;
    flex: 0 0;
  }

  li:first-of-type {
    margin-left: 0;
  }

  li:last-of-type {
    margin-right: 0;
  }

  button {
    font-weight: normal;
    padding: 0;
    margin: 0;
    background: var(--taki-grey1);
    color: initial;
    font-size: 1rem;
    overflow: hidden;
    overflow-wrap: normal;
    word-break: keep-all;
    text-align: center;
    width: 2.6rem;
    height: 100%;

    -webkit-transition: background-color 0.25s;
    transition: background-color 0.25s;
  }

  .xoftotal {
    font-family: sans-serif;
    font-size: 1.2rem;
    display: block;
    white-space: nowrap;
    word-wrap: normal;
    width: auto;
    line-height: 3.2rem;
    padding: 0 1rem;
  }

  button.arrow {
    font-size: 2rem;
  }

  button:not(:disabled):hover,
  .active {
    color: #fff;
    background-color: var(--taki-red);
    text-decoration: none;
  }

  button:disabled {
    opacity: 25%;
    background-color: initial !important;
    text-decoration: none !important;
    cursor: default;
  }

  @media (min-width: 350px) {
    .pagination {
      margin: 1rem;
    }

    .mobile {
      display: none;
    }

    .full {
      display: flex;
    }
  }
</style>

{#if pages && pages.length > 1}
  <div class="pagination">
    <!--simplified mobile pagination-->
    <ul class="mobile">
      <li>
        <button
          aria-label="Erste Seite"
          class="arrow"
          disabled={activePage <= 1}
          on:click={firstPage}>&laquo;</button>
      </li>
      <li>
        <button
          aria-label="Eine Seite zurück"
          class="arrow"
          disabled={activePage <= 1}
          on:click={previousPage}>&lsaquo;</button>
      </li>
      <li>
        <span class="active xoftotal">{activePage} von {totalPages}</span>
      </li>
      <li>
        <button
          aria-label="Eine Seite vor"
          class="arrow"
          disabled={activePage >= totalPages}
          on:click={nextPage}>&rsaquo;</button>
      </li>
      <li>
        <button
          aria-label="Letzte Seite"
          class="arrow"
          disabled={activePage >= totalPages}
          on:click={lastPage}>&raquo;</button>
      </li>
    </ul>
    <!--regular pagination-->
    <ul class="full">
      <li>
        <button
          aria-label="Eine Seite zurück"
          class="arrow"
          disabled={activePage <= 1}
          on:click={previousPage}>&lsaquo;</button>
      </li>
      {#each pages as pageNum}
        <li>
          <button
            aria-label="Gehe zu Seite {pageNum}"
            disabled={typeof pageNum === 'string'}
            class={activePage === pageNum ? 'active' : ''}
            on:click={() => (activePage = pageNum)}>{pageNum}</button>
        </li>
      {/each}
      <li>
        <button
          aria-label="Eine Seite vor"
          class="arrow"
          disabled={activePage >= totalPages}
          on:click={nextPage}>&rsaquo;</button>
      </li>
    </ul>
  </div>
{/if}
