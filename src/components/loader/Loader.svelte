<script>
  export let loading;
</script>

<style>
  @-webkit-keyframes progress {
    0% {
      -webkit-transform: translateX(-100%);
    }
    100% {
      -webkit-transform: translateX(150%);
    }
  }

  @keyframes progress {
    0% {
      transform: translateX(-100%);
    }
    100% {
      transform: translateX(150%);
    }
  }

  .progress {
    height: 1px;
    overflow: hidden;
    margin: 0;
    opacity: 0;
    transition: opacity 0.2s;
  }

  .progress-bar {
    background-color: #db3b0f;
    height: 1px;
  }

  .loading {
    opacity: 1;
  }

  .loading .progress-bar {
    -webkit-animation: progress 1s linear infinite; /* Safari */
    animation: progress 1s linear infinite;
  }
</style>

<div class="progress" class:loading>
  <div class="progress-bar" />
</div>
