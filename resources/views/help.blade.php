<x-layouts.store title="Help & contact — London Threads & Tassels">
    <section class="mx-auto max-w-5xl px-5 py-16 md:px-10">
        <p class="mono-label text-signal-orange" data-motion>We&rsquo;re here to help</p>
        <h1 class="display mt-3 text-3xl md:text-5xl" data-motion>HELP &amp; CONTACT</h1>
        <p class="mt-6 max-w-xl text-sm leading-7 text-ink/70" data-motion>Questions about an order, sizing or a bespoke request? Reach us any of the ways below, or message us instantly on WhatsApp using the button in the corner of the screen.</p>

        <div class="mt-12 grid gap-6 sm:grid-cols-3" data-motion>
            <a href="mailto:hello@londonthreadsandtassels.co.uk" class="surface-card border border-ink/15 p-6">
                <p class="mono-label">Email</p>
                <p class="mt-3 text-sm">hello@londonthreadsandtassels.co.uk</p>
                <p class="mt-1 text-[10px] uppercase tracking-wider text-ink/45">We reply within one business day</p>
            </a>
            <a href="tel:+442012345678" class="surface-card border border-ink/15 p-6">
                <p class="mono-label">Phone &amp; WhatsApp</p>
                <p class="mt-3 text-sm">+44 20 1234 5678</p>
                <p class="mt-1 text-[10px] uppercase tracking-wider text-ink/45">Mon&ndash;Fri, 9am&ndash;6pm GMT</p>
            </a>
            <div class="border border-ink/15 p-6">
                <p class="mono-label">Studio</p>
                <p class="mt-3 text-sm">London, United Kingdom</p>
                <p class="mt-1 text-[10px] uppercase tracking-wider text-ink/45">By appointment only</p>
            </div>
        </div>

        <div id="shipping" class="mt-16 grid gap-10 border-t border-ink/10 pt-12 md:grid-cols-2" data-motion>
            <div>
                <p class="mono-label text-signal-orange">Shipping</p>
                <h2 class="display mt-3 text-xl">DELIVERY &amp; RETURNS</h2>
                <p class="mt-4 text-sm leading-7 text-ink/70">Every order ships complimentary within the UK, dispatched within 2&ndash;3 working days as each suit is packed by hand. If a piece isn&rsquo;t right, you have 14 days from delivery to return it unworn and in its original packaging for a full refund.</p>
            </div>
            <div id="sizing">
                <p class="mono-label text-signal-orange">Fit</p>
                <h2 class="display mt-3 text-xl">SIZE GUIDE</h2>
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="border-y border-ink/15 text-[10px] uppercase tracking-wider">
                            <tr><th class="py-2">Size</th><th>UK</th><th>Bust (in)</th><th>Waist (in)</th></tr>
                        </thead>
                        <tbody class="text-ink/70">
                            <tr class="border-b border-ink/10"><td class="py-2">XS</td><td>6</td><td>32</td><td>25</td></tr>
                            <tr class="border-b border-ink/10"><td class="py-2">S</td><td>8</td><td>34</td><td>27</td></tr>
                            <tr class="border-b border-ink/10"><td class="py-2">M</td><td>10&ndash;12</td><td>36&ndash;38</td><td>29&ndash;31</td></tr>
                            <tr class="border-b border-ink/10"><td class="py-2">L</td><td>14</td><td>40</td><td>33</td></tr>
                            <tr><td class="py-2">XL</td><td>16</td><td>42</td><td>35</td></tr>
                        </tbody>
                    </table>
                    <p class="mt-3 text-[10px] uppercase tracking-wider text-ink/45">Between sizes? We recommend sizing up for the most comfortable fit.</p>
                </div>
            </div>
        </div>

        <div class="mt-16 border-t border-ink/10 pt-12" data-motion>
            <p class="mono-label text-signal-orange">FAQ</p>
            <h2 class="display mt-3 text-xl">FREQUENTLY ASKED</h2>
            <div class="mt-6 divide-y divide-ink/10">
                <details class="py-4">
                    <summary class="cursor-pointer text-sm font-medium">Do you offer alterations?</summary>
                    <p class="mt-3 text-sm leading-7 text-ink/65">Yes — get in touch after ordering and our studio can advise on minor alterations for a considered fit.</p>
                </details>
                <details class="py-4">
                    <summary class="cursor-pointer text-sm font-medium">Can I track my order?</summary>
                    <p class="mt-3 text-sm leading-7 text-ink/65">Sign in and visit <a class="underline" href="{{ route('account') }}">My Account</a> to see the status of every order, from placed through to delivered.</p>
                </details>
                <details class="py-4">
                    <summary class="cursor-pointer text-sm font-medium">Do you ship outside the UK?</summary>
                    <p class="mt-3 text-sm leading-7 text-ink/65">Currently we ship within the UK only. Message us on WhatsApp if you&rsquo;re ordering from abroad and we&rsquo;ll do our best to help.</p>
                </details>
            </div>
        </div>
    </section>
</x-layouts.store>
