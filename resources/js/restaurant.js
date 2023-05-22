require('./bootstrap');

import Vue from 'vue';
import Restaurant from './components/Restaurant.vue';

const app = new Vue({
  el: '#app',
  components: {
    Restaurant,
  },
});
