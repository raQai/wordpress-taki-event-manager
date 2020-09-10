<script context="module">
</script>

<script>
  export const settings = { count: 0 };
  let increase = () => {
    settings.count += 1;
  };
</script>

<button on:click={increase}>+</button>
<p>click count: {settings.count}</p>
