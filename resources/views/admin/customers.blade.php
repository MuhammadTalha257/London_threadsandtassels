<x-layouts.admin title="Customers">
    <p class="mono-label text-signal-orange" data-motion>Customer directory</p>
    <h1 class="display mt-3 text-3xl md:text-5xl" data-motion>CUSTOMERS</h1>

    <div class="mt-9 overflow-x-auto" data-motion>
        <table class="w-full text-left text-xs">
            <thead class="border-y border-ink/15 text-[10px] uppercase tracking-wider">
                <tr><th class="py-3">Name</th><th>Email</th><th>Phone</th><th class="text-right">Orders</th></tr>
            </thead>
            <tbody>
                @foreach($customers as $customer)
                    <tr class="border-b border-ink/10">
                        <td class="py-4"><a class="underline" href="{{ route('admin.customers.show', $customer) }}">{{ $customer->name }}</a></td>
                        <td>{{ $customer->email }}</td>
                        <td>{{ $customer->phone ?: '—' }}</td>
                        <td class="text-right">{{ $customer->orders_count }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $customers->links() }}</div>
</x-layouts.admin>
