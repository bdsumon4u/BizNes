<x-filament-panels::page @class([
    'fi-resource-create-record-page',
    'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),
])>
    <div x-data="{
        products: [],
        search: '',
        searchResults: $wire.entangle('searchResults'),
        globalDiscount: 0,
        globalDiscountType: 'fixed',
        additionalCost: 0,
        additionalCostNote: '',
    
        validateProductDiscount(product) {
            if (product.discount_type === 'fixed') {
                const maxDiscount = product.price * product.quantity;
                if (product.discount > maxDiscount) {
                    product.discount = maxDiscount;
                }
            } else if (product.discount_type === 'percent') {
                if (product.discount > 100) {
                    product.discount = 100;
                }
            }
        },
    
        validateGlobalDiscount() {
            if (this.globalDiscountType === 'fixed') {
                const subtotal = this.calculateTotal();
                if (this.globalDiscount > subtotal) {
                    this.globalDiscount = subtotal;
                }
            } else if (this.globalDiscountType === 'percent') {
                if (this.globalDiscount > 100) {
                    this.globalDiscount = 100;
                }
            }
        },
    
        addProduct(product) {
            const existingProduct = this.products.find(p => (p.id === product.id) && !p.expiry_date);
    
            if (existingProduct) {
                existingProduct.quantity++;
            } else {
                this.products.unshift({
                    id: product.id,
                    name: product.name,
                    image: product.image,
                    quantity: 1,
                    price: product.price || 0,
                    discount: 0,
                    discount_type: 'fixed',
                    expiry_date: null,
                });
            }
    
            this.search = '';
            this.searchResults = [];
            $wire.set('searchResults', []);
            $wire.set('data.search', '');
        },
    
        calculateSubtotal(product) {
            const subtotal = product.quantity * product.price;
            if (product.discount_type === 'fixed') {
                return subtotal - (product.discount * product.quantity);
            }
            return subtotal - (subtotal * (product.discount / 100));
        },
    
        calculateTotal() {
            return this.products.reduce((total, product) => total + this.calculateSubtotal(product), 0);
        },
    
        calculateGlobalDiscount() {
            const subtotal = this.calculateTotal();
            if (this.globalDiscountType === 'fixed') {
                return parseFloat(this.globalDiscount) || 0;
            }
            return subtotal * (parseFloat(this.globalDiscount) || 0) / 100;
        },
    
        calculateFinalTotal() {
            return this.calculateTotal() - this.calculateGlobalDiscount() + parseFloat(this.additionalCost || 0);
        }
    }" class="space-y-6">
        <x-filament-panels::form id="form" :wire:key="$this->getId() . '.forms.' . $this->getFormStatePath()"
            wire:submit="create"
            @submit.prevent="$wire.set('purchaseItems', products); $wire.set('data.global_discount', globalDiscount); $wire.set('data.global_discount_type', globalDiscountType); $wire.set('data.final_amount', calculateFinalTotal())">
            <!-- Search Input -->
            <div class="relative">
                <div class="relative">
                    {{ $this->form }}

                    <!-- Search Results Dropdown -->
                    <div x-show="$wire.searchResults.length > 0" x-cloak
                        class="absolute z-50 w-full mt-1 overflow-hidden bg-white border rounded-lg shadow-lg dark:bg-gray-800 dark:border-gray-700">
                        <template x-for="product in $wire.searchResults">
                            <div @click="addProduct(product); $nextTick(() => { document.getElementById('data.search').focus() })"
                                class="flex items-center p-2 transition duration-150 cursor-pointer gap-x-2 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <img width="50" height="50" :src="product.image" :alt="product.name" />
                                <span class="text-gray-700 dark:text-gray-200" x-text="product.name"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Products Table -->
            <div class="overflow-hidden bg-white rounded-lg shadow dark:bg-gray-800">
                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase whitespace-nowrap dark:text-gray-400">
                                    Product</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase whitespace-nowrap dark:text-gray-400">
                                    Quantity</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase whitespace-nowrap dark:text-gray-400">
                                    Unit Price</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase whitespace-nowrap dark:text-gray-400">
                                    Unit Discount</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase whitespace-nowrap dark:text-gray-400">
                                    Subtotal</th>
                                <th class="px-6 py-3 text-right whitespace-nowrap"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <template x-for="(product, index) in products">
                                <tr class="transition duration-150 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="p-2 text-sm text-gray-700 dark:text-gray-200">
                                        <div
                                            class="min-w-[20rem] flex items-center transition duration-150 cursor-pointer gap-x-2 hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <img width="80" height="80" :src="product.image"
                                                :alt="product.name" />
                                            <div>
                                                <p class="font-semibold text-gray-700 dark:text-gray-200 line-clamp-3"
                                                    x-text="product.name"></span>
                                                    <x-datepicker placeholder="Expiry Date" model="product.expiry_date" />
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-2 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <button type="button" @click="if(product.quantity > 1) product.quantity--"
                                                tabindex="-1"
                                                class="p-1 border border-r-0 border-gray-300 rounded-l dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                <x-tabler-minus class="w-4 h-4 text-gray-500 dark:text-gray-400" />
                                            </button>
                                            <input type="number" x-model="product.quantity" min="1"
                                                class="w-20 px-2 py-1 text-center border-gray-300 border-y dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-primary-500 focus:border-primary-500"
                                                @focus="$event.target.select()">
                                            <button type="button" @click="product.quantity++" tabindex="-1"
                                                class="p-1 border border-l-0 border-gray-300 rounded-r dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                <x-tabler-plus class="w-4 h-4 text-gray-500 dark:text-gray-400" />
                                            </button>
                                        </div>
                                    </td>
                                    <td class="p-2 whitespace-nowrap">
                                        <div class="relative w-32">
                                            <input type="number" x-model="product.price" min="0"
                                                class="w-full px-3 py-1 border border-gray-300 rounded ps-8 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-primary-500 focus:border-primary-500"
                                                @focus="$event.target.select()">
                                            <div
                                                class="absolute inset-y-0 flex items-center pointer-events-none start-0 ps-2">
                                                <x-tabler-currency-taka class="w-4 h-4" />
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-2 whitespace-nowrap">
                                        <div class="flex items-center space-x-1">
                                            <input type="number" x-model="product.discount" min="0"
                                                @input="validateProductDiscount(product)"
                                                class="w-24 px-3 py-1 border border-gray-300 rounded dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-primary-500 focus:border-primary-500"
                                                @focus="$event.target.select()">
                                            <button type="button"
                                                @click="product.discount_type = product.discount_type === 'fixed' ? 'percent' : 'fixed'; validateProductDiscount(product)"
                                                class="p-1 transition-colors duration-150 bg-gray-100 rounded dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600">
                                                <span class="text-gray-700 dark:text-gray-200">
                                                    <x-tabler-currency-taka class="w-5 h-5"
                                                        x-show="product.discount_type == 'fixed'" />
                                                    <x-tabler-percentage class="w-5 h-5"
                                                        x-show="product.discount_type != 'fixed'" />
                                                </span>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="p-2 text-sm text-gray-700 whitespace-nowrap dark:text-gray-200">
                                        <div class="flex items-center">
                                            <x-tabler-currency-taka class="w-5 h-5" />
                                            <span x-text="calculateSubtotal(product).toFixed(2)"></span>
                                        </div>
                                    </td>
                                    <td class="p-2 text-right whitespace-nowrap">
                                        <button type="button" @click="products.splice(index, 1)"
                                            class="text-red-600 transition duration-150 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                            <x-tabler-trash class="w-5 h-5" />
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="products.length === 0">
                                <td colspan="6" class="px-6 py-8 text-center">
                                    <div
                                        class="flex flex-col items-center justify-center space-y-2 text-gray-500 dark:text-gray-400">
                                        <x-tabler-shopping-cart class="w-8 h-8" />
                                        <p class="text-sm font-medium">No products added to purchase</p>
                                        <p class="text-xs">Search and select products to add them to your purchase</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Replace the entire summary section with this new design -->
            <div class="overflow-hidden bg-white rounded-lg shadow dark:bg-gray-800">
                <div class="p-2">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-7">
                        <!-- Subtotal Card -->
                        <div class="p-2 rounded-lg bg-gray-50 dark:bg-gray-700">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Subtotal</span>
                                <x-tabler-trending-up class="w-5 h-5 text-gray-400 dark:text-gray-500" />
                            </div>
                            <div class="flex items-center mt-2 text-gray-500 dark:text-gray-400">
                                <x-tabler-currency-taka />
                                <span class="text-xl font-bold" x-text="calculateTotal().toFixed(2)"></span>
                            </div>
                        </div>

                        <!-- Discount Card with Input -->
                        <div class="p-2 rounded-lg md:col-span-2 bg-red-50 dark:bg-red-900/20">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-medium text-red-600 dark:text-red-400">Discount</span>
                                <x-tabler-info-circle class="w-5 h-5 text-red-500 dark:text-red-400" />
                            </div>
                            <div class="relative flex justify-between">
                                <div class="flex items-center">
                                    <div class="relative flex-1">
                                        <div class="flex items-center space-x-1">
                                            <input type="number" x-model="globalDiscount" min="0"
                                                @input="validateGlobalDiscount()"
                                                class="w-24 px-3 py-1 border border-gray-300 rounded dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-primary-500 focus:border-primary-500"
                                                @focus="$event.target.select()">
                                            <button type="button"
                                                @click="globalDiscountType = globalDiscountType === 'fixed' ? 'percent' : 'fixed'; validateGlobalDiscount()"
                                                class="p-1 transition-colors duration-150 bg-gray-100 rounded dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600">
                                                <span class="text-gray-700 dark:text-gray-200">
                                                    <x-tabler-currency-taka x-show="globalDiscountType == 'fixed'" />
                                                    <x-tabler-percentage x-show="globalDiscountType != 'fixed'" />
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-2 text-xs font-medium text-red-500 gap-x-2 dark:text-red-400">
                                    Applied: <span x-text="'- ' + calculateGlobalDiscount().toFixed(2)"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Cost Card -->
                        <div class="p-2 rounded-lg md:col-span-3 bg-yellow-50 dark:bg-yellow-900/20">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-medium text-yellow-600 dark:text-yellow-400">Additional
                                    Cost</span>
                                <x-tabler-truck class="w-5 h-5 text-yellow-500 dark:text-yellow-400" />
                            </div>
                            <div class="flex flex-col md:flex-row gap-x-1">
                                <div class="relative w-32">
                                    <input type="number" x-model="additionalCost" min="0"
                                        class="w-full px-3 py-1 border border-gray-300 rounded ps-8 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-primary-500 focus:border-primary-500"
                                        placeholder="Enter amount" @focus="$event.target.select()">
                                    <div class="absolute inset-y-0 flex items-center pointer-events-none start-0 ps-2">
                                        <x-tabler-currency-taka class="w-4 h-4" />
                                    </div>
                                </div>
                                <input type="text" x-model="additionalCostNote"
                                    class="w-full px-3 py-1 text-sm border border-gray-300 rounded dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-primary-500 focus:border-primary-500"
                                    placeholder="Note (optional)">
                            </div>
                        </div>

                        <!-- Final Total Card -->
                        <div class="p-2 rounded-lg bg-primary-50 dark:bg-primary-900/20">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-primary-600 dark:text-primary-400">Final
                                    Total</span>
                                <x-tabler-cash class="w-5 h-5 text-primary-400 dark:text-primary-500" />
                            </div>
                            <div class="flex items-center justify-end mt-2 text-primary-600 dark:text-primary-400">
                                <x-tabler-currency-taka />
                                <span class="text-xl font-bold" x-text="calculateFinalTotal().toFixed(2)"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <x-filament-panels::form.actions :actions="$this->getCachedFormActions()" :full-width="$this->hasFullWidthFormActions()" />
        </x-filament-panels::form>
    </div>

    <x-filament-panels::page.unsaved-data-changes-alert />
</x-filament-panels::page>
