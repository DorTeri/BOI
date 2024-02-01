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
        <div v-if="!isLoading">
          <CurrencyDisplay
            @click="handleClick(currency)"
            :title="currency"
            :value="getObsValue(currency)"
            :diff="getDiff(currency)"
          />
        </div>
        <div v-else>
          <div class="loader">Loading...</div>
        </div>
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
  props: {
    isLoading: {
      type: Boolean,
      required: false,
    },
  },
  data() {
    return {
      currencyData: {},
      currencies: ["USD", "EUR", "GBP"],
    };
  },
  created() {
    this.fetchCurrencyData();
  },
  methods: {
    fetchCurrencyData() {
      this.currencies.forEach((currency) => {
        axios
          .get(
            `http://localhost/bank/backend/api/get.php?currency=${currency}&start_date=2023-01-01&end_date=2024-01-01`
          )
          .then((response) => {
            if (typeof response.data === "object") {
              this.currencyData[currency] = response.data;
            }
          })
          .catch((error) => {
            console.error(`Error fetching ${currency} data:`, error);
          });
      });
    },
    getObsValue(currency) {
      const currencyData = this.currencyData[currency];
      console.log("currencyData", currencyData);
      if (currencyData && currencyData.length > 0) {
        return +currencyData[currencyData.length - 1].obsValue;
      }
      return null;
    },
    getDiff(currency) {
      const currencyData = this.currencyData[currency];
      if (currencyData && currencyData.length > 1) {
        const length = currencyData.length;
        const value1 = currencyData[length - 1].obsValue;
        const value2 = currencyData[length - 2].obsValue;
        const diff = value1 - value2;
        const percentageDiff = (diff / value2) * 100;
        const fixed = percentageDiff.toFixed(2);
        if (fixed < 1) return `-${fixed}`;
        return `+${fixed}`;
      }
      return null;
    },
    handleClick(currency) {
      this.$router.push({ name: "Stats", query: { currency } });
    },
  },
  watch: {
    isLoading(newValue) {
      if (!newValue) {
        this.fetchCurrencyData();
      }
    }
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