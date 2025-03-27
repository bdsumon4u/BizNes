<x-filament-panels::page @class([
    'fi-resource-create-record-page',
    'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),
])>
    <x-filament-panels::form id="form" :wire:key="$this->getId() . '.forms.' . $this->getFormStatePath()"
        wire:submit="create">
        <!-- component -->
        <div class="flex flex-col-reverse lg:flex-row">
            <!-- left section -->
            <div class="w-full min-h-screen shadow-lg lg:w-3/5">
                <!-- header -->
                <div class="flex flex-row items-center justify-between px-5 mt-5">
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
                <!-- categories -->
                <div class="flex flex-row px-5 mt-5">
                    <span class="px-5 py-1 mr-4 text-sm text-white bg-yellow-500 rounded-2xl">
                        All items
                    </span>
                    <span class="px-5 py-1 mr-4 text-sm font-semibold rounded-2xl">
                        Food
                    </span>
                    <span class="px-5 py-1 mr-4 text-sm font-semibold rounded-2xl">
                        Cold Drinks
                    </span>
                    <span class="px-5 py-1 mr-4 text-sm font-semibold rounded-2xl">
                        Hot Drinks
                    </span>
                </div>
                <!-- end categories -->
                <!-- products -->
                <div class="grid grid-cols-3 gap-4 px-5 mt-5 overflow-y-auto h-3/4">
                    <div class="flex flex-col justify-between h-32 px-3 py-3 border border-gray-200 rounded-md">
                        <div>
                            <div class="font-bold text-gray-800">Griled corn</div>
                            <span class="text-sm font-light text-gray-400">150g</span>
                        </div>
                        <div class="flex flex-row items-center justify-between">
                            <span class="self-end text-lg font-bold text-yellow-500">$1.75</span>
                            <img src="https://source.unsplash.com/sc5sTPMrVfk/600x600"
                                class="object-cover rounded-md h-14 w-14" alt="">
                        </div>
                    </div>
                    <div class="flex flex-col justify-between h-32 px-3 py-3 border border-gray-200 rounded-md">
                        <div>
                            <div class="font-bold text-gray-800">Ranch Burger</div>
                            <span class="text-sm font-light text-gray-400">150g</span>
                        </div>
                        <div class="flex flex-row items-center justify-between">
                            <span class="self-end text-lg font-bold text-yellow-500">$7.00</span>
                            <img src="https://source.unsplash.com/sc5sTPMrVfk/600x500"
                                class="object-cover rounded-md h-14 w-14" alt="">
                        </div>
                    </div>
                    <div class="flex flex-col justify-between h-32 px-3 py-3 border border-gray-200 rounded-md">
                        <div>
                            <div class="font-bold text-gray-800">Pizza Bacon</div>
                            <span class="text-sm font-light text-gray-400">150g</span>
                        </div>
                        <div class="flex flex-row items-center justify-between">
                            <span class="self-end text-lg font-bold text-yellow-500">$1.75</span>
                            <img src="https://source.unsplash.com/sc5sTPMrVfk/500x500"
                                class="object-cover rounded-md h-14 w-14" alt="">
                        </div>
                    </div>
                    <div class="flex flex-col justify-between h-32 px-3 py-3 border border-gray-200 rounded-md">
                        <div>
                            <div class="font-bold text-gray-800">Griled corn</div>
                            <span class="text-sm font-light text-gray-400">150g</span>
                        </div>
                        <div class="flex flex-row items-center justify-between">
                            <span class="self-end text-lg font-bold text-yellow-500">$1.75</span>
                            <img src="https://source.unsplash.com/MNtag_eXMKw/600x600"
                                class="object-cover rounded-md h-14 w-14" alt="">
                        </div>
                    </div>
                    <div class="flex flex-col justify-between h-32 px-3 py-3 border border-gray-200 rounded-md">
                        <div>
                            <div class="font-bold text-gray-800">Griled corn</div>
                            <span class="text-sm font-light text-gray-400">150g</span>
                        </div>
                        <div class="flex flex-row items-center justify-between">
                            <span class="self-end text-lg font-bold text-yellow-500">$1.75</span>
                            <img src="https://source.unsplash.com/MNtag_eXMKw/600x600"
                                class="object-cover rounded-md h-14 w-14" alt="">
                        </div>
                    </div>
                    <div class="flex flex-col justify-between h-32 px-3 py-3 border border-gray-200 rounded-md">
                        <div>
                            <div class="font-bold text-gray-800">Griled corn</div>
                            <span class="text-sm font-light text-gray-400">150g</span>
                        </div>
                        <div class="flex flex-row items-center justify-between">
                            <span class="self-end text-lg font-bold text-yellow-500">$1.75</span>
                            <img src="https://source.unsplash.com/MNtag_eXMKw/600x600"
                                class="object-cover rounded-md h-14 w-14" alt="">
                        </div>
                    </div>
                    <div class="flex flex-col justify-between h-32 px-3 py-3 border border-gray-200 rounded-md">
                        <div>
                            <div class="font-bold text-gray-800">Griled corn</div>
                            <span class="text-sm font-light text-gray-400">150g</span>
                        </div>
                        <div class="flex flex-row items-center justify-between">
                            <span class="self-end text-lg font-bold text-yellow-500">$1.75</span>
                            <img src="https://source.unsplash.com/MNtag_eXMKw/600x600"
                                class="object-cover rounded-md h-14 w-14" alt="">
                        </div>
                    </div>
                    <div class="flex flex-col justify-between h-32 px-3 py-3 border border-gray-200 rounded-md">
                        <div>
                            <div class="font-bold text-gray-800">Griled corn</div>
                            <span class="text-sm font-light text-gray-400">150g</span>
                        </div>
                        <div class="flex flex-row items-center justify-between">
                            <span class="self-end text-lg font-bold text-yellow-500">$1.75</span>
                            <img src="https://source.unsplash.com/MNtag_eXMKw/600x600"
                                class="object-cover rounded-md h-14 w-14" alt="">
                        </div>
                    </div>
                    <div class="flex flex-col justify-between h-32 px-3 py-3 border border-gray-200 rounded-md">
                        <div>
                            <div class="font-bold text-gray-800">Griled corn</div>
                            <span class="text-sm font-light text-gray-400">150g</span>
                        </div>
                        <div class="flex flex-row items-center justify-between">
                            <span class="self-end text-lg font-bold text-yellow-500">$1.75</span>
                            <img src="https://source.unsplash.com/MNtag_eXMKw/600x600"
                                class="object-cover rounded-md h-14 w-14" alt="">
                        </div>
                    </div>
                    <div class="flex flex-col justify-between h-32 px-3 py-3 border border-gray-200 rounded-md">
                        <div>
                            <div class="font-bold text-gray-800">Griled corn</div>
                            <span class="text-sm font-light text-gray-400">150g</span>
                        </div>
                        <div class="flex flex-row items-center justify-between">
                            <span class="self-end text-lg font-bold text-yellow-500">$1.75</span>
                            <img src="https://source.unsplash.com/MNtag_eXMKw/600x600"
                                class="object-cover rounded-md h-14 w-14" alt="">
                        </div>
                    </div>
                    <div class="flex flex-col justify-between h-32 px-3 py-3 border border-gray-200 rounded-md">
                        <div>
                            <div class="font-bold text-gray-800">Griled corn</div>
                            <span class="text-sm font-light text-gray-400">150g</span>
                        </div>
                        <div class="flex flex-row items-center justify-between">
                            <span class="self-end text-lg font-bold text-yellow-500">$1.75</span>
                            <img src="https://source.unsplash.com/MNtag_eXMKw/600x600"
                                class="object-cover rounded-md h-14 w-14" alt="">
                        </div>
                    </div>
                </div>
                <!-- end products -->
            </div>
            <!-- end left section -->
            <!-- right section -->
            <div class="w-full lg:w-2/5">
                <!-- header -->
                <div class="flex flex-row items-center justify-between px-5 mt-5">
                    <div class="text-xl font-bold">Current Order</div>
                    <div class="font-semibold">
                        <span class="px-4 py-2 text-red-500 bg-red-100 rounded-md">Clear All</span>
                        <span class="px-4 py-2 text-gray-800 bg-gray-100 rounded-md">Setting</span>
                    </div>
                </div>
                <!-- end header -->
                <!-- order list -->
                <div class="h-64 px-5 py-4 mt-5 overflow-y-auto">
                    <div class="flex flex-row items-center justify-between mb-4">
                        <div class="flex flex-row items-center w-2/5">
                            <img src="https://source.unsplash.com/4u_nRgiLW3M/600x600"
                                class="object-cover w-10 h-10 rounded-md" alt="">
                            <span class="ml-4 text-sm font-semibold">Stuffed flank steak</span>
                        </div>
                        <div class="flex justify-between w-32">
                            <span class="px-3 py-1 bg-gray-300 rounded-md ">-</span>
                            <span class="mx-4 font-semibold">2</span>
                            <span class="px-3 py-1 bg-gray-300 rounded-md ">+</span>
                        </div>
                        <div class="w-16 text-lg font-semibold text-center">
                            $13.50
                        </div>
                    </div>
                    <div class="flex flex-row items-center justify-between mb-4">
                        <div class="flex flex-row items-center w-2/5">
                            <img src="https://source.unsplash.com/sc5sTPMrVfk/600x600"
                                class="object-cover w-10 h-10 rounded-md" alt="">
                            <span class="ml-4 text-sm font-semibold">Grilled Corn</span>
                        </div>
                        <div class="flex justify-between w-32">
                            <span class="px-3 py-1 bg-gray-300 rounded-md ">-</span>
                            <span class="mx-4 font-semibold">10</span>
                            <span class="px-3 py-1 bg-gray-300 rounded-md ">+</span>
                        </div>
                        <div class="w-16 text-lg font-semibold text-center">
                            $3.50
                        </div>
                    </div>
                    <div class="flex flex-row items-center justify-between mb-4">
                        <div class="flex flex-row items-center w-2/5">
                            <img src="https://source.unsplash.com/MNtag_eXMKw/600x600"
                                class="object-cover w-10 h-10 rounded-md" alt="">
                            <span class="ml-4 text-sm font-semibold">Grilled Corn</span>
                        </div>
                        <div class="flex justify-between w-32">
                            <span class="px-3 py-1 bg-gray-300 rounded-md ">-</span>
                            <span class="mx-4 font-semibold">10</span>
                            <span class="px-3 py-1 bg-gray-300 rounded-md ">+</span>
                        </div>
                        <div class="w-16 text-lg font-semibold text-center">
                            $3.50
                        </div>
                    </div>
                    <div class="flex flex-row items-center justify-between mb-4">
                        <div class="flex flex-row items-center w-2/5">
                            <img src="https://source.unsplash.com/MNtag_eXMKw/600x600"
                                class="object-cover w-10 h-10 rounded-md" alt="">
                            <span class="ml-4 text-sm font-semibold">Grilled Corn</span>
                        </div>
                        <div class="flex justify-between w-32">
                            <span class="px-3 py-1 bg-gray-300 rounded-md ">-</span>
                            <span class="mx-4 font-semibold">10</span>
                            <span class="px-3 py-1 bg-gray-300 rounded-md ">+</span>
                        </div>
                        <div class="w-16 text-lg font-semibold text-center">
                            $3.50
                        </div>
                    </div>
                    <div class="flex flex-row items-center justify-between mb-4">
                        <div class="flex flex-row items-center w-2/5">
                            <img src="https://source.unsplash.com/MNtag_eXMKw/600x600"
                                class="object-cover w-10 h-10 rounded-md" alt="">
                            <span class="ml-4 text-sm font-semibold">Ranch Burger</span>
                        </div>
                        <div class="flex justify-between w-32">
                            <span class="px-3 py-1 text-white bg-red-300 rounded-md">x</span>
                            <span class="mx-4 font-semibold">1</span>
                            <span class="px-3 py-1 bg-gray-300 rounded-md ">+</span>
                        </div>
                        <div class="w-16 text-lg font-semibold text-center">
                            $2.50
                        </div>
                    </div>
                    <div class="flex flex-row items-center justify-between mb-4">
                        <div class="flex flex-row items-center w-2/5">
                            <img src="https://source.unsplash.com/4u_nRgiLW3M/600x600"
                                class="object-cover w-10 h-10 rounded-md" alt="">
                            <span class="ml-4 text-sm font-semibold">Ranch Burger</span>
                        </div>
                        <div class="flex justify-between w-32">
                            <span class="px-3 py-1 text-white bg-red-300 rounded-md">x</span>
                            <span class="mx-4 font-semibold">1</span>
                            <span class="px-3 py-1 bg-gray-300 rounded-md ">+</span>
                        </div>
                        <div class="w-16 text-lg font-semibold text-center">
                            $2.50
                        </div>
                    </div>
                </div>
                <!-- end order list -->
                <!-- totalItems -->
                <div class="px-5 mt-5">
                    <div class="py-4 rounded-md shadow-lg">
                        <div class="flex justify-between px-4 ">
                            <span class="text-sm font-semibold">Subtotal</span>
                            <span class="font-bold">$35.25</span>
                        </div>
                        <div class="flex justify-between px-4 ">
                            <span class="text-sm font-semibold">Discount</span>
                            <span class="font-bold">- $5.00</span>
                        </div>
                        <div class="flex justify-between px-4 ">
                            <span class="text-sm font-semibold">Sales Tax</span>
                            <span class="font-bold">$2.25</span>
                        </div>
                        <div class="flex items-center justify-between px-4 py-2 mt-3 border-t-2">
                            <span class="text-2xl font-semibold">Total</span>
                            <span class="text-2xl font-bold">$37.50</span>
                        </div>
                    </div>
                </div>
                <!-- end total -->
                <!-- cash -->
                <div class="px-5 mt-5">
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
                <div class="px-5 mt-5">
                    <div class="px-4 py-4 font-semibold text-center text-white bg-yellow-500 rounded-md shadow-lg">
                        Pay With Cashless Credit
                    </div>
                </div>
                <!-- end button pay -->
            </div>
            <!-- end right section -->
        </div>

        <x-filament-panels::form.actions :actions="$this->getCachedFormActions()" :full-width="$this->hasFullWidthFormActions()" />
    </x-filament-panels::form>

    <x-filament-panels::page.unsaved-data-changes-alert />
</x-filament-panels::page>
