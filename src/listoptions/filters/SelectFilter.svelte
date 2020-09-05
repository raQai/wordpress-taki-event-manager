<script>
  export let selectFilterSettings = {
    type: "select",
    label: "label",
    items: new Map(), // items in the select filter as key->value pairs
    selected,
  };
</script>

<style>
  label {
    flex-shrink: 0;
    margin: 0 1rem 0 0;
    font-size: 1.4rem;
    line-height: 3.2rem;
  }
  select {
    flex-grow: 1;
    height: 3.2rem;
    width: 100%;
    font-size: 1.2rem;
    border: none;
    padding: 0.7rem;
    background-color: var(--taki-grey1);
    -moz-appearance: none;
    appearance: none;
    cursor:pointer;
  }
  option {
    color: #222;
  }
  @media (min-width: 600px) {
    label {
      margin: 0 1rem;
    }
  }
</style>

{#if selectFilterSettings.items}
  <label
    for="select-{selectFilterSettings.label
      .toString()
      .toLocaleLowerCase()}">{selectFilterSettings.label.toString()}</label>
  <select id="select-{selectFilterSettings.label.toString().toLowerCase()}">
    <option
      style="color:#aaa"
      selected={!selectFilterSettings.items.has(selectFilterSettings.select)}>
      --- Alle ---
    </option>
    {#each [...selectFilterSettings.items.keys()] as key}
      <option selected={key == selectFilterSettings.selected} {key}>
        {selectFilterSettings.items.get(key)}
      </option>
    {/each}
  </select>
{/if}
