<script>
  import SelectFilter from "./SelectFilter.svelte";

  export let filterSettings = [];
  export let selectEventCallback = ({ filterSettings: filterSettings }) => {};
</script>

<style>
  .wrapper {
    display: flex;
    flex-direction: column;
    opacity: 0.7;
    margin:0;
  }
  .filter {
    display: flex;
    flex-direction: row;
    overflow: hidden;
    margin: 0 0 1rem;
  }
  .select {
    margin-right:-1.1rem;
  }
  .select::after {
    content: "\25BC";
    position: relative;
    right: 2.1rem;
    top: 0.7rem;
    font-size: 1.2rem;
  }
  @media (min-width: 600px) {
    .wrapper {
      flex-direction: row;
      flex-wrap: wrap;
    }
    .filter {
      flex-basis: 40%;
      flex-grow: 1;
    }
  }
  @media (min-width: 1000px) {
    .filter {
      flex-basis: auto;
      flex-grow: 0;
    }
  }
</style>

<div class="wrapper">
  {#each filterSettings as filterItem}
    {#if filterItem.type === 'select'}
      <div class="filter select">
        <SelectFilter selectFilterSettings={filterItem} />
      </div>
    {/if}
  {/each}
</div>
