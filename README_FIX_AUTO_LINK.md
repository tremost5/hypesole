# SHOESTENTIC — Fix Auto-Link Customer Order

Replace:
`app/Http/Controllers/AdminShoeCareOrderController.php`

This update automatically links a new order to an existing Customer account by normalized Indonesian WhatsApp number when the operator does not select a customer from the dropdown.

Recognized formats:
- 08xxxxxxxxxx
- 628xxxxxxxxxx
- +628xxxxxxxxxx

No migration or database reset is required. Existing orders are not modified.

After replacing the file:
```bash
php artisan optimize:clear
```

Create a NEW test order with the same WhatsApp number as the OTP account. The success message should confirm whether the order was linked to a customer account. Older orders are not retroactively linked by this patch.
