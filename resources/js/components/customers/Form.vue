<script setup>
import { ref, reactive, watch, onMounted } from 'vue';
import axios from 'axios';
import { useRouter, useRoute } from 'vue-router';

// Using the Vue Router
const router = useRouter();
const route = useRoute();

// Emitting the 'close' event to parent to close the modal when done
const emit = defineEmits(['close', 'refreshList']); // 'refreshList' will be emitted to update the customer list

// Props received from the parent component (whether it's in edit mode and the customer ID if editing)
const props = defineProps({
  editMode: Boolean, // Determine if it's edit mode or create mode
  customerId: Number, // The customer ID to be edited
});

// Reactive form data for binding form input values
const form = reactive({
  name: '',           // Customer name
  reference: '',      // Customer reference code
  category_id: '',    // Customer category ID
  start_date: '',     // Start date for the customer
  description: ''     // Additional description for the customer
});

const categories = ref([]);  // Categories fetched from the API
const errors = ref([]);      // Validation errors from the backend

// Fetch categories from the API
const fetchCategories = async () => {
  try {
    const response = await axios.get('/api/categories');
    categories.value = response.data; // Store the fetched categories
  } catch (error) {
    console.error('Error fetching categories:', error);
  }
};

// Fetch customer details by ID (used when in edit mode)
const fetchCustomer = async (id) => {
  try {
    const response = await axios.get(`/api/customers/${id}`);
    Object.assign(form, response.data); // Populate the form with the fetched customer data
  } catch (error) {
    console.error('Error fetching customer:', error);
  }
};

// Watch for changes in the customerId prop and fetch customer details when in edit mode
watch(() => props.customerId, (newCustomerId) => {
  if (props.editMode && newCustomerId) {
    fetchCustomer(newCustomerId); // Fetch customer data for editing
  }
});

// Handle form submission (either to create a new customer or update an existing one)
const handleSave = () => {
  const saveAction = props.editMode
    ? axios.put(`/api/customers/${props.customerId}`, form) // Update existing customer
    : axios.post('/api/customers', form); // Create new customer

  saveAction
    .then(() => {
      emit('close'); // Close modal after successful save
      emit('refreshList'); // Refresh the customer list

      toast.fire({
        icon: 'success',
        title: props.editMode ? 'Customer updated successfully' : 'Customer added successfully'
      });
    })
    .catch((error) => {
      // Handle validation errors
      if (error.response && error.response.status === 422) {
        errors.value = error.response.data.errors; // Capture validation errors
      } else {
        console.error('Error saving customer:', error); // Handle other errors
        Swal.fire({
          title: 'Error',
          text: 'An error occurred while saving the customer. Please try again.',
          icon: 'error'
        });
      }
    });
};

// Initialize categories and fetch customer details if in edit mode
onMounted(() => {
  fetchCategories(); // Always fetch categories
  if (props.editMode && props.customerId) {
    fetchCustomer(props.customerId); // Fetch customer details if in edit mode
  }
});
</script>

<template>
  <h1 class="text-xl font-bold mb-4">Customer Details</h1>

  <div class="modal bg-white p-6 rounded-lg shadow-lg mx-auto mt-10 relative max-w-5xl w-full">
    

    <!-- Form to create or update a customer -->
    <form @submit.prevent="handleSave">
      <div class="flex justify-between items-center mb-4">
        <!-- Text "Add Customer" or "Edit Customer" -->
        <h1 class="text-xl font-bold">{{ editMode ? 'Edit' : 'Add' }} Customer</h1>

        <!-- Form Actions (Save or Cancel) -->
        <div class="space-x-2">
          <button
            type="button"
            class="bg-gray-300 hover:bg-gray-400 text-black font-bold py-2 px-4 rounded"
            @click="$emit('close')"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
          >
            Save
          </button>
        </div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- General Customer Information -->
        <div>
          <h2 class="text-lg font-semibold mb-4">General</h2>

          <!-- Name Field -->
          <div class="mb-4">
            <label class="block text-gray-700">Name</label>
            <input type="text" v-model="form.name" class="w-full border rounded p-2" />
            <span v-if="errors.name" class="text-red-500">{{ errors.name[0] }}</span>
          </div>

          <!-- Reference Field -->
          <div class="mb-4">
            <label class="block text-gray-700">Reference</label>
            <input type="text" v-model="form.reference" class="w-full border rounded p-2" />
            <span v-if="errors.reference" class="text-red-500">{{ errors.reference[0] }}</span>
          </div>

          <!-- Category Selection -->
          <div class="mb-4">
            <label class="block text-gray-700">Category</label>
            <select v-model="form.category_id" class="w-full border rounded p-2">
              <option value="" disabled>Select Category</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
            <span v-if="errors.category_id" class="text-red-500">{{ errors.category_id[0] }}</span>
          </div>
        </div>

        <!-- Additional Details -->
        <div>
          <h2 class="text-lg font-semibold mb-4">Details</h2>

          <!-- Start Date Field -->
          <div class="mb-4">
            <label class="block text-gray-700">Start Date</label>
            <input type="date" v-model="form.start_date" class="w-full border rounded p-2" />
            <span v-if="errors.start_date" class="text-red-500">{{ errors.start_date[0] }}</span>
          </div>

          <!-- Description Field -->
          <div class="mb-4">
            <label class="block text-gray-700">Description</label>
            <textarea v-model="form.description" class="w-full border rounded p-2"></textarea>
            <span v-if="errors.description" class="text-red-500">{{ errors.description[0] }}</span>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>
