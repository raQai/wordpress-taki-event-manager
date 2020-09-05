<script>
  export let paginationSettings = {
    total, // num total pages
    active, // current active page
    maxItems, // maximum pagination items to display
  };
  export let selectEventCallback = ({
    paginationSettings: paginationSettings,
  }) => {};

  let pagination = [];

  const updateSettingsAndCall = ({ active = 1 } = {}) => {
      paginationSettings.active = active;
      selectEventCallback({ paginationSettings: paginationSettings });
    },
    getPagesArray = (numPages) =>
      Array.from(Array(parseInt(numPages)), (_, i) => i + 1),
    middleValue = ({ a, b, c }) =>
      a + b + c - Math.min(a, b, c) - Math.max(a, b, c),
    getPaginationArray = ({ total = 0, active = 0, maxItems = 7 } = {}) => {
      const pagesArray = getPagesArray(total);
      if (total <= maxItems) return pagesArray;
      const preferredNumPagesBoundary = (maxItems - 1) >> 1, // remove first and last element and the actual element and divide by 2
        leftUpperBoundary = active - total + maxItems - 1,
        leftLowerBoundary = active - 1,
        rightUpperBoundary = maxItems - active,
        rightLowerBoundary = total - active,
        leftBoundary =
          active -
          middleValue({
            a: preferredNumPagesBoundary,
            b: leftUpperBoundary,
            c: leftLowerBoundary,
          }),
        rightBoundary =
          active +
          middleValue({
            a: preferredNumPagesBoundary - (maxItems % 2) + 1,
            b: rightUpperBoundary,
            c: rightLowerBoundary,
          });

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

  $: pagination = getPaginationArray(paginationSettings);
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

{#if pagination.length > 1}
  <div class="pagination">
    <!--simplified mobile pagination-->
    <ul class="mobile">
      <li>
        <button
          aria-label="Erste Seite"
          class="arrow"
          disabled={paginationSettings.active === 1}
          on:click={() => updateSettingsAndCall({ active: 1 })}>&laquo;</button>
      </li>
      <li>
        <button
          aria-label="Eine Seite zurück"
          class="arrow"
          disabled={paginationSettings.active === 1}
          on:click={() => updateSettingsAndCall({
              active: paginationSettings.active - 1,
            })}>&lsaquo;</button>
      </li>
      <li>
        <span class="active xoftotal">{paginationSettings.active} von {paginationSettings.total}</span>
      </li>
      <li>
        <button
          aria-label="Eine Seite vor"
          class="arrow"
          disabled={paginationSettings.active === paginationSettings.total}
          on:click={() => updateSettingsAndCall({
              active: paginationSettings.active + 1,
            })}>&rsaquo;</button>
      </li>
      <li>
        <button
          aria-label="Letzte Seite"
          class="arrow"
          disabled={paginationSettings.active === paginationSettings.total}
          on:click={() => updateSettingsAndCall({
              active: paginationSettings.total,
            })}>&raquo;</button>
      </li>
    </ul>
    <!--regular pagination-->
    <ul class="full">
      <li>
        <button
          aria-label="Eine Seite zurück"
          class="arrow"
          disabled={paginationSettings.active === 1}
          on:click={() => updateSettingsAndCall({
              active: paginationSettings.active - 1,
            })}>&lsaquo;</button>
      </li>
      {#each pagination as pageNum}
        <li>
          <button
            aria-label="Gehe zu Seite {pageNum}"
            disabled={typeof pageNum === 'string'}
            class={paginationSettings.active === pageNum ? 'active' : ''}
            on:click={() => updateSettingsAndCall({
                active: pageNum,
              })}>{pageNum}</button>
        </li>
      {/each}
      <li>
        <button
          aria-label="Eine Seite vor"
          class="arrow"
          disabled={paginationSettings.active === paginationSettings.total}
          on:click={() => updateSettingsAndCall({
              active: paginationSettings.active + 1,
            })}>&rsaquo;</button>
      </li>
    </ul>
  </div>
{/if}
