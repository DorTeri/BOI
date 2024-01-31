<template>
  <div class="content-container">
    <h2>{{ rate }}</h2>
    <hr />
    <h4>{{ title }}</h4>
    <p class="desc">{{ description }}</p>
  </div>
</template>

<script>
export default {
  name: "HeroCalc",
  props: {
    rate: {
      type: String,
      required: true,
    },
    title: {
      type: String,
      required: true,
    },
    description: {
      type: String,
      required: true,
    },
  },
};
</script>

<style >

.content-container {
    height: 100%;
    width: 300px;
    text-align: center;
    padding-inline: 20px;
    background-color: #3e5268;
}

.content-container h2 {
    font-size: 1.8rem;
    font-weight: 400;
    margin-top: 12px;
    margin-bottom: 0;
}

.content-container h4 {
    font-weight: 400;
    font-size: 1.2rem;
    margin-bottom: 4px;
}

.content-container .desc {
    font-size: 1rem;
    color: rgb(199, 199, 199);
    font-weight: 300;
}

.content-container hr {
    width: 60%;
    margin-block: 0;
    margin-top: 5px;
}
</style>
