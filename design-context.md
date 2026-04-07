# 🎨 SaaS Design Context — Mess Management System

## 🧠 Product Overview

This is a SaaS-based Mess Management System used by:

* Students
* Job holder
* Bachelor Mess

The UI must be:

* Clean
* Fast
* Highly readable
* Minimal cognitive load

---

## 🎯 Design Goals

* Reduce clutter
* Improve usability
* Make data easy to scan
* Professional SaaS look (like Stripe / Linear / Notion)

---

## 🌈 Theme Configuration (Tailwind आधारित)

### Primary Color

* `sky-600` (main brand color)

### Color System

* Primary: `sky-600`
* Primary Hover: `sky-700`
* Light Background: `bg-white`
* Secondary Background: `bg-gray-50`
* Border: `border-gray-200`
* Text Primary: `text-gray-800`
* Text Secondary: `text-gray-500`
* Success: `green-600`
* Danger: `red-500`
* Warning: `yellow-500`

---

## 🧱 Layout Rules

### Structure

* Header only (NO sidebar)
* No vertical scroll unless necessary
* Content centered with max width

### Container

```tailwind
max-w-7xl mx-auto
```

---

## 🧩 Component Design Rules

### Cards (Use minimally)

* `bg-white border border-gray-200 rounded-xl shadow-sm`
* Avoid too many nested cards

---

### Buttons

Primary:

```tailwind
bg-sky-600 hover:bg-sky-700 text-white font-bold px-4 py-2 rounded transition-colors
```

Secondary:

```tailwind
bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold px-4 py-2 rounded transition-colors
```
---

### Tables (IMPORTANT for Mess System)

* Use the reusable `<x-datatable>` component
* Features to include:
    - Search (global filter)
    - Pagination (client-side)
    - Sorting on columns
    - Responsive layout
    - Empty state with icon + message
* Columns and actions should be config-driven

---

### Status Badges

* Paid → bg-green-100 text-green-700 text-xs px-2 py-1 rounded
* Due → bg-red-100 text-red-600 text-xs px-2 py-1 rounded

### Forms

* Input:

```tailwind
border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-sky-500 outline-none
```

* Label:

```tailwind
text-sm text-gray-600 mb-1
```

---

## 📊 Data Visualization Style

* Use spacing instead of borders
* Highlight important numbers (bold + bigger text)
* Avoid heavy colors

---

## 🚫 Avoid

* Gradient backgrounds
* Too many colors
* Deep shadows
* Overlapping cards
* Sidebar navigation

---

## ✨ UX Enhancements

* Skeleton loaders
* Empty states
* Toast notifications
* Confirmation modals

---

## 📱 Responsiveness

* Mobile-first
* Stack layout on small screens

---

## 🧬 Design Inspiration

* Stripe Dashboard
* Linear App
* Notion

---

## ⚡ Behavior Rules

* Keep UI minimal
* Prefer whitespace over borders
* Every page must feel fast and lightweight

---

## 🧑‍💻 Instruction for Copilot

When redesigning:

* Follow this design system strictly
* Do NOT add random colors
* Keep layout consistent
* Improve spacing and hierarchy
* Avoid overengineering

Always return:

* Production-ready UI

---