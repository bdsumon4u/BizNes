@push('styles')
    <style>
        .fi-main {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .fi-point-of-sale-page>section {
            padding-top: 1rem;
            padding-bottom: 0;
        }
    </style>
@endpush

<x-filament-panels::page @class(['fi-' . str_replace('/', '-', $this->getSlug()) . '-page'])>
    <div x-init="$store.sidebar.close()" x-data="{
        products: [],
        search: '',
        searchResults: $wire.entangle('searchResults'),
        globalDiscount: 0,
        globalDiscountType: 'fixed',
        couponCode: '',
        couponDiscount: 0,
        couponType: 'fixed',
        couponError: '',
        draftName: '',
        showDraftModal: false,
        showCouponModal: false,
    
        addProduct(product) {
            const existingProduct = this.products.find(p => p.id === product.id);
    
            if (existingProduct) {
                existingProduct.quantity++;
            } else {
                this.products.unshift({
                    id: product.id,
                    name: product.name,
                    quantity: 1,
                    price: product.price || 0,
                    discount: 0,
                    discount_type: 'fixed'
                });
            }
    
            this.search = '';
            this.searchResults = [];
            $wire.set('searchResults', []);
            $wire.set('search', '');
        },
    
        clearAll() {
            if (this.products.length && confirm('Are you sure you want to clear all items?')) {
                this.products = [];
                this.globalDiscount = 0;
                this.couponCode = '';
                this.couponDiscount = 0;
            }
        },
    
        saveDraft() {
            if (!this.products.length) {
                alert('Add some products first!');
                return;
            }
            if (!this.draftName.trim()) {
                alert('Please enter a name for the draft');
                return;
            }
            const draft = {
                name: this.draftName,
                products: this.products,
                globalDiscount: this.globalDiscount,
                globalDiscountType: this.globalDiscountType,
                couponCode: this.couponCode,
                couponDiscount: this.couponDiscount,
                couponType: this.couponType,
                timestamp: new Date().toISOString()
            };
            $wire.saveDraft(draft).then(() => {
                this.showDraftModal = false;
                this.draftName = '';
            });
        },
    
        loadDraft(draft) {
            this.products = draft.products;
            this.globalDiscount = draft.globalDiscount;
            this.globalDiscountType = draft.globalDiscountType;
            this.couponCode = draft.couponCode;
            this.couponDiscount = draft.couponDiscount;
            this.couponType = draft.couponType;
        },
    
        applyCoupon() {
            if (!this.couponCode.trim()) {
                this.couponError = 'Please enter a coupon code';
                return;
            }
            $wire.validateCoupon(this.couponCode).then(result => {
                if (result.valid) {
                    this.couponDiscount = result.discount;
                    this.couponType = result.type;
                    this.couponError = '';
                    this.showCouponModal = false;
                } else {
                    this.couponError = result.message;
                }
            });
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
    
        calculateCouponDiscount() {
            const subtotal = this.calculateTotal() - this.calculateGlobalDiscount();
            if (this.couponType === 'fixed') {
                return this.couponDiscount;
            }
            return subtotal * (this.couponDiscount / 100);
        },
    
        calculateFinalTotal() {
            return this.calculateTotal() - this.calculateGlobalDiscount() - this.calculateCouponDiscount();
        }
    }" class="flex flex-col-reverse lg:flex-row">
        <!-- left section -->
        <div class="w-full h-[calc(100vh-5rem)] shadow-lg lg:w-3/5">
            <!-- header -->
            <div class="flex flex-row items-center justify-between px-5 mt-3">
                <div class="text-gray-800">
                    <div class="text-xl font-bold">Simons's BQQ Team</div>
                    <span class="text-xs">Location ID#SIMON123</span>
                </div>
                <div class="flex items-center">
                    <div class="mr-4 text-sm text-center">
                        <div class="font-light text-gray-500">last synced</div>
                        <span class="font-semibold">3 mins ago</span>
                    </div>
                    <div>
                        <span class="px-4 py-2 font-semibold text-gray-800 bg-gray-200 rounded">
                            Help
                    </div>
                    </span>
                </div>
            </div>
            <!-- end header -->
            <!-- Search Input -->
            <div class="px-5 mt-3">
                <input id="search" type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Search products..." @focus="$event.target.select()"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                    autofocus>
            </div>

            <!-- products -->
            <div class="grid grid-cols-1 gap-4 px-5 mt-3 overflow-y-auto sm:grid-cols-2 xl:grid-cols-4 h-[calc(100vh-16rem)]"
                tabindex="-1">
                <template x-for="product in $wire.searchResults">
                    <div @click="addProduct(product); $nextTick(() => { document.getElementById('search').focus() })"
                        class="flex flex-col h-64 bg-white border border-gray-100 rounded-lg shadow-md cursor-pointer">
                        <div class="relative flex mx-1 mt-1 overflow-hidden rounded-xl" href="#">
                            <img class="object-cover w-full h-40" :src="product.image" :alt="product.name" />
                            <span
                                class="absolute top-0 left-0 px-2 m-2 text-sm font-medium text-center text-white bg-black rounded-full">39%
                                OFF</span>
                        </div>
                        <div class="px-3 pb-3 mt-1">
                            <p class="font-semibold tracking-tight text-slate-900 line-clamp-3" :title="product.name"
                                x-text="product.name">
                                </h5>
                            <div class="flex items-center justify-between mt-1">
                                <p>
                                    <span class="text-xl font-bold text-slate-900">$449</span>
                                    <span class="text-sm line-through text-slate-900">$699</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            <!-- end products -->
        </div>
        <!-- end left section -->
        <!-- right section -->
        <div class="w-full lg:w-2/5">
            <!-- header -->
            <div class="flex flex-row items-center justify-between px-5 mt-3">
                <div class="text-xl font-bold">Current Order</div>
                <div class="flex items-center space-x-2">
                    <button @click="showDraftModal = true"
                        class="px-4 py-2 text-gray-800 bg-gray-100 rounded-md hover:bg-gray-200">
                        Save Draft
                    </button>
                    <button @click="showCouponModal = true"
                        class="px-4 py-2 text-blue-600 bg-blue-100 rounded-md hover:bg-blue-200">
                        Apply Coupon
                    </button>
                    <button @click="clearAll()" class="px-4 py-2 text-red-500 bg-red-100 rounded-md hover:bg-red-200">
                        Clear All
                    </button>
                </div>
            </div>
            <!-- end header -->
            <!-- order list -->
            <div class="h-64 px-5 py-4 mt-3 overflow-y-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-xs font-semibold text-gray-500 border-b">
                            <th class="pb-2 text-left">Product</th>
                            <th class="pb-2 text-center">Quantity</th>
                            <th class="pb-2 text-center">Unit Price</th>
                            <th class="pb-2 text-center">Unit Discount</th>
                            <th class="pb-2 text-right">Subtotal</th>
                            <th class="w-10 pb-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(product, index) in products">
                            <tr class="border-b border-gray-100">
                                <td class="py-1">
                                    <span class="text-sm font-semibold line-clamp-3" x-text="product.name"
                                        :title="product.name"></span>
                                </td>
                                <td class="py-1">
                                    <div class="flex items-center justify-center space-x-1">
                                        <input type="number" x-model="product.quantity" min="1"
                                            class="px-1 py-1 text-center border border-gray-300 rounded w-14"
                                            @focus="$event.target.select()">
                                    </div>
                                </td>
                                <td class="py-1">
                                    <div class="flex items-center justify-center space-x-1">
                                        <input type="number" x-model="product.price" min="0"
                                            class="w-24 px-1 py-1 text-center border border-gray-300 rounded"
                                            @focus="$event.target.select()">
                                    </div>
                                </td>
                                <td class="py-1">
                                    <div class="flex items-center justify-center space-x-1">
                                        <input type="number" x-model="product.discount" min="0"
                                            class="w-20 px-1 py-1 text-center border border-gray-300 rounded"
                                            @focus="$event.target.select()">
                                        <button type="button"
                                            @click="product.discount_type = product.discount_type === 'fixed' ? 'percent' : 'fixed'"
                                            class="p-1 bg-gray-100 rounded hover:bg-gray-200">
                                            <x-tabler-currency-taka class="w-5 h-5"
                                                x-show="product.discount_type == 'fixed'" />
                                            <x-tabler-percentage class="w-5 h-5"
                                                x-show="product.discount_type != 'fixed'" />
                                        </button>
                                    </div>
                                </td>
                                <td class="py-1 text-right">
                                    <span x-text="calculateSubtotal(product).toFixed(1)"></span>
                                </td>
                                <td class="py-1 text-right">
                                    <button @click="products.splice(index, 1)" class="text-red-500 hover:text-red-700">
                                        <x-tabler-trash class="w-5 h-5" />
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            <!-- end order list -->
            <!-- totalItems -->
            <div class="px-5 mt-3">
                <div x-data="{ isExpanded: false }" class="shadow-lg ounded-md ">
                    <div x-show="isExpanded" x-collapse class="space-y-1">
                        <div class="flex items-center justify-between px-4">
                            <span class="text-sm font-semibold">Subtotal</span>
                            <span class="font-bold">৳<span x-text="calculateTotal().toFixed(2)"></span></span>
                        </div>
                        <div class="flex items-center justify-between px-4">
                            <span class="text-sm font-semibold">Shipping</span>
                            <div class="relative w-24">
                                <input type="number" x-model="shippingCost" min="0"
                                    class="w-full px-3 py-1 border border-gray-300 rounded ps-8 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-primary-500 focus:border-primary-500"
                                    @focus="$event.target.select()">
                                <div class="absolute inset-y-0 flex items-center pointer-events-none start-0 ps-2">
                                    <x-tabler-currency-taka class="w-4 h-4" />
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between px-4">
                            <span class="text-sm font-semibold">Discount</span>
                            <div class="flex items-center space-x-2">
                                <input type="number" x-model="globalDiscount" min="0"
                                    class="w-20 px-2 py-1 text-right border border-gray-300 rounded"
                                    @focus="$event.target.select()">
                                <button type="button"
                                    @click="globalDiscountType = globalDiscountType === 'fixed' ? 'percent' : 'fixed'"
                                    class="p-1 bg-gray-100 rounded hover:bg-gray-200">
                                    <x-tabler-currency-taka x-show="globalDiscountType == 'fixed'" />
                                    <x-tabler-percentage x-show="globalDiscountType != 'fixed'" />
                                </button>
                                <span class="font-bold text-red-500">-৳<span
                                        x-text="calculateGlobalDiscount().toFixed(2)"></span></span>
                            </div>
                        </div>
                        <template x-if="couponDiscount > 0">
                            <div class="flex items-center justify-between px-4">
                                <span class="text-sm font-semibold">Coupon Discount</span>
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs text-gray-500" x-text="couponCode"></span>
                                    <button @click="couponCode = ''; couponDiscount = 0"
                                        class="text-red-500 hover:text-red-700">
                                        <x-tabler-x class="w-4 h-4" />
                                    </button>
                                    <span class="font-bold text-red-500">-৳<span
                                            x-text="calculateCouponDiscount().toFixed(2)"></span></span>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div class="flex items-center justify-between px-4 py-2 mt-3 border-t-2">
                        <span class="text-2xl font-semibold">Total</span>
                        <button @click="isExpanded = !isExpanded">
                            <x-tabler-arrow-down-dashed class="w-4 h-4 transition-transform"
                                x-bind:class="{ 'rotate-180': isExpanded }" />
                        </button>
                        <span class="text-2xl font-bold">৳<span
                                x-text="calculateFinalTotal().toFixed(2)"></span></span>
                    </div>
                </div>
            </div>
            <!-- end total -->
            <!-- cash -->
            <div class="px-5 mt-3">
                <div class="px-4 py-4 rounded-md shadow-lg">
                    <div class="flex flex-row items-center justify-between">
                        <div class="flex flex-col">
                            <span class="text-xs font-semibold uppercase">cashless credit</span>
                            <span class="text-xl font-bold text-yellow-500">$32.50</span>
                            <span class="text-xs text-gray-400 ">Available</span>
                        </div>
                        <div class="px-4 py-3 font-bold text-gray-800 bg-gray-300 rounded-md"> Cancel</div>
                    </div>
                </div>
            </div>
            <!-- end cash -->
            <!-- button pay-->
            <div class="px-5 my-3">
                <div class="px-4 py-4 font-semibold text-center text-white bg-yellow-500 rounded-md shadow-lg">
                    Pay With Cashless Credit
                </div>
            </div>
            <!-- end button pay -->
        </div>
        <!-- end right section -->

        <!-- Draft Modal -->
        <div x-show="showDraftModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                <div
                    class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="sm:flex sm:items-start">
                        <div class="w-full mt-3 text-center sm:mt-0 sm:text-left">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Save Draft</h3>
                            <div class="mt-2">
                                <input type="text" x-model="draftName" placeholder="Enter draft name"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <button type="button" @click="saveDraft()"
                            class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Save Draft
                        </button>
                        <button type="button" @click="showDraftModal = false"
                            class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Coupon Modal -->
        <div x-show="showCouponModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                <div
                    class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="sm:flex sm:items-start">
                        <div class="w-full mt-3 text-center sm:mt-0 sm:text-left">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Apply Coupon</h3>
                            <div class="mt-2">
                                <input type="text" x-model="couponCode" placeholder="Enter coupon code"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <p x-show="couponError" x-text="couponError" class="mt-1 text-sm text-red-600"></p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <button type="button" @click="applyCoupon()"
                            class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Apply
                        </button>
                        <button type="button" @click="showCouponModal = false"
                            class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
