<template>
  <section class="content">
    <div class="exchange-container">
      <div class="exchange-title">
        <h3>Exchange rates</h3>
      </div>
      <div
        class="exchange"
        v-for="(currency, index) in currencies"
        :key="index"
      >
        <CurrencyDisplay
          @click="handleClick(currency)"
          v-if="currencyData[currency]"
          :title="currency"
          :short="currency"
          :value="
            +currencyData[currency][currencyData[currency].length - 1].obsValue
          "
          :diff="this.calcDiff(currencyData[currency])"
        />
      </div>
    </div>
  </section>
</template>

<script>
import CurrencyDisplay from "./CurrencyDisplay.vue";
import axios from "axios";
export default {
  components: {
    CurrencyDisplay,
  },
  data() {
    return {
      currencyData: {},
      currencies: ["USD", "EUR", "GBP"],
    };
  },
  created() {
    // Make an HTTP GET request to your backend API for each currency
    this.currencies.forEach((currency) => {
      axios
        .get(
          `http://localhost/bank/backend/api/get.php?currency=${currency}&start_date=2023-09-09&end_date=2023-10-10`
        )
        .then((response) => {
          this.currencyData[currency] = response.data;
        })
        .catch((error) => {
          console.error(`Error fetching ${currency} data:`, error);
        });
    });
  },
  methods: {
    calcDiff(arr) {
      const length = arr.length;
      if (length < 2) {
        return null;
      }
      const value1 = arr[length - 1].obsValue;
      const value2 = arr[length - 2].obsValue;
      const diff = value1 - value2;
      const percentageDiff = (diff / value2) * 100;
      const fixed = percentageDiff.toFixed(2);
      if (fixed < 1) return `-${fixed}`;
      return `+${fixed}`;
    },
    handleClick(currency) {
      this.$router.push({name: 'Stats', query: { currency}})
    },
  },
};
</script>

<style>
.exchange-container {
  padding-top: 20px;
  width: 300px;
  margin-inline: auto;
}

.exchange {
  padding: 5px;
  border-top: 1px solid rgb(196, 196, 196);
}

.exchange:hover {
  cursor: pointer;
  color: white;
  background-color: var(--primary);
  transition: 300ms;
}

</style>