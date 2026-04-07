# Toastr.js - Simple Toast Notifications

## Setup Complete ✅

Toastr.js has been installed and configured throughout your system. It's much simpler than Hot Toast and works directly without React.

## How to Use

### 1. **Automatic Session Notifications** (Already Working!)
Laravel session messages are automatically converted to Toastr notifications:

```php
// In your controller
return redirect()->back()->with('success', 'Meal created successfully!');
return redirect()->back()->with('error', 'Something went wrong');
return redirect()->back()->with('warning', 'Please be careful');
return redirect()->back()->with('info', 'Here is some information');
```

### 2. **Manual Notifications in JavaScript**
```javascript
// Success
toastr.success('Operation successful!');

// Error
toastr.error('Operation failed!');

// Warning
toastr.warning('Please be careful!');

// Info
toastr.info('Here is some info');
```

## Toast Types & Colors

| Type | Color | Icon |
|------|-------|------|
| `success()` | Green | ✓ |
| `error()` | Red | ✗ |
| `warning()` | Orange | ⚠️ |
| `info()` | Blue | ℹ️ |

## Configuration

Default settings (all toasts):
- **Duration**: 4 seconds
- **Position**: Top right
- **Progress bar**: Yes
- **Close button**: Yes
- **Animation**: Slide down/up

### Change Position Globally
Edit `resources/js/app.js`:
```javascript
toastr.options.positionClass = 'toast-bottom-right'; // or other positions
```

### Available Positions
- `toast-top-left`
- `toast-top-center`
- `toast-top-right` (default)
- `toast-bottom-left`
- `toast-bottom-center`
- `toast-bottom-right`

### Customize Per Toast
```javascript
toastr.options.timeOut = 2000; // 2 seconds
toastr.success('Quick message!');
```

## Files Modified

- `resources/js/app.js` - Toastr configuration
- `resources/views/layouts/app.blade.php` - Toastr CDN links + session handling
- `resources/views/layouts/guest.blade.php` - Toastr CDN links + session handling
- `vite.config.js` - Removed React plugin
- `package.json` - Toastr installed

## Build Output

```
✓ 61 modules transformed (down from 81)
✓ JS: 169.02 KB (gzip: 61.21 KB) - 40% smaller!
✓ Built successfully
```

## Browser Console Test

Try this in your browser console:
```javascript
toastr.success('This is a success message!');
toastr.error('This is an error message!');
toastr.warning('This is a warning message!');
toastr.info('This is an info message!');
```

## Examples

### Form Submission Success
```php
// Controller
public function store(Request $request) {
    Meal::create($request->validated());
    return redirect()->route('meals.index')
        ->with('success', 'Meal recorded successfully!');
}
```

### Form Validation Error
```php
// Automatically handled - validation errors show as error toasts
```

### AJAX Request
```javascript
axios.post('/api/meals', data)
    .then(() => toastr.success('Saved!'))
    .catch(err => toastr.error(err.response.data.message));
```

### Loading State
```javascript
// Show custom message
toastr.info('Processing...');

// Do something
setTimeout(() => {
    toastr.clear(); // Clear all toasts
    toastr.success('Done!');
}, 3000);
```

## CDN Links Used

- CSS: `https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css`
- JS: `https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js`

## Support

- **Works on**: All modern browsers
- **Size**: ~5KB minified
- **Dependencies**: jQuery (already in your project)
- **Documentation**: https://codeseven.github.io/toastr/

## Ready to Use! 🎉

Test it now by performing any action that redirects with a message. You should see a toast notification appear!
