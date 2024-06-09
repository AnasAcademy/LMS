 $query = Sale::query()
                ->whereNull('sales.refund_at')
                ->where('access_to_purchased_item', true)
                ->where(function ($query) {
                    $query->whereNotNull('sales.bundle_id')
                        ->whereIn('sales.type', ['bundle', 'installment_payment']);

                })
                ->distinct()
                ->select('sales.bundle_id');

            $combinedSales = deepClone($query)

                ->orderBy('created_at', 'desc')
                ->get();
