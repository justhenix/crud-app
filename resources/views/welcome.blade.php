<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>PC Laboratory Inventory</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-900 font-sans antialiased flex flex-col" 
          x-data="{ 
              activeTab: 'all',
              items: [
                  { id: 1, name: 'Workstation PC 01', type: 'PC', room: 'Lab A', serial: 'SN-PC-001', status: 'Operational' },
                  { id: 2, name: 'Dell UltraSharp 24', type: 'Monitor', room: 'Lab A', serial: 'SN-MON-002', status: 'Operational' },
                  { id: 3, name: 'Developer Laptop', type: 'Laptop', room: 'Lab B', serial: 'SN-LAP-003', status: 'Maintenance' },
                  { id: 4, name: 'Mechanical Keyboard', type: 'Keyboard', room: 'Lab B', serial: 'SN-KB-004', status: 'Broken' },
                  { id: 5, name: 'Server PC 02', type: 'PC', room: 'Lab A', serial: 'SN-PC-002', status: 'Operational' }
              ],
              init() {
                  const saved = localStorage.getItem('pc_lab_inventory');
                  if (saved) {
                      try {
                          this.items = JSON.parse(saved);
                      } catch (e) {
                          console.error('Failed to load inventory:', e);
                      }
                  }
                  this.$watch('items', value => {
                      localStorage.setItem('pc_lab_inventory', JSON.stringify(value));
                  });
              },
              showCreateModal: false,
              newItem: {
                  name: '',
                  type: 'PC',
                  room: '',
                  serial: '',
                  status: 'Operational'
              },
              showEditModal: false,
              editItemData: {
                  id: null,
                  name: '',
                  type: 'PC',
                  room: '',
                  serial: '',
                  status: 'Operational'
              },
              showDeleteModal: false,
              deleteItemId: null,
              filteredItems() {
                  if (this.activeTab === 'all') return this.items;
                  if (this.activeTab === 'computers') return this.items.filter(i => ['PC', 'Laptop'].includes(i.type));
                  if (this.activeTab === 'peripherals') return this.items.filter(i => ['Monitor', 'Keyboard', 'Mouse', 'Printer', 'Headset'].includes(i.type));
                  return this.items;
              },
              resetNewItem() {
                  this.newItem = { name: '', type: 'PC', room: '', serial: '', status: 'Operational' };
              },
              createItem() {
                  if (!this.newItem.name.trim() || !this.newItem.room.trim() || !this.newItem.serial.trim()) {
                      alert('Please fill in all fields.');
                      return;
                  }
                  this.items.push({
                      id: Date.now(),
                      name: this.newItem.name.trim(),
                      type: this.newItem.type,
                      room: this.newItem.room.trim(),
                      serial: this.newItem.serial.trim(),
                      status: this.newItem.status
                  });
                  this.resetNewItem();
                  this.showCreateModal = false;
              },
              editItem(item) {
                  this.editItemData = { ...item };
                  this.showEditModal = true;
              },
              updateItem() {
                  if (!this.editItemData.name.trim() || !this.editItemData.room.trim() || !this.editItemData.serial.trim()) {
                      alert('Please fill in all fields.');
                      return;
                  }
                  const index = this.items.findIndex(i => i.id === this.editItemData.id);
                  if (index !== -1) {
                      this.items[index] = { ...this.editItemData };
                  }
                  this.showEditModal = false;
              },
              confirmDelete(id) {
                  this.deleteItemId = id;
                  this.showDeleteModal = true;
              },
              deleteItem() {
                  this.items = this.items.filter(i => i.id !== this.deleteItemId);
                  this.showDeleteModal = false;
                  this.deleteItemId = null;
              }
          }">
        <!-- App Header -->
        <header class="border-b border-slate-200 bg-white">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center">
                        <span class="text-lg font-semibold tracking-tight">PC Lab Inventory</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-400">System Shell</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="mx-auto max-w-7xl w-full px-4 sm:px-6 lg:px-8 py-8 flex-1">
            <!-- Navigation & Actions Flex Row -->
            <div class="flex items-center justify-between border-b border-slate-200 mb-6">
                <!-- Navigation Tabs -->
                <nav class="-mb-px flex space-x-6" aria-label="Tabs">
                    <button 
                        @click="activeTab = 'all'" 
                        :class="activeTab === 'all' ? 'border-slate-800 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                        class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition-colors duration-150 cursor-pointer">
                        All Items
                    </button>
                    <button 
                        @click="activeTab = 'computers'" 
                        :class="activeTab === 'computers' ? 'border-slate-800 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                        class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition-colors duration-150 cursor-pointer">
                        Computers
                    </button>
                    <button 
                        @click="activeTab = 'peripherals'" 
                        :class="activeTab === 'peripherals' ? 'border-slate-800 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                        class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition-colors duration-150 cursor-pointer">
                        Peripherals
                    </button>
                </nav>

                <!-- Action Button -->
                <button @click="showCreateModal = true" class="mb-2 inline-flex items-center bg-slate-900 text-white hover:bg-slate-800 text-xs font-semibold px-3 py-2 rounded-md transition-colors cursor-pointer">
                    + Add Item
                </button>
            </div>

            <!-- Tab Contents -->
            <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                        <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500">
                            <tr>
                                <th scope="col" class="px-6 py-3">Asset Name</th>
                                <th scope="col" class="px-6 py-3">Type</th>
                                <th scope="col" class="px-6 py-3">Lab Room</th>
                                <th scope="col" class="px-6 py-3">Serial Number</th>
                                <th scope="col" class="px-6 py-3">Status</th>
                                <th scope="col" class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white text-slate-700">
                            <template x-for="item in filteredItems()" :key="item.id">
                                <tr>
                                    <td class="px-6 py-4 font-medium text-slate-900" x-text="item.name"></td>
                                    <td class="px-6 py-4" x-text="item.type"></td>
                                    <td class="px-6 py-4" x-text="item.room"></td>
                                    <td class="px-6 py-4 font-mono text-xs" x-text="item.serial"></td>
                                    <td class="px-6 py-4" x-text="item.status"></td>
                                    <td class="px-6 py-4 text-right space-x-3 whitespace-nowrap">
                                        <button @click="editItem(item)" class="text-xs font-medium text-slate-600 hover:text-slate-900 cursor-pointer">Edit</button>
                                        <button @click="confirmDelete(item.id)" class="text-xs font-medium text-red-600 hover:text-red-900 cursor-pointer">Delete</button>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="filteredItems().length === 0">
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-sm text-slate-400">
                                        No inventory items found.
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>

        <!-- Create Modal Overlay -->
        <div x-show="showCreateModal" 
             class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 z-50" 
             x-cloak
             @keydown.escape.window="showCreateModal = false">
            
            <div class="bg-white border border-slate-200 rounded-lg shadow-xl max-w-md w-full p-6 space-y-4"
                 @click.away="showCreateModal = false">
                
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-semibold text-slate-900">Add New Inventory Asset</h3>
                    <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <form @submit.prevent="createItem()" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Asset Name</label>
                        <input type="text" x-model="newItem.name" required
                               class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm focus:border-slate-800 focus:outline-none bg-slate-50" 
                               placeholder="e.g. Workstation PC 03">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Asset Type</label>
                            <select x-model="newItem.type" class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm focus:border-slate-800 focus:outline-none bg-slate-50">
                                <option value="PC">PC</option>
                                <option value="Laptop">Laptop</option>
                                <option value="Monitor">Monitor</option>
                                <option value="Keyboard">Keyboard</option>
                                <option value="Mouse">Mouse</option>
                                <option value="Printer">Printer</option>
                                <option value="Headset">Headset</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Status</label>
                            <select x-model="newItem.status" class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm focus:border-slate-800 focus:outline-none bg-slate-50">
                                <option value="Operational">Operational</option>
                                <option value="Maintenance">Maintenance</option>
                                <option value="Broken">Broken</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Lab Room</label>
                            <input type="text" x-model="newItem.room" required
                                   class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm focus:border-slate-800 focus:outline-none bg-slate-50" 
                                   placeholder="e.g. Lab B">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Serial Number</label>
                            <input type="text" x-model="newItem.serial" required
                                   class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm focus:border-slate-800 focus:outline-none bg-slate-50" 
                                   placeholder="e.g. SN-PC-003">
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-3 border-t border-slate-100">
                        <button type="button" @click="showCreateModal = false" 
                                class="border border-slate-300 text-slate-700 hover:bg-slate-50 text-xs font-semibold px-3 py-2 rounded-md cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="bg-slate-900 text-white hover:bg-slate-800 text-xs font-semibold px-3 py-2 rounded-md cursor-pointer">
                            Save Asset
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Modal Overlay -->
        <div x-show="showEditModal" 
             class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 z-50" 
             x-cloak
             @keydown.escape.window="showEditModal = false">
            
            <div class="bg-white border border-slate-200 rounded-lg shadow-xl max-w-md w-full p-6 space-y-4"
                 @click.away="showEditModal = false">
                
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-semibold text-slate-900">Edit Inventory Asset</h3>
                    <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <form @submit.prevent="updateItem()" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Asset Name</label>
                        <input type="text" x-model="editItemData.name" required
                               class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm focus:border-slate-800 focus:outline-none bg-slate-50" 
                               placeholder="e.g. Workstation PC 03">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Asset Type</label>
                            <select x-model="editItemData.type" class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm focus:border-slate-800 focus:outline-none bg-slate-50">
                                <option value="PC">PC</option>
                                <option value="Laptop">Laptop</option>
                                <option value="Monitor">Monitor</option>
                                <option value="Keyboard">Keyboard</option>
                                <option value="Mouse">Mouse</option>
                                <option value="Printer">Printer</option>
                                <option value="Headset">Headset</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Status</label>
                            <select x-model="editItemData.status" class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm focus:border-slate-800 focus:outline-none bg-slate-50">
                                <option value="Operational">Operational</option>
                                <option value="Maintenance">Maintenance</option>
                                <option value="Broken">Broken</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Lab Room</label>
                            <input type="text" x-model="editItemData.room" required
                                   class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm focus:border-slate-800 focus:outline-none bg-slate-50" 
                                   placeholder="e.g. Lab B">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Serial Number</label>
                            <input type="text" x-model="editItemData.serial" required
                                   class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm focus:border-slate-800 focus:outline-none bg-slate-50" 
                                   placeholder="e.g. SN-PC-003">
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-3 border-t border-slate-100">
                        <button type="button" @click="showEditModal = false" 
                                class="border border-slate-300 text-slate-700 hover:bg-slate-50 text-xs font-semibold px-3 py-2 rounded-md cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="bg-slate-900 text-white hover:bg-slate-800 text-xs font-semibold px-3 py-2 rounded-md cursor-pointer">
                            Update Asset
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Confirmation Modal Overlay -->
        <div x-show="showDeleteModal" 
             class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 z-50" 
             x-cloak
             @keydown.escape.window="showDeleteModal = false">
            
            <div class="bg-white border border-slate-200 rounded-lg shadow-xl max-w-sm w-full p-6 space-y-4"
                 @click.away="showDeleteModal = false">
                
                <div class="space-y-2">
                    <h3 class="text-base font-semibold text-slate-900">Delete Asset</h3>
                    <p class="text-sm text-slate-500">Are you sure you want to remove this asset from the inventory? This action cannot be undone.</p>
                </div>

                <div class="flex justify-end space-x-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="showDeleteModal = false" 
                            class="border border-slate-300 text-slate-700 hover:bg-slate-50 text-xs font-semibold px-3 py-2 rounded-md cursor-pointer">
                        Cancel
                    </button>
                    <button type="button" @click="deleteItem()" 
                            class="bg-red-600 text-white hover:bg-red-700 text-xs font-semibold px-3 py-2 rounded-md cursor-pointer">
                        Confirm Delete
                    </button>
                </div>
            </div>
        </div>

        <!-- App Footer -->
        <footer class="border-t border-slate-200 bg-white py-4 text-center text-xs text-slate-500">
            <span>PC Laboratory Inventory System &bull; Admin Panel</span>
        </footer>
    </body>
</html>
