<template>
  <section>
    <notifications position='bottom right'/>
    <AppHeader />
    <RouterView :isLoading="isLoading" />
  </section>
</template>

<script>
import AppHeader from "./components/AppHeader.vue";

export default {
  name: "App",
  components: {
    AppHeader,
  },
  data() {
    return {
      isLoading: false,
    };
  },
  created() {
    this.isLoading = true;
    fetch("http://localhost/bank/backend/index.php")
      .then((res) => {
        this.isLoading = false;
        console.log(res);
      })
      .catch((error) => {
        console.error("Error fetching data from backend:", error);
      });
  },
};
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  box-sizing: border-box;
  -moz-box-sizing: border-box;
  -webkit-box-sizing: border-box;
  -ms-box-sizing: border-box;
  text-align: center;
  color: #2c3e50;
  --primary: rgb(46, 61, 80);
}

body {
  margin: 0;
}

.content {
  padding-inline: 40px;
}
</style>
