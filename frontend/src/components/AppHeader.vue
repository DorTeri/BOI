<template>
  <nav class="nav content">
    <div class="logo" @click="handleLogoClick">
      <img
        class="logo-image"
        src="../assets/images/boi-logo.png"
        alt="boi-logo"
      />
      <h2 class="logo-text">Bank Of Israel</h2>
    </div>
    <div class="nav-items">
      <div class="nav-item" v-for="(item, index) in items" :key="index">
        <div class="nav-item" :class="{ active: isItemActive(item) }" @click="handleNavigation(item)">
          {{ item }}
        </div>
      </div>
    </div>
  </nav>
</template>

<script>
export default {
  name: "app-header",
  data() {
    return {
      items: ["USD", "EUR", "GBP"],
    };
  },
  methods: {
    handleLogoClick() {
      this.$router.push({ name: "Home" });
    },
    handleNavigation(currency) {
      this.$router.push({ name: "Stats", query: { currency } });
    },
    isItemActive(item) {
      return this.$route.query.currency === item;
    }
  },
};
</script>

<style>
.nav {
  display: flex;
  justify-content: space-between;
  height: 90px;
  border-bottom: 5px solid #b8c4d5;
}

.nav-items {
  display: flex;
  align-items: center;
}

.nav-item {
  padding-inline: 10px;
  cursor: pointer;
}

.nav-item:hover {
  color: rgb(39, 194, 255);
}

.nav-item:last-child {
  padding-right: 0px;
}

.logo {
  cursor: pointer;
  display: flex;
  align-items: center;
}

.logo-image {
  width: 80px;
  height: 80px;
  padding-right: 15px;
}

.logo-text {
  font-weight: 400;
}

.active {
  color: rgb(39, 194, 255);
}
</style>