<script>
  export let id; // unique id for the filter to reference to
  export let label; // label of the filter
  export let selected; // selected value
  export let options; // Promise returning object array [{value:<value>, label:<label>},];

  let optionData;

  $: if (options) {
    options.then((opt) => (optionData = opt));
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
  <label for="select-{id}">{label}</label>
  <select id="select-{id}" bind:value={selected}>
    <option style="color:#999" value="">--- Alle ---</option>
    {#each optionData as option}
      {#if option && option.value && option.label}
        <option value={option.value}>{option.label}</option>
      {/if}
    {/each}
  </select>
{/if}
