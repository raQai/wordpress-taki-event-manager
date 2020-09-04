<script>
  import Pagination from "./Pagination.svelte";
  export let paginationSettings = {};
  export let selectEventCallback = ({
    paginationSettings: paginationSettings,
  } = {}) => {};
</script>

<style>
  .options {
    margin: 1rem 0;
  }
  @media (min-width: 350px) {
    .options {
      margin: 1rem;
    }
  }
</style>

<div class="options">
  <div class="pagination">
    <Pagination {paginationSettings} {selectEventCallback} />
  </div>
</div>
