<template>
  <div class="currency-display">
    <p>{{ title }}</p>
    <p>{{ short }}</p>
    <p>{{ value }}</p>
    <p :class="{ positive: isPositive }">{{ diff }}%</p>
  </div>
</template>

<script>
export default {
  name: "CurrencyDisplay",
  components: {},
  props: {
    title: {
      type: String,
      required: true,
    },
    value: {
      type: Number,
      required: true,
    },
    short: {
      type: String,
      required: true,
    },
    diff: {
      type: Number,
      required: true,
    },
  },
  computed: {
    isPositive() {
      return this.diff > 1;
    },
  },
};
</script>

<style>
.currency-display {
  display: flex;
  justify-content: space-between;
}

.currency-display p:last-child {
  color: red;
}

.currency-display p:last-child.positive {
  color: rgb(1, 226, 1);
}
</style>