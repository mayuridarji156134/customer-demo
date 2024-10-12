import { createApp } from 'vue';
import Swal from 'sweetalert2';
import App from './App.vue'; 
import CustomerList from './components/customers/Index.vue';
import router from './router'
import '../css/app.css';  


window.Swal = Swal
const toast = Swal.mixin({
	toast:true,
	position:'top-end',
	showConfirmButton: false,
	timer: 3000,
	timerProgressBar: true,
})

window.toast= toast
// Mount the Vue instance and render the component
createApp(App).use(router).mount('#app');
