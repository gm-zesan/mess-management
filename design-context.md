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

* Clean, spaced rows
* Hover effect: `hover:bg-gray-50`
* Header:

```tailwind
text-xs uppercase text-gray-500 bg-gray-50
```

---

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












Use the design system from #design-context.md

Redesign this page:
[PASTE YOUR CODE HERE]

Requirements:
- Use Tailwind CSS only
- Apply sky-600 theme
- Light theme only
- Improve spacing, hierarchy, and readability
- Make it look like a modern SaaS dashboard
- Clean spacing and section-based layout
- Use minimal cards (avoid overuse)
- Keep UI minimal and professional
- Make it look like Stripe/Linear dashboard

UI:
- Clean layout
- Proper spacing
- sky-600 theme
- Light SaaS style

Avoid:
- clutter
- heavy borders

Improve:
- spacing
- hierarchy
- readability
- alignment

Output:
- Only updated code
- No explanation