<div class="relative">
    {{-- Hero Section --}}
    <div class="relative isolate overflow-hidden bg-gradient-to-b from-indigo-50/50 via-white to-white">
        <div class="absolute inset-x-0 -top-40 -z-20 transform-gpu overflow-hidden blur-3xl sm:-top-80"
            aria-hidden="true">
            <div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-indigo-600 to-indigo-400 opacity-20 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]"
                style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)">
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="relative z-30 mx-auto flex max-w-7xl items-center justify-between p-6 lg:px-8" aria-label="Global">
            <div class="flex lg:flex-1">
                <a href="/" wire:navigate class="-m-1.5 p-1.5">
                    <span class="sr-only">TruckDispatch</span>
                    <div class="flex items-center gap-2">
                        <div class="h-8 w-8 rounded-lg bg-indigo-600 flex items-center justify-center">
                            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 18L12 15L8 12M16 18L12 15M12 3L4 9L12 15L20 9L12 3Z" />
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-gray-900">TruckDispatch</span>
                    </div>
                </a>
            </div>
            <div class="flex gap-x-6">
                <a href="#features"
                    class="relative z-30 text-sm font-semibold leading-6 text-gray-900 hover:text-indigo-600 transition-colors">Features</a>
                <a href="#pricing"
                    class="relative z-30 text-sm font-semibold leading-6 text-gray-900 hover:text-indigo-600 transition-colors">Pricing</a>
                <a href="#contact"
                    class="relative z-30 text-sm font-semibold leading-6 text-gray-900 hover:text-indigo-600 transition-colors">Contact</a>
            </div>
            <div class="flex flex-1 justify-end gap-x-4">
                <a href="{{ route('register') }}" wire:navigate
                    class="relative z-30 rounded-md border-2 border-indigo-600 px-4 py-2 text-sm font-semibold text-indigo-600 hover:bg-indigo-600 hover:text-white transition-all duration-300">
                    {{ __('Get Started') }}
                </a>
            </div>
        </nav>

        {{-- Hero Content --}}
        <div class="relative z-10 mx-auto max-w-7xl px-6 py-12 sm:py-24 lg:flex lg:items-center lg:gap-x-10 lg:px-8">
            <div class="mx-auto max-w-2xl lg:mx-0 lg:flex-auto">
                <div class="flex">
                    <div
                        class="relative rounded-full px-3 py-1 text-sm leading-6 text-indigo-600 ring-1 ring-gray-900/10 hover:ring-gray-900/20">
                        Now available. Start dispatching smarter.
                    </div>
                </div>
                <h1 class="mt-8 text-4xl font-bold tracking-tight text-gray-900 sm:text-6xl">Modern dispatch for <span
                        class="text-indigo-600">modern fleets</span></h1>
                <p class="mt-6 text-lg leading-8 text-gray-600">Streamline your entire trucking operation with real-time
                    tracking, automated dispatch, and intelligent route optimization. Used by over 500+ logistics
                    companies.</p>
                <div class="mt-10 flex items-center gap-x-6">
                    <a href="{{ route('register') }}" wire:navigate
                        class="relative z-20 rounded-md border-2 border-indigo-600 px-6 py-3 text-sm font-semibold text-indigo-600 hover:bg-indigo-600 hover:text-white transition-all duration-300">
                        Start free trial →
                    </a>
                    <a href="#demo"
                        class="relative z-20 text-sm font-semibold leading-6 text-gray-900 hover:text-indigo-600 transition-colors">Watch
                        demo <span aria-hidden="true">→</span></a>
                </div>
                <div class="mt-8 flex gap-x-8 border-t border-gray-100 pt-8">
                    <div class="flex gap-x-4">
                        <div class="text-2xl font-bold text-gray-900">500+</div>
                        <div class="text-sm text-gray-500">Active<br>Fleets</div>
                    </div>
                    <div class="flex gap-x-4">
                        <div class="text-2xl font-bold text-gray-900">98%</div>
                        <div class="text-sm text-gray-500">On-time<br>Delivery</div>
                    </div>
                    <div class="flex gap-x-4">
                        <div class="text-2xl font-bold text-gray-900">24/7</div>
                        <div class="text-sm text-gray-500">Support<br>Coverage</div>
                    </div>
                </div>
            </div>
            <div class="relative z-10 mt-16 sm:mt-24 lg:mt-0 lg:flex-shrink-0 lg:flex-grow-0">
                <div class="relative mx-auto w-full max-w-md lg:w-[480px]">
                    <div
                        class="absolute -inset-4 rounded-2xl bg-gradient-to-r from-indigo-500/20 to-indigo-300/20 blur-2xl">
                    </div>
                    <div class="relative rounded-2xl bg-white shadow-2xl ring-1 ring-gray-200/50 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=800&q=80"
                            alt="Truck dispatch dashboard" class="w-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-gray-900/10 to-transparent"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Features Section --}}
    <div id="features" class="relative z-10 mx-auto max-w-7xl px-6 py-24 sm:py-32 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <h2 class="text-base font-semibold leading-7 text-indigo-600">Everything you need</h2>
            <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Powerful dispatch features</p>
            <p class="mt-6 text-lg leading-8 text-gray-600">Built specifically for trucking and logistics companies to
                optimize every aspect of their operations.</p>
        </div>
        <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-none">
            <dl class="grid max-w-xl grid-cols-1 gap-x-8 gap-y-16 lg:max-w-none lg:grid-cols-3">
                <div class="flex flex-col">
                    <dt class="text-base font-semibold leading-7 text-gray-900">
                        <div class="mb-6 flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-600">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        Real-time GPS Tracking
                    </dt>
                    <dd class="mt-1 flex flex-auto flex-col text-base leading-7 text-gray-600">
                        <p class="flex-auto">Track your entire fleet in real-time with live location updates, geofencing
                            alerts, and predictive ETA calculations.</p>
                    </dd>
                </div>
                <div class="flex flex-col">
                    <dt class="text-base font-semibold leading-7 text-gray-900">
                        <div class="mb-6 flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-600">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        Intelligent Dispatching
                    </dt>
                    <dd class="mt-1 flex flex-auto flex-col text-base leading-7 text-gray-600">
                        <p class="flex-auto">AI-powered load matching and automated dispatch workflows reduce manual
                            work and maximize fleet utilization.</p>
                    </dd>
                </div>
                <div class="flex flex-col">
                    <dt class="text-base font-semibold leading-7 text-gray-900">
                        <div class="mb-6 flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-600">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        Analytics & Reporting
                    </dt>
                    <dd class="mt-1 flex flex-auto flex-col text-base leading-7 text-gray-600">
                        <p class="flex-auto">Comprehensive dashboards and custom reports to track KPIs, driver
                            performance, and cost efficiency across your fleet.</p>
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    {{-- CTA Section --}}
    <div id="pricing" class="relative isolate mt-8">
        <div class="relative z-10 mx-auto max-w-7xl px-6 py-24 sm:py-32 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Ready to transform your
                    dispatch?<br>Start your free trial today.</h2>
                <p class="mx-auto mt-6 max-w-xl text-lg leading-8 text-gray-600">Join thousands of fleet managers who
                    have reduced empty miles by 25% and increased on-time deliveries to 98%.</p>
                <div class="mt-10 flex items-center justify-center gap-x-6">
                    <a href="{{ route('register') }}" wire:navigate
                        class="relative z-20 rounded-md border-2 border-indigo-600 px-6 py-3 text-sm font-semibold text-indigo-600 hover:bg-indigo-600 hover:text-white transition-all duration-300">
                        Get started →
                    </a>
                    <a href="#features"
                        class="relative z-20 text-sm font-semibold leading-6 text-gray-900 hover:text-indigo-600 transition-colors">Learn
                        more <span aria-hidden="true">→</span></a>
                </div>
            </div>
        </div>
        <div class="absolute inset-x-0 -top-16 -z-20 flex transform-gpu justify-center overflow-hidden blur-3xl"
            aria-hidden="true">
            <div class="aspect-[1318/752] w-[82.375rem] flex-none bg-gradient-to-r from-indigo-500/50 to-indigo-300/50 opacity-25"
                style="clip-path: polygon(73.6% 51.7%, 91.7% 11.8%, 100% 46.4%, 97.4% 82.2%, 92.5% 84.9%, 75.7% 64%, 55.3% 47.5%, 46.5% 49.4%, 45% 62.9%, 50.3% 87.2%, 21.3% 64.1%, 0.1% 100%, 5.4% 51.1%, 21.4% 63.9%, 58.9% 0.2%, 73.6% 51.7%)">
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <footer id="contact" class="relative z-10 mt-20 border-t border-gray-100 bg-white">
        <div class="mx-auto max-w-7xl px-6 py-12 md:flex md:items-center md:justify-between lg:px-8">
            <div class="flex justify-center space-x-6 md:order-2">
                <a href="#" class="text-gray-400 hover:text-gray-500 transition-colors">
                    <span class="sr-only">Twitter</span>
                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path
                            d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                    </svg>
                </a>
                <a href="#" class="text-gray-400 hover:text-gray-500 transition-colors">
                    <span class="sr-only">LinkedIn</span>
                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path
                            d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
                    </svg>
                </a>
            </div>
            <div class="mt-8 md:order-1 md:mt-0">
                <p class="text-center text-xs leading-5 text-gray-500">&copy; 2025 TruckDispatchPro, Inc. All rights
                    reserved.</p>
            </div>
        </div>
    </footer>
</div>