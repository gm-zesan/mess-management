<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Premium Fonts from Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />

        <!-- Vite Assets -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            @keyframes slide-in {
                from {
                    opacity: 0;
                    transform: translateY(-8px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .animate-slide-in {
                animation: slide-in 0.3s ease-out;
            }

            /* Custom gradient for auth image overlay and buttons */
            .gradient-overlay {
                background: linear-gradient(180deg, rgba(15, 23, 42, 0.7) 0%, rgba(15, 23, 42, 0.85) 100%);
            }

            .gradient-blue {
                background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            }

            .gradient-indigo-purple {
                background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            }

            .gradient-pink-rose {
                background: linear-gradient(135deg, #ec4899 0%, #f43f5e 100%);
            }

            .gradient-teal-green {
                background: linear-gradient(135deg, #14b8a6 0%, #10b981 100%);
            }

            /* Divider line */
            .divider-line {
                position: relative;
                display: flex;
                align-items: center;
                text-align: center;
            }

            .divider-line::before {
                content: '';
                position: absolute;
                top: 50%;
                left: 0;
                right: 0;
                height: 1px;
                background: #e5e7eb;
                transform: translateY(-50%);
            }

            .divider-line span {
                position: relative;
                background: #f8fafc;
                padding: 0 12px;
                font-size: 12px;
                color: #6b7280;
                font-weight: 500;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
        </style>
    </head>
    <body class="antialiased">
        <div class="split-layout flex h-screen w-full">
            <!-- Left Panel - Branding -->
            <div class="left-panel relative hidden lg:flex lg:w-3/5 flex-col justify-between overflow-hidden bg-cover bg-center bg-no-repeat p-[60px]" style="background-image: url('/auth.jpeg')">
                <!-- Dark Overlay -->
                <div class="gradient-overlay absolute inset-0 z-0"></div>

                <!-- Logo Container -->
                <div class="logo-container relative z-10 flex items-center gap-4 mb-0">
                    <div class="gradient-blue flex h-[50px] w-[50px] flex-shrink-0 items-center justify-center rounded-xl">
                        <svg class="h-7 w-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                        </svg>
                    </div>
                    <div class="logo-text m-0">
                        <h2 class="m-0 font-[family-name:var(--font-sora)] text-2xl font-bold text-white">MessManager</h2>
                        <p class="m-0 mt-0.5 text-xs font-normal text-blue-300">Meal & Expense Tracking</p>
                    </div>
                </div>

                <!-- Bottom Content -->
                <div class="bottom-content relative z-10">
                    <h3 class="font-[family-name:var(--font-sora)] mb-3 text-4xl font-bold leading-snug text-white">Manage every meal, every expense.</h3>
                    <p class="mb-8 max-w-sm leading-relaxed text-slate-200">From attendance to billing — one platform to run your entire mess operation smoothly.</p>

                    <!-- Features List -->
                    <ul class="features-list list-none m-0 mb-8 flex flex-col gap-3 p-0">
                        <li class="feature-item flex items-center gap-3 text-sm font-normal text-slate-200">
                            <svg class="feature-icon h-5 w-5 flex-shrink-0 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 12a5 5 0 100-10 5 5 0 000 10zM2 18a8 8 0 0116 0H2z"></path>
                            </svg>
                            <span>Member & User Management</span>
                        </li>
                        <li class="feature-item flex items-center gap-3 text-sm font-normal text-slate-200">
                            <svg class="feature-icon h-5 w-5 flex-shrink-0 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Meal & Attendance Tracking</span>
                        </li>
                        <li class="feature-item flex items-center gap-3 text-sm font-normal text-slate-200">
                            <svg class="feature-icon h-5 w-5 flex-shrink-0 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Expense & Deposit Tracking</span>
                        </li>
                        <li class="feature-item flex items-center gap-3 text-sm font-normal text-slate-200">
                            <svg class="feature-icon h-5 w-5 flex-shrink-0 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v2h16V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Monthly Billing & Settlement</span>
                        </li>
                    </ul>

                    <!-- Avatar Stack -->
                    <div class="avatar-stack relative z-10 flex flex-col items-start gap-3" style="margin-top: 32px;">
                        <div class="avatars flex mr-2">
                            <div class="gradient-indigo-purple avatar relative -ml-2 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full border-2 border-slate-900 text-xs font-semibold text-white first:ml-0">SA</div>
                            <div class="gradient-pink-rose avatar relative -ml-2 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full border-2 border-slate-900 text-xs font-semibold text-white first:ml-0">JD</div>
                            <div class="gradient-teal-green avatar relative -ml-2 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full border-2 border-slate-900 text-xs font-semibold text-white first:ml-0">MK</div>
                        </div>
                        <p class="m-0 text-sm font-medium text-slate-200">Trusted by <strong class="text-white">500+ mess managers</strong></p>
                    </div>
                </div>
            </div>

            <!-- Right Panel - Form Side -->
            <div class="right-panel flex w-full flex-col justify-center overflow-y-auto bg-slate-50 px-24 py-[60px] lg:w-2/5 md:px-24 md:py-10">
                <!-- Grid Texture -->
                <svg class="grid-texture pointer-events-none fixed top-0 right-0 z-0 h-full w-full" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000" style="color: rgba(148, 163, 184, 0.02);">
                    <defs>
                        <pattern id="grid-light" width="40" height="40" patternUnits="userSpaceOnUse">
                            <path d="M 40 0 L 0 0 0 40" fill="none" stroke="currentColor" stroke-width="1"/>
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#grid-light)" />
                </svg>

                <!-- Accent Bar -->
                <div class="accent-bar absolute top-0 left-0 z-20 h-1 w-full" style="background: linear-gradient(90deg, #2563eb 0%, transparent 100%);"></div>

                <!-- Form Content -->
                <div class="form-content relative z-10">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
