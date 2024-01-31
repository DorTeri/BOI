<template>
  <section class="home-container">
    <div class="hero-container">
      <div class="hero-text">
        <div class="hero-text-main">
          <h1>Bank Of Israel</h1>
          <p>
            Contributing to the prosperity of Israel and the welfare of its
            citizens
          </p>
        </div>
        <div class="hero-content">
          <div class="content-inner">
            <p>
              Periodic report on the prices of common banking services for
              households – first half of 2023
            </p>
            <p>28/01/2024</p>
            <p>
              The Banking Supervision Department today submitted to the Knesset
              Economics Committee the periodic report on the prices of common
              banking services to households for the first half of 2023.
            </p>
          </div>
          <DynamicComponent
            rate="4.5%"
            title="BOI Interest rate"
            description="Next decision date: 26/02/2024"
          />
          <DynamicComponent
            rate="3.0%"
            title="Inflation"
            description="12-month percentage change"
          />
        </div>
      </div>
    </div>
    <div>
      <ExchangeRates />
    </div>
  </section>
</template>

<script>
import ExchangeRates from "../components/ExchangeRates.vue";
import DynamicComponent from "../components/HeroCalc.vue";
export default {
  name: "Home-view",
  components: {
    ExchangeRates,
    DynamicComponent,
  },
};
</script>

<style>
.hero-container {
  width: 100%;
  height: 510px;
  display: flex;
  justify-content: left;
  align-items: center;
  position: relative;
  background-image: url("https://images.unsplash.com/photo-1538548537250-bbf3a59bc365?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTh8fGlzcmFlbHxlbnwwfDB8MHx8fDA%3D");
  background-size: cover;
  background-position: center;
}

.hero-text {
  display: flex;
  flex-direction: column;
  width: 70%;
  height: 100%;
  text-align: left;
  align-self: flex-start;
  color: var(--primary);
  font-size: 3.5rem;
}

.hero-text-main {
  width: 50%;
}

.hero-text h1 {
  margin-bottom: 20px;
}

.hero-text p {
  font-size: 2.3rem;
  margin-top: 0;
}

.hero-content {
  display: flex;
  background-color: var(--primary);
  color: white;
  width: 90%;
  margin-top: auto;
}

.content-inner {
  padding: 20px;
}

.hero-content p {
  font-size: 1.2rem;
}

.hero-content p:first-child {
  margin-bottom: 3px;
}

.hero-content p:nth-child(2) {
  color: rgb(207, 207, 207);
}

@media screen and (max-width: 768px) {
  .hero-text {
    padding-inline: 20px; /* Adjust as needed for small screens */
  }
}

/* Apply padding for medium screens */
@media screen and (min-width: 769px) and (max-width: 1024px) {
  .hero-text {
    padding-inline: 50px; /* Adjust as needed for medium screens */
  }
}

/* Apply padding for large screens */
@media screen and (min-width: 1025px) {
  .hero-text {
    padding-inline: 200px; /* Adjust as needed for large screens */
  }

  .hero-content {
    width: 95%;
  }
}

@media screen and (min-width: 1500px) {
  .hero-text {
    padding-inline: 300px; /* Adjust as needed for large screens */
  }
}
</style>