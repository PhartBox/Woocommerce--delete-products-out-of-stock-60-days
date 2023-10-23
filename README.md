# Woocommerce
Woocommerce plugin that delete products and it's images that are not in stock and not purchased in the last 60 days.

Upload&Activate. No additional settings needed. Should run only when admin.

*If you need different timeframe then search for this line if ($last_order_date && (current_time('timestamp') - strtotime($last_order_date)) > 60 * DAY_IN_SECONDS) {
and change number 60 to something else. Number represents days.

Don't forget. Always backup database and test before going live!

Good luck.
