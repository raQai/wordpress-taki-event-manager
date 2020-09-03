<script>
  export let paginationSettings = {
    total, // num total pages
    active, // current active page
    maxItems, // maximum pagination items to display
  };
  export let selectEventCallback = ({ pageSettings: paginationSettings }) => {};

  let pagination = [];

  const updateSettingsAndCall = ({ active = 1 } = {}) => {
      paginationSettings.active = active;
      selectEventCallback({ pageSettings: paginationSettings });
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
  ul {
    display: flex;
    flex-wrap: wrap;
    list-style: none;
    margin: 0;
    padding: 0;
    height: 2.2rem;
  }

  li {
    margin: 0 0.2rem;
    flex: 1 1;
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
    width: 100%;
    height: 100%;

    -webkit-transition: background-color 0.25s;
    transition: background-color 0.25s;
  }

  button.arrow {
    font-size: 2rem;
  }

  button:not(:disabled):hover,
  button.active {
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
    ul {
      height: 3.2rem;
    }
    li {
      flex: 0 0;
    }
    button {
      width: 2.6rem;
      text-align: center;
    }
  }
</style>

{#if pagination.length > 1}
  <ul>
    {#if paginationSettings.total > pagination.length}
      <li>
        <button
          class="arrow"
          disabled={paginationSettings.active === 1}
          on:click={() => updateSettingsAndCall({
              active: paginationSettings.active - 1,
            })}>&lsaquo;</button>
      </li>
    {/if}
    {#each pagination as pageNum}
      <li>
        <button
          disabled={typeof pageNum === 'string'}
          class={paginationSettings.active === pageNum ? 'active' : ''}
          on:click={() => updateSettingsAndCall({
              active: pageNum,
            })}>{pageNum}</button>
      </li>
    {/each}
    {#if paginationSettings.total > pagination.length}
      <li>
        <button
          class="arrow"
          disabled={paginationSettings.active === paginationSettings.total}
          on:click={() => updateSettingsAndCall({
              active: paginationSettings.active + 1,
            })}>&rsaquo;</button>
      </li>
    {/if}
  </ul>
{/if}
