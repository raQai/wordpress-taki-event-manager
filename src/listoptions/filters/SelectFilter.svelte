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
    white-space: nowrap;
    font-size: 1.4rem;
    line-height: 3.2rem;
    margin-right:-1rem;
  }
  .select {
    width: 100%;
    height: 3.2rem;
    display: flex;
    flex-direction: row;
    flex-wrap: nowrap;
  }
  select {
    width: 100%;
    height: 3.2rem;
    font-size: 1.2rem;
    border: none;
    padding: 0.7rem;
    background-color: var(--taki-grey1);
    -moz-appearance: none;
    appearance: none;
    cursor: pointer;
  }
  .select::after {
    content: "\25BC";
    position: relative;
    font-size: 1.2rem;
    width:0;
    top:.8rem;
    right:2.4rem;
    pointer-events: none;
  }
  option {
    color: #222;
  }
  @media (min-width: 1000px) {
  }
</style>

{#if selectFilterSettings.items}
  <label
    for="select-{selectFilterSettings.label
      .toString()
      .toLocaleLowerCase()}">{selectFilterSettings.label.toString()}</label>
  <div class="select">
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
  </div>
{/if}
