@extends('layouts.app')
@section('content')
    <div class=" overflow-hidden w-full">
         @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 text-red-800 p-3 rounded mb-4">{{ session('error') }}</div>
        @endif
        <div class="bg-white rounded-xl shadow p-6 mt-4 mb-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center justify-between">
                <span>Coupons</span>
                <a href="{{ route('shopify.import-coupons') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm">Import Coupons</a>
                <a href="{{ route('shopify.create-coupon') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm">Create Coupon</a>
            </h3>
            <div class="overflow-x-auto bg-white shadow rounded-lg">
                <table class="min-w-full border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr class="text-left text-sm text-gray-700">
                            <th class="px-4 py-3 border">Code</th>
                            <th class="px-4 py-3 border">Title</th>
                            <th class="px-4 py-3 border">Type</th>
                            <th class="px-4 py-3 border">Value</th>
                            <th class="px-4 py-3 border">Usage</th>
                            <th class="px-4 py-3 border">Validity</th>
                            <th class="px-4 py-3 border">Status</th>
                            <th class="px-4 py-3 border">Delete</th>
                        </tr>
                    </thead>

                    <tbody class="text-sm">
                        @forelse ($coupons as $coupon)

                            @php
                                // 🔥 Explicitly using determineStatus() in the view
                                $status = \App\Models\Coupon::determineStatus(
                                    $coupon->raw_price_rule ?? []
                                );
                            @endphp

                            <tr class="border-t">
                                <td class="px-4 py-2 font-medium">
                                    {{ $coupon->code }}
                                </td>

                                <td class="px-4 py-2">
                                    {{ $coupon->title ?? '-' }}
                                </td>

                                <td class="px-4 py-2 capitalize">
                                    {{ str_replace('_', ' ', $coupon->value_type) }}
                                </td>

                                <td class="px-4 py-2">
                                    @if ($coupon->value_type === 'percentage')
                                        {{ abs($coupon->value) }}%
                                    @else
                                        ₹{{ number_format(abs($coupon->value), 2) }}
                                    @endif
                                </td>

                                <td class="px-4 py-2">
                                    {{ $coupon->times_used }}
                                    @if($coupon->usage_limit)
                                        / {{ $coupon->usage_limit }}
                                    @endif
                                </td>

                                <td class="px-4 py-2 text-xs text-gray-600">
                                    <div>
                                        Start:
                                        {{ $coupon->starts_at?->format('d M Y') ?? '—' }}
                                    </div>
                                    <div>
                                        End:
                                        {{ $coupon->ends_at?->format('d M Y') ?? '—' }}
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="px-4 py-2">
                                    @if ($status === 'active')
                                        <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded">
                                            Active
                                        </span>
                                    @elseif ($status === 'expired')
                                        <span class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded">
                                            Expired
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded">
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td>
                                   
                                        <form action="{{ route('coupons.delete', $coupon) }}" method="POST"
                                            onsubmit="return confirm('Delete this coupon permanently?')">
                                            @csrf
                                            @method('DELETE')

                                            <button class="text-red-600 hover:text-red-800 text-sm cursor-pointer">
                                                Delete
                                            </button>
                                        </form>
                                    


                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-6 text-gray-500">
                                    No coupons found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
