<script>
  import Option from "./option.mjs";

  import SelectFilter from "./select-filter.mjs";

  /* SelectFilter */
  export let selectFilter;

  /* {?Option[]} */
  let optionData;
  /* {string} */
  let label;
  let selected;

  $: if (selectFilter.label instanceof Promise) {
    selectFilter.label.then((result) => (label = result));
  } else {
    label = selectFilter.label;
  }

  $: if (selectFilter && selectFilter.options) {
    selectFilter.options.then((options) => (optionData = options));
  }

  $: if (selectFilter) {
    selectFilter.select(selected);
  }
</script>

<style>
  label {
    white-space: nowrap;
    font-size: 1.4rem;
    line-height: 3.2rem;
    margin: 0 -1rem 0 0;
  }
  select {
    width: 100%;
    height: 3.2rem;
    font-size: 1.2rem;
    border: none;
    padding: 0.7rem;
    background-color: #eee;
    cursor: pointer;
  }
  option {
    color: #222;
  }
  @media (min-width: 1000px) {
  }
</style>

{#if Array.isArray(optionData) && optionData.length}
  <label for="select-{selectFilter.id}">{label}</label>
  <select id="select-{selectFilter.id}" bind:value={selected}>
    <option style="color:#999" value="">--- Alle ---</option>
    {#each optionData as option}
      {#if option instanceof Option}
        <option value={option.value}>{option.label}</option>
      {/if}
    {/each}
  </select>
{/if}
