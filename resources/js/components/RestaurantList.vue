<template>
  <div>
    <form @submit.prevent="searchRestaurants">
      <input type="text" v-model="keyword" placeholder="Enter a keyword">
      <button type="submit">Search</button>
    </form>
    <ul>
      <li v-for="restaurant in restaurants" :key="restaurant.id">{{ restaurant.name }}</li>
    </ul>
  </div>
</template>

<script>
export default {
  data() {
    return {
      keyword: '',
      restaurants: []
    };
  },
  methods: {
    searchRestaurants() {
      // Make an HTTP request to Laravel backend route to fetch restaurants based on the keyword
      axios.get('/restaurants', { params: { keyword: this.keyword } })
        .then(response => {
          this.restaurants = response.data;
        })
        .catch(error => {
          console.log(error);
        });
    }
  },
  created() {
    // Fetch initial list of restaurants (default keyword: Bang sue)
    this.searchRestaurants();
  }
};
</script>
