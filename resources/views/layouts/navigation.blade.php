<div class="w-[350px] py-10 px-3 ">
    <ul>
        <li class="mb-2"><a class="flex p-4  items-center bg-black text-white rounded-md cursor-pointer gap-3" href="{{ route('shopify.products') }}"><i class="bi bi-box2 text-white"></i>Shopify Products</a></li>
        <li class="mb-2"><a class="flex p-4 gap-3 items-center bg-black text-white rounded-md cursor-pointer" href="{{ route('shopify.db-products') }}"><i class="bi bi-box2 text-white"></i>Local Products</a></li>
        <li class="mb-2"><a class="flex p-4 gap-3 items-center bg-black text-white rounded-md cursor-pointer" href="{{ route('shopify.orders') }}"><i class="bi bi-bag-dash text-white"></i></i>Orders</a></li>
        <li class="mb-2"><a class="flex p-4 gap-3 items-center bg-black text-white rounded-md cursor-pointer" href="{{ route('shopify.customers') }}"><i class="bi bi-bag-dash text-white"></i></i>Customers</a></li>
        <li class="mb-2"><a class="flex p-4 gap-3 items-center bg-black text-white rounded-md cursor-pointer" href="{{ route('shopify.all-coupons') }}"><i class="bi bi-ticket-perforated-fill text-white"></i></i>Coupons</a></li>
        <li class="mb-2"><a class="flex p-4 gap-3 items-center bg-red-800 text-white rounded-md cursor-pointer" href="{{ route('shopify.import') }}"><i class="bi bi-box2 text-white"></i>Import Products</a></li>
        <li class="mb-2"><a class="flex p-4 gap-3 items-center bg-red-900 text-white rounded-md cursor-pointer" href="{{ route('shopify.import.orders') }}"><i class="bi bi-box2 text-white"></i>Import Orders</a></li>
        <li class="mb-2"><a class="flex p-4 gap-3 items-center bg-purple-900 text-white rounded-md cursor-pointer" href="{{ route('shopify.import.customers') }}"><i class="bi bi-box2 text-white"></i>Import Customers</a></li>
    </ul>
   
</div>