<template>
  <section class="stats-container">
    <h2 class="title">
      {{ this.$route.query.currency }}
    </h2>
    <div class="date-container">
      <VueDatePicker
        v-model="date"
        range
        multi-calendars
        @update:model-value="handleDateChange"
      />
    </div>
    <apexchart
      width="700"
      type="line"
      :options="options"
      :series="series"
    ></apexchart>
  </section>
</template>

<script>
import axios from "axios";

export default {
  name: "Stats-view",
  components: {},
  data() {
    return {
      date: [],
      options: {
        chart: {
          id: "vuechart-example",
        },
        xaxis: {
          type: "datetime",
          categories: [],
        },
      },
      series: [
        {
          name: this.$route.query.currency,
          data: [],
        },
      ],
      limits: {
        USD: 3.8,
        EUR: 4.1,
        GBP: 4.6,
      },
    };
  },
  created() {
    this.fetchData();
    const startDate = "2023-02-01";
    const endDate = "2023-11-11";
    this.date = [startDate, endDate];
  },
  methods: {
    handleDateChange() {
      let startFormattedDate = "2023-01-01";
      let endFormattedDate = "2024-01-01";
      if (this.date[0] && this.date[1]) {
        startFormattedDate = this.date[0].toISOString().slice(0, 10);
        endFormattedDate = this.date[1].toISOString().slice(0, 10);
      }
      this.fetchData(startFormattedDate, endFormattedDate);
      this.alert();
    },
    fetchData(startDate = "2023-01-01", endDate = "2024-01-01") {
      this.series[0].data = [];
      this.options.xaxis.categories = [];
      const currency = this.$route.query.currency;
      axios
        .get(
          `http://localhost/bank/backend/api/get.php?currency=${currency}&start_date=${startDate}&end_date=${endDate}`
        )
        .then((response) => {
          response.data.forEach((data) => {
            this.series[0].data.push(data.exchange_rate);
            this.options.xaxis.categories.push(data.date_time);
          });
          this.alert();
        })
        .catch((error) => {
          console.error(`Error fetching ${currency} data:`, error);
        });
    },
    alert() {
      const currency = this.$route.query.currency;
      const dataLength = this.series[0].data.length;
      const lastPrice = this.series[0].data[dataLength - 1];
      let message = "";
      let type = "";
      if (this.limits[currency] > lastPrice) {
        message = `Good news! the price of ${currency} is lower than usual`;
        type = "success";
      } else if (this.limits[currency] < lastPrice) {
        message = `Bad news! the price of ${currency} is higher than usual`;
        type = "error";
      } else {
        message = `Hey! the price of ${currency} is still the same`;
        type = "info";
      }
      this.$notify({
        title: `${currency} price alert`,
        text: message,
        type,
      });
    },
  },
  watch: {
    "$route.query": {
      handler: function (newQuery, oldQuery) {
        if (newQuery && oldQuery) {
          if (newQuery.currency !== oldQuery.currency) {
            this.fetchData();
          }
        }
      },
      immediate: true,
    },
  },
};
</script>

<style>
.stats-container {
  padding-top: 50px;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.title {
  color: var(--primary);
}
</style>