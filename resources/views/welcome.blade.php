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

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96">
        <link rel="icon" type="image/svg+xml" href="/favicon.svg">
        <link rel="shortcut icon" href="/favicon.ico">
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <meta name="apple-mobile-web-app-title" content="CRUD">
        <link rel="manifest" href="/site.webmanifest">
    </head>
    <body class="min-h-screen bg-background text-foreground font-sans antialiased flex flex-col" 
          x-data="{ 
              activeTab: 'all',
              items: [
                  { id: 1, name: 'Workstation PC 01', type: 'PC', room: 'Lab A', serial: 'SN-PC-001', status: 'Operational' },
                  { id: 2, name: 'Dell UltraSharp 24', type: 'Monitor', room: 'Lab A', serial: 'SN-MON-002', status: 'Operational' },
                  { id: 3, name: 'Developer Laptop', type: 'Laptop', room: 'Lab B', serial: 'SN-LAP-003', status: 'Maintenance' },
                  { id: 4, name: 'Mechanical Keyboard', type: 'Keyboard', room: 'Lab B', serial: 'SN-KB-004', status: 'Broken' },
                  { id: 5, name: 'Server PC 02', type: 'PC', room: 'Lab A', serial: 'SN-PC-002', status: 'Operational' }
              ],
              theme: 'henix',
              darkMode: false,
              sortBy: 'name',
              sortAsc: true,
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

                  this.theme = localStorage.getItem('pc_lab_theme') || 'henix';
                  this.darkMode = localStorage.getItem('pc_lab_dark') === 'true';
                  this.applyTheme();

                  this.$watch('theme', value => {
                      localStorage.setItem('pc_lab_theme', value);
                      this.applyTheme();
                  });
                  this.$watch('darkMode', value => {
                      localStorage.setItem('pc_lab_dark', value.toString());
                      this.applyTheme();
                  });
              },
              applyTheme() {
                  const html = document.documentElement;
                  html.classList.remove('theme-henix', 'theme-teto', 'dark');
                  html.classList.add(`theme-${this.theme}`);
                  if (this.darkMode) {
                      html.classList.add('dark');
                  }
              },
              toggleSort(field) {
                  if (this.sortBy === field) {
                      this.sortAsc = !this.sortAsc;
                  } else {
                      this.sortBy = field;
                      this.sortAsc = true;
                  }
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
                  let filtered = this.items;
                  if (this.activeTab === 'computers') {
                      filtered = this.items.filter(i => ['PC', 'Laptop'].includes(i.type));
                  } else if (this.activeTab === 'peripherals') {
                      filtered = this.items.filter(i => ['Monitor', 'Keyboard', 'Mouse', 'Printer', 'Headset'].includes(i.type));
                  }
                  
                  return filtered.sort((a, b) => {
                      let valA = (a[this.sortBy] || '').toString().toLowerCase();
                      let valB = (b[this.sortBy] || '').toString().toLowerCase();
                      if (valA < valB) return this.sortAsc ? -1 : 1;
                      if (valA > valB) return this.sortAsc ? 1 : -1;
                      return 0;
                  });
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
        <header class="border-b border-border bg-card">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center">
                        <span class="text-lg font-semibold tracking-tight text-foreground">Demo Test</span>
                    </div>
                    
                    <!-- Theme and Mode Controls -->
                    <div class="flex items-center space-x-3">
                        <!-- Dark/Light Toggle -->
                        <button @click="darkMode = !darkMode" 
                                class="p-1.5 rounded-md border border-border bg-card hover:bg-background-alt text-muted hover:text-foreground cursor-pointer focus:outline-none flex items-center justify-center">
                            <svg x-show="darkMode" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                            </svg>
                            <svg x-show="!darkMode" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </button>

                        <!-- Color Scheme Burger Menu -->
                        <div x-data="{ openSchemeMenu: false }" class="relative inline-block text-left">
                            <button @click="openSchemeMenu = !openSchemeMenu" @click.away="openSchemeMenu = false" 
                                    class="p-1.5 rounded-md border border-border bg-card hover:bg-background-alt text-muted hover:text-foreground cursor-pointer focus:outline-none flex items-center justify-center" 
                                    title="Color Scheme">
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                            <div x-show="openSchemeMenu" x-cloak
                                 class="absolute right-0 mt-1 w-32 bg-card border border-border-subtle rounded-md shadow-lg py-1 z-30 focus:outline-none text-left">
                                <div class="px-3 py-1 text-[10px] font-bold text-muted uppercase tracking-wider border-b border-border-subtle mb-1">
                                    Color Scheme
                                </div>
                                <button type="button" @click="theme = 'henix'; openSchemeMenu = false" 
                                        :class="theme === 'henix' ? 'bg-background-alt font-medium text-foreground' : 'text-muted hover:text-foreground hover:bg-background-alt/50'"
                                        class="w-full text-left px-3 py-1.5 text-xs cursor-pointer flex items-center justify-between">
                                    <span>Henix</span>
                                    <template x-if="theme === 'henix'">
                                        <span class="text-xs">&bull;</span>
                                    </template>
                                </button>
                                <button type="button" @click="theme = 'teto'; openSchemeMenu = false" 
                                        :class="theme === 'teto' ? 'bg-background-alt font-medium text-foreground' : 'text-muted hover:text-foreground hover:bg-background-alt/50'"
                                        class="w-full text-left px-3 py-1.5 text-xs cursor-pointer flex items-center justify-between">
                                    <span>Teto</span>
                                    <template x-if="theme === 'teto'">
                                        <span class="text-xs">&bull;</span>
                                    </template>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="mx-auto max-w-7xl w-full px-4 sm:px-6 lg:px-8 py-8 flex-1">
            <!-- Navigation & Actions Flex Row -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-border mb-6 gap-3 sm:gap-0">
                <!-- Navigation Tabs -->
                <nav class="-mb-px flex space-x-6" aria-label="Tabs">
                    <button 
                        @click="activeTab = 'all'" 
                        :class="activeTab === 'all' ? 'border-primary text-primary font-medium' : 'border-transparent text-muted hover:text-foreground hover:border-border'"
                        class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition-colors duration-150 cursor-pointer">
                        All Items
                    </button>
                    <button 
                        @click="activeTab = 'computers'" 
                        :class="activeTab === 'computers' ? 'border-primary text-primary font-medium' : 'border-transparent text-muted hover:text-foreground hover:border-border'"
                        class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition-colors duration-150 cursor-pointer">
                        Computers
                    </button>
                    <button 
                        @click="activeTab = 'peripherals'" 
                        :class="activeTab === 'peripherals' ? 'border-primary text-primary font-medium' : 'border-transparent text-muted hover:text-foreground hover:border-border'"
                        class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition-colors duration-150 cursor-pointer">
                        Peripherals
                    </button>
                </nav>

                <!-- Action Button -->
                <button @click="showCreateModal = true" class="mb-2 inline-flex items-center bg-primary text-primary-text hover:bg-primary-hover text-xs font-semibold px-3 py-2 rounded-md transition-colors cursor-pointer">
                    + Add Item
                </button>
            </div>

            <!-- Tab Contents -->
            <!-- Desktop Table View (Hidden on mobile) -->
            <div class="hidden md:block bg-card border border-border rounded-lg shadow-sm overflow-visible">
                <div class="overflow-x-visible">
                    <table class="min-w-full divide-y divide-border text-left text-sm">
                        <thead class="bg-background-alt text-xs font-semibold uppercase tracking-wider text-muted select-none">
                            <tr>
                                <th scope="col" @click="toggleSort('name')" class="px-6 py-3 cursor-pointer hover:text-foreground">
                                    <div class="flex items-center space-x-1">
                                        <span>Asset Name</span>
                                        <template x-if="sortBy === 'name'"><span x-text="sortAsc ? '↑' : '↓'"></span></template>
                                    </div>
                                </th>
                                <th scope="col" @click="toggleSort('type')" class="px-6 py-3 cursor-pointer hover:text-foreground">
                                    <div class="flex items-center space-x-1">
                                        <span>Type</span>
                                        <template x-if="sortBy === 'type'"><span x-text="sortAsc ? '↑' : '↓'"></span></template>
                                    </div>
                                </th>
                                <th scope="col" @click="toggleSort('room')" class="px-6 py-3 cursor-pointer hover:text-foreground">
                                    <div class="flex items-center space-x-1">
                                        <span>Lab Room</span>
                                        <template x-if="sortBy === 'room'"><span x-text="sortAsc ? '↑' : '↓'"></span></template>
                                    </div>
                                </th>
                                <th scope="col" @click="toggleSort('serial')" class="px-6 py-3 cursor-pointer hover:text-foreground">
                                    <div class="flex items-center space-x-1">
                                        <span>Serial Number</span>
                                        <template x-if="sortBy === 'serial'"><span x-text="sortAsc ? '↑' : '↓'"></span></template>
                                    </div>
                                </th>
                                <th scope="col" @click="toggleSort('status')" class="px-6 py-3 cursor-pointer hover:text-foreground">
                                    <div class="flex items-center space-x-1">
                                        <span>Status</span>
                                        <template x-if="sortBy === 'status'"><span x-text="sortAsc ? '↑' : '↓'"></span></template>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border bg-card text-foreground">
                            <template x-for="item in filteredItems()" :key="item.id">
                                <tr class="hover:bg-background-alt/30 transition-colors">
                                    <td class="px-6 py-4 font-medium text-foreground" x-text="item.name"></td>
                                    <td class="px-6 py-4 text-muted" x-text="item.type"></td>
                                    <td class="px-6 py-4 text-muted" x-text="item.room"></td>
                                    <td class="px-6 py-4 font-mono text-xs text-muted" x-text="item.serial"></td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" 
                                              :class="item.status === 'Operational' ? 'status-operational' : (item.status === 'Maintenance' ? 'status-maintenance' : 'status-broken')"
                                              x-text="item.status"></span>
                                    </td>
                                    <td class="px-6 py-4 text-right overflow-visible">
                                        <!-- Google Drive-like Three-Dots Action Button -->
                                        <div x-data="{ openMenu: false }" class="relative inline-block text-left">
                                            <button @click="openMenu = !openMenu" @click.away="openMenu = false" class="p-1 rounded-full hover:bg-background-alt text-muted hover:text-foreground cursor-pointer focus:outline-none">
                                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 5v.01M12 12v.01M12 19v.01" />
                                                </svg>
                                            </button>
                                            <div x-show="openMenu" x-cloak
                                                 class="absolute right-0 mt-1 w-24 bg-card border border-border-subtle rounded-md shadow-lg py-1 z-20 text-left focus:outline-none">
                                                <button type="button" @click="editItem(item); openMenu = false" class="w-full text-left px-3 py-1.5 text-xs text-foreground hover:bg-background-alt cursor-pointer">
                                                    Edit
                                                </button>
                                                <button type="button" @click="confirmDelete(item.id); openMenu = false" class="w-full text-left px-3 py-1.5 text-xs text-red-500 hover:bg-background-alt cursor-pointer">
                                                    Delete
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="filteredItems().length === 0">
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-sm text-muted bg-card">
                                        No inventory items found.
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mobile List View (Hidden on desktop) -->
            <div class="block md:hidden space-y-3">
                <!-- Mobile Sort Controls -->
                <div class="flex items-center justify-between bg-card border border-border rounded-lg p-3 text-xs text-muted mb-2">
                    <div class="flex items-center space-x-1">
                        <span>Sort by:</span>
                        <select x-model="sortBy" class="bg-background-alt border border-border rounded-md px-2 py-1 text-xs text-foreground focus:outline-none cursor-pointer">
                            <option value="name">Name</option>
                            <option value="type">Type</option>
                            <option value="room">Room</option>
                            <option value="status">Status</option>
                        </select>
                    </div>
                    <button @click="sortAsc = !sortAsc" class="p-1.5 border border-border rounded-md bg-card hover:bg-background-alt text-foreground cursor-pointer flex items-center justify-center">
                        <span x-text="sortAsc ? '↑ Asc' : '↓ Desc'"></span>
                    </button>
                </div>

                <template x-for="item in filteredItems()" :key="item.id">
                    <div class="bg-card border border-border rounded-lg p-4 space-y-3 shadow-xs">
                        <div class="flex items-start justify-between">
                            <div class="space-y-1">
                                <h4 class="font-medium text-foreground text-sm" x-text="item.name"></h4>
                                <div class="text-xs text-muted">
                                    <span x-text="item.type"></span> &bull; <span x-text="item.room"></span>
                                </div>
                            </div>
                            <div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" 
                                      :class="item.status === 'Operational' ? 'status-operational' : (item.status === 'Maintenance' ? 'status-maintenance' : 'status-broken')"
                                      x-text="item.status"></span>
                            </div>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-border/50">
                            <div class="text-[11px] font-mono text-muted" x-text="item.serial"></div>
                            
                            <!-- Google Drive-like Three-Dots Action Button -->
                            <div x-data="{ openMenu: false }" class="relative inline-block text-left">
                                <button @click="openMenu = !openMenu" @click.away="openMenu = false" class="p-1 rounded-full hover:bg-background-alt text-muted hover:text-foreground cursor-pointer focus:outline-none">
                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 5v.01M12 12v.01M12 19v.01" />
                                    </svg>
                                </button>
                                <div x-show="openMenu" x-cloak
                                     class="absolute right-0 bottom-8 mt-1 w-24 bg-card border border-border-subtle rounded-md shadow-lg py-1 z-10 text-left focus:outline-none">
                                    <button type="button" @click="editItem(item); openMenu = false" class="w-full text-left px-3 py-1.5 text-xs text-foreground hover:bg-background-alt cursor-pointer">
                                        Edit
                                    </button>
                                    <button type="button" @click="confirmDelete(item.id); openMenu = false" class="w-full text-left px-3 py-1.5 text-xs text-red-500 hover:bg-background-alt cursor-pointer">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                <template x-if="filteredItems().length === 0">
                    <div class="bg-card border border-border rounded-lg p-6 text-center text-sm text-muted">
                        No inventory items found.
                    </div>
                </template>
            </div>
        </main>

        <!-- Create Modal Overlay -->
        <div x-show="showCreateModal" 
             class="fixed inset-0 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 z-50" 
             x-cloak
             @keydown.escape.window="showCreateModal = false">
            
            <div class="bg-card border border-border rounded-lg shadow-xl max-w-md w-full p-6 space-y-4"
                 @click.away="showCreateModal = false">
                
                <div class="flex items-center justify-between border-b border-border pb-3">
                    <h3 class="text-base font-semibold text-foreground">Add New Inventory Asset</h3>
                    <button @click="showCreateModal = false" class="text-muted hover:text-foreground cursor-pointer">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <form @submit.prevent="createItem()" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-1">Asset Name</label>
                        <input type="text" x-model="newItem.name" required
                               class="w-full border border-border rounded-md px-3 py-2 text-sm focus:border-primary focus:outline-none bg-background-alt text-foreground" 
                               placeholder="e.g. Workstation PC 03">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-1">Asset Type</label>
                            <select x-model="newItem.type" class="w-full border border-border rounded-md px-3 py-2 text-sm focus:border-primary focus:outline-none bg-background-alt text-foreground">
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
                            <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-1">Status</label>
                            <select x-model="newItem.status" class="w-full border border-border rounded-md px-3 py-2 text-sm focus:border-primary focus:outline-none bg-background-alt text-foreground">
                                <option value="Operational">Operational</option>
                                <option value="Maintenance">Maintenance</option>
                                <option value="Broken">Broken</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-1">Lab Room</label>
                            <input type="text" x-model="newItem.room" required
                                   class="w-full border border-border rounded-md px-3 py-2 text-sm focus:border-primary focus:outline-none bg-background-alt text-foreground" 
                                   placeholder="e.g. Lab B">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-1">Serial Number</label>
                            <input type="text" x-model="newItem.serial" required
                                   class="w-full border border-border rounded-md px-3 py-2 text-sm focus:border-primary focus:outline-none bg-background-alt text-foreground" 
                                   placeholder="e.g. SN-PC-003">
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-3 border-t border-border">
                        <button type="button" @click="showCreateModal = false" 
                                class="border border-border text-foreground hover:bg-background-alt text-xs font-semibold px-3 py-2 rounded-md cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="bg-primary text-primary-text hover:bg-primary-hover text-xs font-semibold px-3 py-2 rounded-md cursor-pointer">
                            Save Asset
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Modal Overlay -->
        <div x-show="showEditModal" 
             class="fixed inset-0 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 z-50" 
             x-cloak
             @keydown.escape.window="showEditModal = false">
            
            <div class="bg-card border border-border rounded-lg shadow-xl max-w-md w-full p-6 space-y-4"
                 @click.away="showEditModal = false">
                
                <div class="flex items-center justify-between border-b border-border pb-3">
                    <h3 class="text-base font-semibold text-foreground">Edit Inventory Asset</h3>
                    <button @click="showEditModal = false" class="text-muted hover:text-foreground cursor-pointer">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <form @submit.prevent="updateItem()" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-1">Asset Name</label>
                        <input type="text" x-model="editItemData.name" required
                               class="w-full border border-border rounded-md px-3 py-2 text-sm focus:border-primary focus:outline-none bg-background-alt text-foreground" 
                               placeholder="e.g. Workstation PC 03">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-1">Asset Type</label>
                            <select x-model="editItemData.type" class="w-full border border-border rounded-md px-3 py-2 text-sm focus:border-primary focus:outline-none bg-background-alt text-foreground">
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
                            <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-1">Status</label>
                            <select x-model="editItemData.status" class="w-full border border-border rounded-md px-3 py-2 text-sm focus:border-primary focus:outline-none bg-background-alt text-foreground">
                                <option value="Operational">Operational</option>
                                <option value="Maintenance">Maintenance</option>
                                <option value="Broken">Broken</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-1">Lab Room</label>
                            <input type="text" x-model="editItemData.room" required
                                   class="w-full border border-border rounded-md px-3 py-2 text-sm focus:border-primary focus:outline-none bg-background-alt text-foreground" 
                                   placeholder="e.g. Lab B">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-1">Serial Number</label>
                            <input type="text" x-model="editItemData.serial" required
                                   class="w-full border border-border rounded-md px-3 py-2 text-sm focus:border-primary focus:outline-none bg-background-alt text-foreground" 
                                   placeholder="e.g. SN-PC-003">
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-3 border-t border-border">
                        <button type="button" @click="showEditModal = false" 
                                class="border border-border text-foreground hover:bg-background-alt text-xs font-semibold px-3 py-2 rounded-md cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="bg-primary text-primary-text hover:bg-primary-hover text-xs font-semibold px-3 py-2 rounded-md cursor-pointer">
                            Update Asset
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Confirmation Modal Overlay -->
        <div x-show="showDeleteModal" 
             class="fixed inset-0 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 z-50" 
             x-cloak
             @keydown.escape.window="showDeleteModal = false">
            
            <div class="bg-card border border-border rounded-lg shadow-xl max-w-sm w-full p-6 space-y-4"
                 @click.away="showDeleteModal = false">
                
                <div class="space-y-2">
                    <h3 class="text-base font-semibold text-foreground">Delete Asset</h3>
                    <p class="text-sm text-muted">Are you sure you want to remove this asset from the inventory? This action cannot be undone.</p>
                </div>

                <div class="flex justify-end space-x-3 pt-3 border-t border-border">
                    <button type="button" @click="showDeleteModal = false" 
                            class="border border-border text-foreground hover:bg-background-alt text-xs font-semibold px-3 py-2 rounded-md cursor-pointer">
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
        <footer class="border-t border-border bg-card py-4 text-xs text-muted">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-0">
                <!-- Left Corner (text info) -->
                <div class="flex flex-col items-center sm:items-start space-y-0.5 text-center sm:text-left">
                    <div>PC Laboratory Inventory System - Admin Panel</div>
                    <div class="text-[10px]">Made by L0125013</div>
                </div>
                <!-- Right Corner (GitHub icon) -->
                <div class="flex items-center justify-center">
                    <a href="https://github.com/justhenix" target="_blank" rel="noopener noreferrer" class="text-muted hover:text-foreground transition-colors duration-150" title="GitHub Profile">
                        <svg class="size-5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/>
                        </svg>
                    </a>
                </div>
            </div>
        </footer>
    </body>
</html>
