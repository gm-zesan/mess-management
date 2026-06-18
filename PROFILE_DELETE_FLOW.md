# Profile Delete - User Flow Guide

## Flow Diagram

```
User Clicks "Delete Account"
        ↓
checkEligibility() API Call
        ↓
    Is User a Manager?
   /              \
  NO              YES
  ↓               ↓
Show Delete    Show Transfer
Modal          Modal
  ↓               ↓
Enter        Select New
Password     Managers
  ↓           (for each
Submit     mess they manage)
  ↓               ↓
Delete      proceedToDelete()
User        API Call
  ↓               ↓
Success     Transfer
Redirect    Roles
            ↓
        Show Delete
        Modal
            ↓
        Enter
        Password
            ↓
        Submit
            ↓
        Delete
        User
            ↓
        Success
        Redirect
```

## Regular User (Non-Manager) Flow

### Step 1: Click Delete Button
```
User Profile Page
┌─────────────────────────────┐
│ Delete Account Section      │
│                             │
│ [Delete Account] Button ← Click here
└─────────────────────────────┘
```

### Step 2: Confirmation Modal Appears
```
┌─────────────────────────────────┐
│ Delete Account                  │
│ This action cannot be undone    │
│                                 │
│ Enter your password:            │
│ ┌─────────────────────────────┐ │
│ │ ••••••••••                  │ │
│ └─────────────────────────────┘ │
│                                 │
│ [Cancel]      [Delete Account]  │
└─────────────────────────────────┘
```

### Step 3: Account Deleted
```
Redirect to home page
Status message: "Account deleted successfully"
```

---

## Manager User Flow

### Step 1: Click Delete Button
```
User clicks "Delete Account"
↓
System checks if user is manager
↓
YES - User manages 1 or more messes
```

### Step 2: Transfer Modal Appears
```
┌──────────────────────────────────────────┐
│ Transfer Manager Role                    │
│ Required before account deletion         │
│                                          │
│ Messes requiring transfer:               │
│ • Mess A                                 │
│ • Mess B                                 │
│                                          │
│ Transfer manager for Mess A to:          │
│ ┌──────────────────────────────────────┐ │
│ │ -- Select new manager --             │ │
│ │ John (john@example.com)              │ │
│ │ Jane (jane@example.com)              │ │
│ └──────────────────────────────────────┘ │
│                                          │
│ Transfer manager for Mess B to:          │
│ ┌──────────────────────────────────────┐ │
│ │ -- Select new manager --             │ │
│ │ John (john@example.com)              │ │
│ │ Jane (jane@example.com)              │ │
│ └──────────────────────────────────────┘ │
│                                          │
│ [Cancel]    [Proceed to Delete] ✓        │
│              (enabled after selection)   │
└──────────────────────────────────────────┘
```

### Step 3: Select Managers
```
User selects new manager for EACH mess:
- Mess A → Jane selected
- Mess B → John selected

"Proceed to Delete" button becomes ENABLED
```

### Step 4: Proceed to Delete
```
Click "Proceed to Delete"
↓
Frontend sends to /profile/transfer-manager:
{
  "transfers": [
    { "mess_id": 1, "new_manager_id": 3 },
    { "mess_id": 2, "new_manager_id": 2 }
  ]
}
↓
Backend validates and updates:
- Mess A.manager_id = 3 (Jane)
- Mess B.manager_id = 2 (John)
- Jane gets MANAGER role
- John gets MANAGER role
- Old user gets MEMBER role
↓
Response: { "success": true }
↓
Transfer modal closes
Delete modal appears
```

### Step 5: Delete Confirmation Modal
```
┌─────────────────────────────────┐
│ Delete Account                  │
│ This action cannot be undone    │
│                                 │
│ Enter your password:            │
│ ┌─────────────────────────────┐ │
│ │ ••••••••••                  │ │
│ └─────────────────────────────┘ │
│                                 │
│ [Cancel]      [Delete Account]  │
└─────────────────────────────────┘
```

### Step 6: Account Deleted
```
Backend processes:
1. Validates password
2. Checks deletion is allowed (already transferred)
3. Logs out user
4. Deletes user record
5. Removes user from all messes
6. Removes all user roles
7. Invalidates session

Frontend:
Redirect to home page
Status: "Account deleted successfully"
```

---

## What Happens to Manager After Transfer

### Before Deletion
```
Mess A Manager: Old User
Mess B Manager: Old User

Old User Roles: [MANAGER, MEMBER]
```

### After Clicking "Proceed to Delete"
```
Mess A Manager: Jane (transferred)
Mess B Manager: John (transferred)

Old User Roles: [MEMBER]
Old User Messes: [] (about to be deleted)
```

### After Deletion Confirmation
```
Mess A Manager: Jane
Mess B Manager: John

Old User: DELETED
```

---

## Error Scenarios

### Error: No Eligible Candidates
```
If no other members in the mess:
"Cannot find eligible members to transfer manager role to"
→ User must add members first
→ Then try deleting again
```

### Error: Invalid Selection
```
If selection is missing:
"Proceed to Delete" button stays disabled
User cannot proceed
```

### Error: Transfer Failed
```
If transfer API fails:
Alert: "Error transferring manager roles: [reason]"
Modal stays open
User can retry
```

### Error: Wrong Password
```
If password is incorrect:
Validation error shown
Modal stays open
User can retry
```

---

## Data Changes During Deletion

### For Non-Manager Users:
```
BEFORE:
- User record exists
- Mess users: User has 1+ approved memberships
- Roles: [MEMBER] or [MANAGER]

AFTER:
- User record: DELETED
- Mess users: All records removed
- Roles: All removed
- Audit logs: Deletion logged
```

### For Manager Users:
```
BEFORE:
- User manages: Mess A, Mess B
- User roles: [MANAGER, MEMBER]
- Mess managers: User assigned

AFTER:
- User record: DELETED
- Mess A manager: Jane (transferred)
- Mess B manager: John (transferred)
- Jane roles: [MANAGER, MEMBER]
- John roles: [MANAGER, MEMBER]
- Old user roles: All removed
- Audit logs: Transfers and deletion logged
```

---

## Testing Checklist

- [ ] Non-manager user deletion works
- [ ] Manager user sees transfer modal
- [ ] Can select new managers
- [ ] Cannot proceed without all selections
- [ ] Transfers work correctly
- [ ] New managers get MANAGER role
- [ ] Old user gets MEMBER role
- [ ] Deletion completes successfully
- [ ] User logged out after deletion
- [ ] Session invalidated
- [ ] Redirected to home page

---

**Status**: Ready for User Testing ✅
