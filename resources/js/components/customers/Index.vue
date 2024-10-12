<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import customerForm from './Form.vue'; // Import customer form component
import Modal from './Model.vue'; // Import Modal

// Reactive references for customers, contacts, and various states
const customers = ref([]);
const contacts = ref([]); // Store contacts for the selected customer
const links = ref([]); // Store pagination links
const categories = ref([]); // Store categories
const searchQuery = ref(''); // Search query input
const selectedCategory = ref(''); // Selected category for filtering
const showModal = ref(false); // Control modal visibility for customer
const editMode = ref(false); // Track if we are in edit or create mode
const currentCustomerId = ref(null); // Store the ID of the customer being edited
const contactForm = ref({ first_name: '', last_name: '' }); // Form data for contact
const isEditingContact = ref(false); // Track if we are editing a contact
const currentContactId = ref(null); // Store the ID of the contact being edited
const showContactModal = ref(false); // Control modal visibility for contact

// Fetch categories from the API
const fetchCategories = async () => {
  try {
    const response = await axios.get('/api/categories');
    categories.value = response.data;
  } catch (error) {
    console.error('Error fetching categories:', error);
  }
};

// Open modal for adding a contact
const addContact = () => {
  isEditingContact.value = false;
  contactForm.value = { first_name: '', last_name: '' }; // Reset form
  showContactModal.value = true; // Show modal for adding contact
};

// Open modal for editing a contact
const onEditContact = (contactId) => {
  const contact = contacts.value.find((contact) => contact.id === contactId);
  if (contact) {
    isEditingContact.value = true;
    currentContactId.value = contactId;
    contactForm.value = { first_name: contact.first_name, last_name: contact.last_name };
    showContactModal.value = true; // Show modal for editing contact
  }
};

const errors = ref({}); // Reactive object to store validation errors

// Save new contact or update existing contact
const saveContact = async () => {
  try {
    if (isEditingContact.value) {
      // Update contact
      await axios.put(`/api/contacts/${currentContactId.value}`, contactForm.value);
      Swal.fire({ title: 'Success', text: 'Contact updated successfully', icon: 'success' });
    } else {
      // Add new contact
      await axios.post(`/api/customers/${currentCustomerId.value}/contacts`, contactForm.value);
      Swal.fire({ title: 'Success', text: 'Contact added successfully', icon: 'success' });
    }
    fetchContacts(currentCustomerId.value); // Refresh contacts after saving
    showContactModal.value = false; // Close modal
    errors.value = {}; // Clear any previous validation errors
  } catch (error) {
    if (error.response && error.response.status === 422) {
      // Validation errors from the server
      errors.value = error.response.data.errors; // Assign errors to the reactive errors object
    } else {
      // Other errors
      console.error('Error saving contact:', error);
      Swal.fire({ title: 'Error', text: 'Error saving contact. Please try again.', icon: 'error' });
    }
  }
};


// Close contact modal
const closeContactModal = () => {
  showContactModal.value = false; // Close modal
};

// Fetch customers with search and category filters
const fetchCustomers = async (url = '/api/customers', query = {}) => {
  try {
    const params = {
      ...query, // Merge query parameters
      search: searchQuery.value, // Search query
      category_id: selectedCategory.value // Category filter
    };
    const response = await axios.get(url, { params });
    customers.value = response.data.customers.data;
    links.value = response.data.customers.links; // Set pagination links
  } catch (error) {
    console.error('Error fetching customers:', error);
  }
};

// Open modal for adding a customer
const newCustomer = () => {
  editMode.value = false; // Set to create mode
  currentCustomerId.value = null; // Reset customer ID
  showModal.value = true; // Show modal
  contacts.value = []; // Reset contacts when adding a new customer
};

// Open modal for editing a customer
const onEdit = async (id) => {
  editMode.value = true; // Set to edit mode
  currentCustomerId.value = id; // Set customer ID for editing
  showModal.value = true; // Open modal
  await fetchCustomer(id); // Fetch customer data for editing
  await fetchContacts(id); // Fetch contacts for the customer
};

// Fetch a single customer for edit mode
const fetchCustomer = async (id) => {
  try {
    const response = await axios.get(`/api/customers/${id}`);
    return response.data;
  } catch (error) {
    console.error('Error fetching customer:', error);
  }
};

// Fetch contacts for a specific customer
const fetchContacts = async (customerId) => {
  try {
    const response = await axios.get(`/api/customers/${customerId}/contacts`);
    contacts.value = response.data;
  } catch (error) {
    console.error('Error fetching contacts:', error);
  }
};

// Close customer modal
const closeModal = () => {
  showModal.value = false;
};

// On component mount, fetch categories and customers
onMounted(() => {
  fetchCategories();
  fetchCustomers();
});

// Delete customer after confirming no associated contacts
const deleteCustomer = async (customerId) => {
  try {
    const contactsResponse = await axios.get(`/api/customers/${customerId}/contacts`);
    const customerContacts = contactsResponse.data;

    if (customerContacts.length > 0) {
      Swal.fire({
        title: 'Cannot Delete Customer',
        text: 'This customer has associated contacts and cannot be deleted. Please delete the contacts first.',
        icon: 'error',
        confirmButtonText: 'OK'
      });
      return;
    }

    Swal.fire({
      title: 'Are you sure?',
      text: 'This will delete the customer. You won\'t be able to revert this!',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, delete it!'
    }).then(async (result) => {
      if (result.isConfirmed) {
        try {
          await axios.delete(`/api/customers/${customerId}`);
          fetchCustomers(); // Refresh the customer list
          Swal.fire({ title: 'Deleted!', text: 'The customer has been deleted.', icon: 'success' });
        } catch (error) {
          console.error('Error deleting customer:', error);
          Swal.fire({ title: 'Error!', text: 'An error occurred while deleting the customer. Please try again.', icon: 'error' });
        }
      }
    });
  } catch (error) {
    console.error('Error fetching contacts:', error);
    Swal.fire({ title: 'Error!', text: 'An error occurred while checking contacts. Please try again.', icon: 'error' });
  }
};

// Delete contact after confirmation
const deleteContact = async (contactId) => {
  Swal.fire({
    title: 'Are you sure?',
    text: 'This will delete the contact. You won\'t be able to revert this!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, delete it!'
  }).then(async (result) => {
    if (result.isConfirmed) {
      try {
        await axios.delete(`/api/contacts/${contactId}`);
        fetchContacts(currentCustomerId.value); // Refresh contacts after deletion
        Swal.fire({ title: 'Deleted!', text: 'The contact has been deleted.', icon: 'success' });
      } catch (error) {
        console.error('Error deleting contact:', error);
        Swal.fire({ title: 'Error!', text: 'An error occurred while deleting the contact.', icon: 'error' });
      }
    }
  });
};

// Handle pagination page change
const handlePageChange = (url) => {
  if (url) {
    fetchCustomers(url); // Fetch new page data
  }
};

// Handle reset action for search and category filter
const handleReset = () => {
  searchQuery.value = ''; // Clear search query
  selectedCategory.value = ''; // Reset category filter
  fetchCustomers(); // Fetch customers without filters
};

// Fetch customers on mount
onMounted(() => {
  fetchCustomers();
});
</script>

<template>
  <div class="min-h-screen bg-gray-100 p-8">
    <div class="container mx-auto">
      <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Customers</h1>
        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" @click="newCustomer">
          Add Customer
        </button>
      </div>

      <!-- Search and Category Filter -->
      <div class="flex gap-4 mb-4">
        <!-- Search Input -->
        <input
          type="text"
          class="form-input w-1/2 p-2 border rounded"
          v-model="searchQuery"
          placeholder="Search by name or reference..."
          @keyup.enter="fetchCustomers('/api/customers', { search: searchQuery.value, category_id: selectedCategory.value })"
        />
        <!-- Category Select -->
        <select class="form-select w-1/4 p-2 border rounded" v-model="selectedCategory">
          <option value="">All Categories</option>
          <option v-for="category in categories" :key="category.id" :value="category.id">
            {{ category.name }}
          </option>
        </select>
        <!-- Search Button -->
        <button
          @click="fetchCustomers('/api/customers', { search: searchQuery.value, category_id: selectedCategory.value })"
          class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
        >
          Search
        </button>
        <!-- Reset Button -->
        <button class="bg-gray-300 hover:bg-gray-400 text-black font-bold py-2 px-4 rounded" @click="handleReset">
          Reset
        </button>
      </div>

      <!-- Customer List -->
      <div class="overflow-x-auto bg-white shadow-md rounded-lg">
        <table class="min-w-full leading-normal">
          <thead>
            <tr>
              <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                Customer Name
              </th>
              <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                Reference
              </th>
              <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                Category
              </th>
              <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                No of Contacts
              </th>
              <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="customer in customers" :key="customer.id" class="bg-white hover:bg-gray-100 border-b">
              <td class="px-5 py-5 text-sm">{{ customer.name }}</td>
              <td class="px-5 py-5 text-sm">{{ customer.reference }}</td>
              <td class="px-5 py-5 text-sm">{{ customer.category?.name }}</td>
              <td class="px-5 py-5 text-sm">
                <a @click="viewContacts(customer.id)">
                  {{ customer.contacts_count }} Contacts
                </a>
              </td>
              <td class="px-5 py-5 text-sm">
                <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded mr-2" @click="onEdit(customer.id)">
                  Edit
                </button>
                <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded" @click="deleteCustomer(customer.id)">
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="flex justify-center mt-5">
        <ul class="pagination-list flex gap-2">
          <li v-for="link in links" :key="link.label" :class="{ 'text-blue-500 font-bold': link.active }">
            <button class="px-4 py-2 border rounded" @click="handlePageChange(link.url)" :disabled="!link.url">
              {{ link.label }}
            </button>
          </li>
        </ul>
      </div>
    </div>

    <!-- Modal for Add/Edit Customer -->
    <Modal :isVisible="showModal" :editMode="editMode" :customerId="currentCustomerId" @close="closeModal" @save="handleSave">
      <div class="p-4 bg-white shadow-md rounded-md">
        <customerForm @refreshList="fetchCustomers" :editMode="editMode" :customerId="currentCustomerId" @close="closeModal" />
        <div v-if="editMode">
          <!-- Contacts Section -->
          <div class="flex justify-between items-center mt-4">
            <h3 class="text-xl font-bold">Contacts</h3>
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" @click="addContact">
              Add Contact
            </button>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
              <thead>
                <tr>
                  <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    First Name
                  </th>
                  <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    Last Name
                  </th>
                  <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    Action
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="contact in contacts" :key="contact.id" class="bg-white hover:bg-gray-100 border-b">
                  <td class="px-5 py-5 text-sm">{{ contact.first_name }}</td>
                  <td class="px-5 py-5 text-sm">{{ contact.last_name }}</td>
                  <td class="px-5 py-5 text-sm">
                    <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded mr-2" @click="onEditContact(contact.id)">
                      Edit
                    </button>
                    <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded" @click="deleteContact(contact.id)">
                      Delete
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </Modal>

    <!-- Modal for Add/Edit Contact -->
    <Modal :isVisible="showContactModal" @close="closeContactModal" @save="saveContact">
      <div class="bg-white shadow-md p-6 rounded-md max-w-lg mx-auto">
        <div class="flex justify-between items-center mb-4">
          <h4 class="text-lg font-semibold">Contact Details</h4>
          <div class="flex space-x-2">
            <button type="button" class="bg-gray-300 hover:bg-gray-400 text-black font-bold py-2 px-4 rounded" @click="closeContactModal">
              Back
            </button>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" @click="saveContact">
              Save
            </button>
          </div>
        </div>

        <!-- General Section Title -->
        <h4 class="text-lg font-semibold mb-4">General</h4>

        <form @submit.prevent="saveContact">
          <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="firstName">First Name *</label>
            <input
              v-model="contactForm.first_name"
              type="text"
              class="w-full border rounded p-2"
              placeholder="First Name"
            />
            <!-- Display error for first_name -->
            <span v-if="errors.first_name" class="text-red-500 text-sm">{{ errors.first_name[0] }}</span>
          </div>

          <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="lastName">Last Name</label>
            <input
              v-model="contactForm.last_name"
              type="text"
              class="w-full border rounded p-2"
              placeholder="Last Name"
            />
            <!-- Display error for last_name (if applicable) -->
            <span v-if="errors.last_name" class="text-red-500 text-sm">{{ errors.last_name[0] }}</span>
          </div>
        </form>
      </div>
    </Modal>

  </div>
</template>
