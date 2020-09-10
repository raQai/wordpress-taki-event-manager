<script>
  import Child from "./Child.svelte";

  let paginationSettings = {};
  $: console.log(paginationSettings);
</script>

<Child bind:settings={paginationSettings}/>